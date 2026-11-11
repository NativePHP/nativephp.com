<?php

namespace App\Support;

use App\Support\GitHub\Release;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class GitHub
{
    public const PACKAGE_ELECTRON = 'nativephp/electron';

    public const PACKAGE_LARAVEL = 'nativephp/laravel';

    public const PACKAGE_DESKTOP = 'nativephp/desktop';

    public const PACKAGE_PHP_BIN = 'nativephp/php-bin';

    public const PACKAGE_MOBILE_AIR = 'nativephp/mobile-air';

    public function __construct(
        public readonly string $package
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

    public static function mobileAir(): static
    {
        return new static(static::PACKAGE_MOBILE_AIR);
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

    /**
     * Releases strictly after the given version, optionally capped below
     * another version and its prereleases — without a cap, a versioned
     * changelog would list the next major's releases too (4.0.0-rc.1
     * compares greater than any 3.x tag).
     */
    public function releasesAfter(string $version, ?string $before = null): Collection
    {
        $version = ltrim($version, 'v');
        $ceiling = $before === null ? null : ltrim($before, 'v').'-dev';

        return $this->releases()->filter(function (Release $release) use ($version, $ceiling) {
            $tag = ltrim((string) $release->tag_name, 'v');

            return version_compare($tag, $version, '>')
                && ($ceiling === null || version_compare($tag, $ceiling, '<'));
        })->values();
    }

    /**
     * Releases for the given version and everything later, including that
     * prefix version itself and its prereleases. releasesAfter("4.0.0")
     * would exclude 4.0.0-rc.1 because version_compare ranks prereleases
     * below the release; "dev" ranks below every other prerelease marker,
     * so a "-dev" floor admits alphas, betas, RCs and the release itself.
     */
    public function releasesFrom(string $version): Collection
    {
        $floor = ltrim($version, 'v').'-dev';

        return $this->releases()->filter(
            fn (Release $release) => version_compare(ltrim((string) $release->tag_name, 'v'), $floor, '>=')
        )->values();
    }

    /**
     * The repository's star count as last cached by refreshStars(). Never hits
     * the GitHub API, so it is safe to call while rendering a page.
     */
    public function stars(): ?int
    {
        return Cache::get($this->getCacheKey('stars'));
    }

    /**
     * The cached star count in a compact, human form such as "1.2k stars", or
     * null when nothing has been cached yet.
     */
    public function formattedStars(): ?string
    {
        $stars = $this->stars();

        if ($stars === null) {
            return null;
        }

        return Str::lower(Number::abbreviate($stars, maxPrecision: 1)).' '.Str::plural('star', $stars);
    }

    /**
     * Fetch the star count from GitHub and cache it with no expiry. A failed
     * request leaves the previously cached count in place.
     */
    public function refreshStars(): ?int
    {
        try {
            $response = Http::timeout(10)
                ->accept('application/vnd.github+json')
                ->when(config('services.github.token'), fn ($request, string $token) => $request->withToken($token))
                ->get('https://api.github.com/repos/'.$this->package);
        } catch (ConnectionException $e) {
            Log::warning('Failed to fetch GitHub star count', ['repository' => $this->package, 'error' => $e->getMessage()]);

            return null;
        }

        $stars = $response->json('stargazers_count');

        if ($response->failed() || ! is_int($stars)) {
            Log::warning('Failed to fetch GitHub star count', ['repository' => $this->package, 'status' => $response->status()]);

            return null;
        }

        Cache::forever($this->getCacheKey('stars'), $stars);

        return $stars;
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
        $releases = collect();
        $page = 1;

        do {
            $response = Http::get('https://api.github.com/repos/'.$this->package.'/releases', [
                'per_page' => 100,
                'page' => $page,
            ]);

            if ($response->failed()) {
                return $releases;
            }

            $pageReleases = $response->json();
            $releases = $releases->concat($pageReleases);
            $page++;
        } while (count($pageReleases) === 100);

        return $releases->map(fn (array $release) => new Release($release));
    }
}
