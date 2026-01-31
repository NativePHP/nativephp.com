<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;

class DocsSearchService
{
    protected string $docsPath;

    public function __construct()
    {
        $this->docsPath = resource_path('views/docs');
    }

    public function search(string $query, ?string $platform = null, ?string $version = null, int $limit = 10): array
    {
        if ($platform !== null && ! $this->sanitizePlatform($platform)) {
            return [];
        }
        if ($version !== null && ! $this->sanitizeVersion($version)) {
            return [];
        }

        $limit = min(max(1, $limit), 100);

        $pages = $this->getAllPages($platform, $version);
        $queryTerms = $this->tokenize($query);

        return collect($pages)
            ->map(function ($page) use ($queryTerms) {
                $score = $this->calculateScore($page, $queryTerms);
                $page['score'] = $score;
                $page['snippet'] = $this->extractSnippet($page['content'], $queryTerms);

                return $page;
            })
            ->filter(fn ($page) => $page['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->toArray();
    }

    public function getPage(string $platform, string $version, string $section, string $slug): ?array
    {
        $platform = $this->sanitizePlatform($platform);
        $version = $this->sanitizeVersion($version);
        $section = $this->sanitizePathSegment($section);
        $slug = $this->sanitizePathSegment($slug);

        if (! $platform || ! $version || ! $section || ! $slug) {
            return null;
        }

        $filePath = "{$this->docsPath}/{$platform}/{$version}/{$section}/{$slug}.md";

        if (! file_exists($filePath)) {
            return null;
        }

        return $this->parsePage($filePath, $platform, $version, $section);
    }

    public function getPageByPath(string $path): ?array
    {
        $parts = explode('/', $path);

        if (count($parts) < 4) {
            return null;
        }

        return $this->getPage($parts[0], $parts[1], $parts[2], $parts[3]);
    }

    public function listApis(string $platform, string $version): array
    {
        if (! $this->sanitizePlatform($platform) || ! $this->sanitizeVersion($version)) {
            return [];
        }

        return collect($this->getAllPages($platform, $version))
            ->filter(fn ($page) => $page['section'] === 'apis')
            ->sortBy('order')
            ->values()
            ->toArray();
    }

    public function getNavigation(string $platform, string $version): array
    {
        if (! $this->sanitizePlatform($platform) || ! $this->sanitizeVersion($version)) {
            return [];
        }

        $pages = $this->getAllPages($platform, $version);

        $sections = [];
        foreach ($pages as $page) {
            $section = $page['section'];
            if (! isset($sections[$section])) {
                $sections[$section] = [];
            }
            $sections[$section][] = $page;
        }

        foreach ($sections as $section => $sectionPages) {
            usort($sections[$section], fn ($a, $b) => $a['order'] <=> $b['order']);
        }

        return $sections;
    }

    public function getPlatforms(): array
    {
        return ['desktop', 'mobile'];
    }

    public function getVersions(?string $platform = null): array
    {
        $versions = [];

        foreach ($this->getPlatforms() as $plat) {
            if ($platform && $plat !== $platform) {
                continue;
            }

            $platformPath = "{$this->docsPath}/{$plat}";
            if (is_dir($platformPath)) {
                $versions[$plat] = collect(scandir($platformPath))
                    ->filter(fn ($dir) => is_dir("{$platformPath}/{$dir}") && ! in_array($dir, ['.', '..']))
                    ->values()
                    ->toArray();
            }
        }

        return $platform ? ($versions[$platform] ?? []) : $versions;
    }

    public function getLatestVersions(): array
    {
        $versions = $this->getVersions();

        return [
            'desktop' => collect($versions['desktop'] ?? [])->sort()->last() ?? '2',
            'mobile' => collect($versions['mobile'] ?? [])->sort()->last() ?? '3',
        ];
    }

    protected function getAllPages(?string $platform = null, ?string $version = null): array
    {
        if ($platform !== null && ! $this->sanitizePlatform($platform)) {
            return [];
        }
        if ($version !== null && ! $this->sanitizeVersion($version)) {
            return [];
        }

        $cacheKey = 'mcp_docs_pages_'.($platform ?? 'all').'_'.($version ?? 'all');

        if (config('app.env') !== 'local') {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }

        $pages = [];
        $platforms = $platform ? [$platform] : $this->getPlatforms();

        foreach ($platforms as $plat) {
            $versions = $version ? [$version] : $this->getVersions($plat);

            foreach ($versions as $ver) {
                $versionPath = "{$this->docsPath}/{$plat}/{$ver}";

                if (! is_dir($versionPath)) {
                    continue;
                }

                $finder = (new Finder)
                    ->files()
                    ->name('*.md')
                    ->notName('_index.md')
                    ->depth('> 0')
                    ->in($versionPath);

                foreach ($finder as $file) {
                    $section = basename(dirname($file->getPathname()));
                    $page = $this->parsePage($file->getPathname(), $plat, $ver, $section);
                    if ($page) {
                        $pages[] = $page;
                    }
                }
            }
        }

        if (config('app.env') !== 'local') {
            Cache::put($cacheKey, $pages, now()->addDay());
        }

        return $pages;
    }

    protected function parsePage(string $filePath, string $platform, string $version, string $section): ?array
    {
        if (! file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        $document = YamlFrontMatter::parse($content);
        $slug = pathinfo($filePath, PATHINFO_FILENAME);

        $cleanContent = $this->stripBladeComponents($document->body());

        return [
            'id' => "{$platform}/{$version}/{$section}/{$slug}",
            'platform' => $platform,
            'version' => $version,
            'section' => $section,
            'slug' => $slug,
            'title' => $document->matter('title') ?? $slug,
            'description' => $document->matter('description') ?? '',
            'content' => $cleanContent,
            'headings' => $this->extractHeadings($cleanContent),
            'order' => $document->matter('order') ?? 9999,
        ];
    }

    protected function stripBladeComponents(string $content): string
    {
        // Remove <x-component>...</x-component> tags
        $content = preg_replace('/<x-[^>]+>[\s\S]*?<\/x-[^>]+>/s', '', $content);
        // Remove self-closing <x-component /> tags
        $content = preg_replace('/<x-[^\/]+\/>/s', '', $content);
        // Remove {{ }} blade echoes
        $content = preg_replace('/\{\{.*?\}\}/s', '', $content);
        // Remove {!! !!} unescaped echoes
        $content = preg_replace('/\{!![\s\S]*?!!\}/s', '', $content);
        // Remove @directives
        $content = preg_replace('/@\w+(\([^)]*\))?/', '', $content);

        return $content;
    }

    protected function extractHeadings(string $content): array
    {
        preg_match_all('/^#{2,3}\s+(.+)$/m', $content, $matches);

        return $matches[1] ?? [];
    }

    protected function tokenize(string $text): array
    {
        return collect(preg_split('/\s+/', Str::lower($text)))
            ->filter(fn ($word) => strlen($word) > 2)
            ->values()
            ->toArray();
    }

    protected function calculateScore(array $page, array $queryTerms): float
    {
        $score = 0;
        $titleLower = Str::lower($page['title']);
        $descLower = Str::lower($page['description']);
        $contentLower = Str::lower($page['content']);
        $headingsLower = Str::lower(implode(' ', $page['headings']));

        foreach ($queryTerms as $term) {
            // Title matches (highest weight)
            if (Str::contains($titleLower, $term)) {
                $score += 10;
            }

            // Heading matches
            if (Str::contains($headingsLower, $term)) {
                $score += 5;
            }

            // Description matches
            if (Str::contains($descLower, $term)) {
                $score += 3;
            }

            // Content matches
            $contentMatches = substr_count($contentLower, $term);
            $score += min($contentMatches, 5); // Cap at 5 content matches
        }

        return $score;
    }

    protected function extractSnippet(string $content, array $queryTerms, int $length = 200): string
    {
        $contentLower = Str::lower($content);

        foreach ($queryTerms as $term) {
            $pos = strpos($contentLower, $term);
            if ($pos !== false) {
                $start = max(0, $pos - 50);
                $snippet = substr($content, $start, $length);

                if ($start > 0) {
                    $snippet = '...'.$snippet;
                }
                if ($start + $length < strlen($content)) {
                    $snippet .= '...';
                }

                return preg_replace('/\s+/', ' ', trim($snippet));
            }
        }

        return Str::limit(preg_replace('/\s+/', ' ', $content), $length);
    }

    protected function sanitizePlatform(?string $platform): ?string
    {
        if ($platform === null) {
            return null;
        }

        $allowed = ['desktop', 'mobile'];

        return in_array($platform, $allowed, true) ? $platform : null;
    }

    protected function sanitizeVersion(?string $version): ?string
    {
        if ($version === null) {
            return null;
        }

        return preg_match('/^[0-9]+$/', $version) ? $version : null;
    }

    protected function sanitizePathSegment(?string $segment): ?string
    {
        if ($segment === null || $segment === '') {
            return null;
        }

        if (str_contains($segment, '..') || str_contains($segment, '/') || str_contains($segment, '\\')) {
            return null;
        }

        return preg_match('/^[a-zA-Z0-9_-]+$/', $segment) ? $segment : null;
    }
}
