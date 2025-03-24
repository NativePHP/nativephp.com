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

    public function __construct(
        private string $package
    ) {}

    public static function electron(): static
    {
        return new static(static::PACKAGE_ELECTRON);
    }

    public static function laravel(): static
    {
        return new static(static::PACKAGE_LARAVEL);
    }

    public function latestVersion()
    {
        $version = Cache::remember(
            $this->getCacheKey('latest-version'),
            now()->addHour(),
            fn () => $this->fetchLatestVersion()
        );

        return $version['name'] ?? 'Unknown';
    }

    public function releases(): Collection
    {
        return Cache::remember(
            $this->getCacheKey('releases'),
            now()->addHour(),
            fn () => $this->fetchReleases()
        );
    }

    private function fetchLatestVersion()
    {
        // Make a request to GitHub
        $response = Http::get('https://api.github.com/repos/'.$this->package.'/releases/latest');

        // Check if the request was successful
        if ($response->failed()) {
            return null;
        }

        return $response->json();
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
