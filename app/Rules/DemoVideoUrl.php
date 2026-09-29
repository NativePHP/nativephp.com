<?php

namespace App\Rules;

use App\Support\DemoVideo;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validates that a plugin demo video is a YouTube, Vimeo or Loom link
 * and that the provider confirms it exists and is publicly viewable.
 */
class DemoVideoUrl implements ValidationRule
{
    private const TIMEOUT_SECONDS = 10;

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $video = DemoVideo::fromUrl($value);

        if (! $video) {
            $fail('The demo video must be a YouTube, Vimeo or Loom link.');

            return;
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->acceptJson()
                ->get($video->oEmbedUrl());
        } catch (ConnectionException $e) {
            Log::warning('[DemoVideoUrl] oEmbed request failed', [
                'provider' => $video->provider,
                'url' => $video->url,
                'exception' => $e->getMessage(),
            ]);

            $fail("We couldn't reach {$video->providerLabel()} to verify the demo video. Please try again.");

            return;
        }

        if (! $response->successful()) {
            $fail("We couldn't find that video on {$video->providerLabel()}. Make sure it exists and is public or unlisted, not private.");
        }
    }
}
