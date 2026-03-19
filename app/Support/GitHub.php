<?php

namespace App\Support;

use App\Support\GitHub\Release;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHub
{
    public const PACKAGE_ELECTRON = 'nativephp/electron';

    public const PACKAGE_LARAVEL = 'nativephp/laravel';

    public const PACKAGE_DESKTOP = 'nativephp/desktop';

    public const PACKAGE_PHP_BIN = 'nativephp/php-bin';

    public function __construct(
        private string $package
    ) {}

    public static function desktop(): static
    {
        return new static(static::PACKAGE_DESKTOP);
    }

    // V1
    public static function electron(): static
    {
        return new static(static::PACKAGE_ELECTRON);
    }

    // V1
    public static function laravel(): static
    {
        return new static(static::PACKAGE_LARAVEL);
    }

    public static function phpBin(): static
    {
        return new static(static::PACKAGE_PHP_BIN);
    }

    public function latestVersion()
    {
        $release = Cache::remember(
            $this->getCacheKey('latest-version'),
            now()->addHour(),
            fn () => $this->fetchLatestVersion()
        );

        return $release?->name ?? 'Unknown';
    }

    public function releases(): Collection
    {
        return Cache::remember(
            $this->getCacheKey('releases'),
            now()->addHour(),
            fn () => $this->fetchReleases()
        ) ?? collect();
    }

    private function fetchLatestVersion(): ?Release
    {
        // Make a request to GitHub
        $response = Http::get('https://api.github.com/repos/'.$this->package.'/releases/latest');

        // Check if the request was successful
        if ($response->failed()) {
            return null;
        }

        return new Release($response->json());
    }

    private function getCacheKey(string $string): string
    {
        return sprintf('%s-%s', $this->package, $string);
    }

    private function fetchReleases(): ?Collection
    {
        // Make a request to GitHub
        $response = Http::get('https://api.github.com/repos/'.$this->package.'/releases');

        // Check if the request was successful
        if ($response->failed()) {
            return collect();
        }

        return collect($response->json())->map(fn (array $release) => new Release($release));
    }
}
