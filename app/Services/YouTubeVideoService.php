<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * The latest uploads from the NativePHP YouTube channel, via the YouTube Data
 * API (the channel RSS feed at youtube.com/feeds now 404s). Served to the Jump
 * app's Videos tab through /api/videos.
 *
 * Results are cached for an hour. The last successful list is kept
 * indefinitely so a YouTube outage or quota hiccup serves slightly stale
 * videos instead of an empty page.
 */
class YouTubeVideoService
{
    public const CACHE_KEY = 'youtube.videos';

    public const LAST_GOOD_CACHE_KEY = 'youtube.videos.last_good';

    private const ENDPOINT = 'https://www.googleapis.com/youtube/v3/playlistItems';

    /**
     * @return list<array{id: string, title: string, description: string, published_at: ?string, thumbnail: string, url: string}>
     */
    public function latest(): array
    {
        if ($cached = Cache::get(self::CACHE_KEY)) {
            return $cached;
        }

        $videos = $this->fetch();

        if ($videos === null) {
            return Cache::get(self::LAST_GOOD_CACHE_KEY, []);
        }

        Cache::put(self::CACHE_KEY, $videos, now()->addHour());
        Cache::forever(self::LAST_GOOD_CACHE_KEY, $videos);

        return $videos;
    }

    /**
     * Null when the API isn't configured or the request fails, so callers can
     * tell "no videos" apart from "couldn't ask".
     *
     * @return list<array{id: string, title: string, description: string, published_at: ?string, thumbnail: string, url: string}>|null
     */
    protected function fetch(): ?array
    {
        $key = config('services.youtube.api_key');
        $channelId = config('services.youtube.channel_id');

        if (! $key || ! $channelId) {
            return null;
        }

        $response = Http::timeout(10)->get(self::ENDPOINT, [
            'part' => 'snippet,contentDetails',
            // Every channel's uploads playlist is its id with UC swapped for UU.
            'playlistId' => 'UU'.substr($channelId, 2),
            'maxResults' => 25,
            'key' => $key,
        ]);

        if ($response->failed()) {
            Log::warning('YouTube videos fetch failed', ['status' => $response->status()]);

            return null;
        }

        return collect($response->json('items', []))
            // Private and deleted uploads stay in the playlist without a
            // publish date; they have nothing to watch.
            ->filter(fn (array $item) => filled($item['contentDetails']['videoPublishedAt'] ?? null))
            ->map(function (array $item) {
                $id = $item['contentDetails']['videoId'];
                $snippet = $item['snippet'];
                $thumbnails = $snippet['thumbnails'] ?? [];

                return [
                    'id' => $id,
                    'title' => $snippet['title'] ?? '',
                    'description' => $snippet['description'] ?? '',
                    'published_at' => $item['contentDetails']['videoPublishedAt'],
                    'thumbnail' => $thumbnails['high']['url']
                        ?? $thumbnails['medium']['url']
                        ?? "https://i.ytimg.com/vi/{$id}/hqdefault.jpg",
                    'url' => "https://www.youtube.com/watch?v={$id}",
                ];
            })
            ->values()
            ->all();
    }
}
