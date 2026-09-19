<?php

namespace Tests\Feature\Api;

use App\Services\YouTubeVideoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VideosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.youtube.api_key' => 'test-key',
            'services.youtube.channel_id' => 'UCbkAE6vLlR6lOy_nxd--22g',
        ]);

        Cache::flush();
    }

    public function test_it_returns_the_latest_uploads(): void
    {
        Http::fake([
            'www.googleapis.com/youtube/v3/playlistItems*' => Http::response($this->playlistResponse()),
        ]);

        $this->getJson('/api/videos')
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=900, public')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0', [
                'id' => 'abc123',
                'title' => 'Jump explained',
                'description' => 'How Jump works.',
                'published_at' => '2026-09-01T12:00:00Z',
                'thumbnail' => 'https://i.ytimg.com/vi/abc123/hqdefault.jpg',
                'url' => 'https://www.youtube.com/watch?v=abc123',
            ]);

        Http::assertSent(fn ($request) => $request['playlistId'] === 'UUbkAE6vLlR6lOy_nxd--22g'
            && $request['key'] === 'test-key');
    }

    public function test_it_caches_the_list(): void
    {
        Http::fake([
            'www.googleapis.com/youtube/v3/playlistItems*' => Http::response($this->playlistResponse()),
        ]);

        $this->getJson('/api/videos')->assertOk();
        $this->getJson('/api/videos')->assertOk();

        Http::assertSentCount(1);
    }

    public function test_it_serves_the_last_good_list_when_youtube_fails(): void
    {
        Cache::forever(YouTubeVideoService::LAST_GOOD_CACHE_KEY, [['id' => 'stale']]);

        Http::fake([
            'www.googleapis.com/youtube/v3/playlistItems*' => Http::response([], 403),
        ]);

        $this->getJson('/api/videos')
            ->assertOk()
            ->assertJsonPath('data.0.id', 'stale');
    }

    public function test_it_returns_an_empty_list_without_an_api_key(): void
    {
        config(['services.youtube.api_key' => null]);
        Http::fake();

        $this->getJson('/api/videos')
            ->assertOk()
            ->assertExactJson(['data' => []]);

        Http::assertNothingSent();
    }

    /**
     * One published upload plus a private one, which the playlist still lists
     * without a publish date.
     */
    private function playlistResponse(): array
    {
        return [
            'items' => [
                [
                    'snippet' => [
                        'title' => 'Jump explained',
                        'description' => 'How Jump works.',
                        'thumbnails' => [
                            'high' => ['url' => 'https://i.ytimg.com/vi/abc123/hqdefault.jpg'],
                        ],
                    ],
                    'contentDetails' => [
                        'videoId' => 'abc123',
                        'videoPublishedAt' => '2026-09-01T12:00:00Z',
                    ],
                ],
                [
                    'snippet' => ['title' => 'Private video', 'description' => ''],
                    'contentDetails' => ['videoId' => 'hidden1'],
                ],
            ],
        ];
    }
}
