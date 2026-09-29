<?php

namespace App\Support;

/**
 * A plugin demo video hosted on a supported provider (YouTube, Vimeo or Loom).
 */
final class DemoVideo
{
    public const PROVIDER_YOUTUBE = 'youtube';

    public const PROVIDER_VIMEO = 'vimeo';

    public const PROVIDER_LOOM = 'loom';

    private function __construct(
        public readonly string $provider,
        public readonly string $videoId,
        public readonly string $url,
        public readonly ?string $privacyHash = null,
    ) {}

    /**
     * Parse a public video URL. Returns null for unsupported hosts or malformed URLs.
     */
    public static function fromUrl(?string $url): ?self
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower(preg_replace('/^(www\.|m\.)/', '', $parts['host'] ?? ''));
        $path = $parts['path'] ?? '';

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        parse_str($parts['query'] ?? '', $query);

        $match = match ($host) {
            'youtube.com' => self::matchYouTubePath($path, $query),
            'youtu.be' => self::matchPattern('#^/([A-Za-z0-9_-]{11})/?$#', $path, self::PROVIDER_YOUTUBE),
            'vimeo.com' => self::matchPattern('#^/(\d+)(?:/([A-Za-z0-9]+))?/?$#', $path, self::PROVIDER_VIMEO),
            'loom.com' => self::matchPattern('#^/(?:share|embed)/([A-Za-z0-9]+)/?$#', $path, self::PROVIDER_LOOM),
            default => null,
        };

        if ($match === null) {
            return null;
        }

        return new self($match[0], $match[1], $url, $match[2] ?? null);
    }

    /**
     * @return array<int, string>
     */
    public static function supportedProviders(): array
    {
        return [self::PROVIDER_YOUTUBE, self::PROVIDER_VIMEO, self::PROVIDER_LOOM];
    }

    public function providerLabel(): string
    {
        return match ($this->provider) {
            self::PROVIDER_YOUTUBE => 'YouTube',
            self::PROVIDER_VIMEO => 'Vimeo',
            self::PROVIDER_LOOM => 'Loom',
        };
    }

    /**
     * The provider's oEmbed endpoint, which returns 200 only for public/unlisted videos that exist.
     */
    public function oEmbedUrl(): string
    {
        $watchUrl = urlencode($this->canonicalUrl());

        return match ($this->provider) {
            self::PROVIDER_YOUTUBE => "https://www.youtube.com/oembed?format=json&url={$watchUrl}",
            self::PROVIDER_VIMEO => "https://vimeo.com/api/oembed.json?url={$watchUrl}",
            self::PROVIDER_LOOM => "https://www.loom.com/v1/oembed?url={$watchUrl}",
        };
    }

    public function canonicalUrl(): string
    {
        return match ($this->provider) {
            self::PROVIDER_YOUTUBE => "https://www.youtube.com/watch?v={$this->videoId}",
            self::PROVIDER_VIMEO => 'https://vimeo.com/'.$this->videoId.($this->privacyHash ? "/{$this->privacyHash}" : ''),
            self::PROVIDER_LOOM => "https://www.loom.com/share/{$this->videoId}",
        };
    }

    public function embedUrl(): string
    {
        return match ($this->provider) {
            self::PROVIDER_YOUTUBE => "https://www.youtube-nocookie.com/embed/{$this->videoId}",
            self::PROVIDER_VIMEO => "https://player.vimeo.com/video/{$this->videoId}".($this->privacyHash ? "?h={$this->privacyHash}" : ''),
            self::PROVIDER_LOOM => "https://www.loom.com/embed/{$this->videoId}",
        };
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array{0: string, 1: string}|null
     */
    private static function matchYouTubePath(string $path, array $query): ?array
    {
        if (rtrim($path, '/') === '/watch') {
            $videoId = $query['v'] ?? null;

            return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)
                ? [self::PROVIDER_YOUTUBE, $videoId]
                : null;
        }

        return self::matchPattern('#^/(?:shorts|embed|live)/([A-Za-z0-9_-]{11})/?$#', $path, self::PROVIDER_YOUTUBE);
    }

    /**
     * @return array{0: string, 1: string, 2?: string}|null
     */
    private static function matchPattern(string $pattern, string $path, string $provider): ?array
    {
        if (! preg_match($pattern, $path, $matches)) {
            return null;
        }

        return array_filter([$provider, $matches[1], $matches[2] ?? null], fn (?string $value): bool => $value !== null && $value !== '');
    }
}
