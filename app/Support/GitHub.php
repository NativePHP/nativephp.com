<?php

namespace App\Support;

use App\Support\GitHub\Release;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHub
{
    public const PACKAGE_ELECTRON = 'nativephp/electron';

    public const PACKAGE_LARAVEL = 'nativephp/laravel';

    public const PACKAGE_DESKTOP = 'nativephp/desktop';

    public const PACKAGE_PHP_BIN = 'nativephp/php-bin';

    public const PACKAGE_MOBILE_AIR = 'nativephp/mobile-air';

    private const CACHE_LATEST_VERSION = 'latest-version';

    private const CACHE_RELEASES = 'releases';

    private const CACHE_TREE = 'tree';

    private const CACHE_DEFAULT_BRANCH = 'default-branch';

    private const CACHE_BLOB_PREFIX = 'blob-';

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

    public static function mobileAir(): static
    {
        return new static(static::PACKAGE_MOBILE_AIR);
    }

    public function latestVersion()
    {
        $release = Cache::remember(
            $this->getCacheKey(self::CACHE_LATEST_VERSION),
            now()->addHour(),
            fn () => $this->fetchLatestVersion()
        );

        return $release?->name ?? 'Unknown';
    }

    public function releases(): Collection
    {
        return Cache::remember(
            $this->getCacheKey(self::CACHE_RELEASES),
            now()->addHour(),
            fn () => $this->fetchReleases()
        ) ?? collect();
    }

    /**
     * @return Collection<int, array{path: string, type: string, sha: string}>
     */
    public function tree(): Collection
    {
        return Cache::remember(
            $this->getCacheKey(self::CACHE_TREE),
            now()->addHour(),
            fn () => $this->fetchTree()
        ) ?? collect();
    }

    /**
     * The decoded contents of a single blob, addressed by the sha reported
     * for it in tree(). Cached separately from the tree itself since blobs
     * are large and most only need to be fetched once per commit.
     */
    public function blob(string $sha): ?string
    {
        return Cache::remember(
            $this->getCacheKey(self::CACHE_BLOB_PREFIX.$sha),
            now()->addDay(),
            fn () => $this->fetchBlob($sha)
        );
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

    private function fetchLatestVersion(): ?Release
    {
        $response = $this->request()->get('/releases/latest');

        if ($response->failed()) {
            return null;
        }

        return new Release($response->json());
    }

    private function getCacheKey(string $string): string
    {
        return sprintf('%s-%s', $this->package, $string);
    }

    private function defaultBranch(): string
    {
        return Cache::remember(
            $this->getCacheKey(self::CACHE_DEFAULT_BRANCH),
            now()->addDay(),
            fn () => $this->fetchDefaultBranch()
        ) ?? 'main';
    }

    private function fetchDefaultBranch(): ?string
    {
        $response = $this->request()->get('/');

        if ($response->failed()) {
            return null;
        }

        return $response->json('default_branch');
    }

    private function fetchTree(): Collection
    {
        $response = $this->request()->get('/git/trees/'.$this->defaultBranch(), ['recursive' => 1]);

        if ($response->failed()) {
            return collect();
        }

        // GitHub silently truncates trees over its internal size limit rather
        // than erroring or paginating, so a truncated result would otherwise
        // look like a repo that simply doesn't have the missing files.
        if ($response->json('truncated') === true) {
            Log::warning("GitHub tree for {$this->package} was truncated; some paths may be missing from the audit.");
        }

        return collect($response->json('tree'));
    }

    private function fetchBlob(string $sha): ?string
    {
        $response = $this->request()->get('/git/blobs/'.$sha);

        if ($response->failed()) {
            return null;
        }

        $content = $response->json('content');

        return $content === null ? null : base64_decode($content);
    }

    private function fetchReleases(): ?Collection
    {
        $releases = collect();
        $page = 1;

        do {
            $response = $this->request()->get('/releases', [
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

    /**
     * A request pre-scoped to this repo, authenticated when a token is
     * configured — GitHub's REST API allows only 60 unauthenticated
     * requests/hour, which the per-file blob fetches in tree-walking
     * callers (e.g. DocsCoverageAuditor) can exhaust in a single run.
     */
    private function request(): PendingRequest
    {
        $request = Http::baseUrl('https://api.github.com/repos/'.$this->package);
        $token = config('services.github.token');

        return $token ? $request->withToken($token) : $request;
    }
}
