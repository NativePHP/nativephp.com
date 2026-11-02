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
        $section = $this->sanitizeSectionPath($section);
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

    /**
     * Resolve a page from a `platform/version/section/slug` path. The section
     * may itself be nested (e.g. `mobile/4/plugins/core/camera`), so anything
     * between the version and the slug is treated as the section path.
     */
    public function getPageByPath(string $path): ?array
    {
        $parts = explode('/', $path);

        if (count($parts) < 4) {
            return null;
        }

        $platform = array_shift($parts);
        $version = array_shift($parts);
        $slug = array_pop($parts);

        return $this->getPage($platform, $version, implode('/', $parts), $slug);
    }

    public function listEdgeComponents(string $platform, string $version): array
    {
        if (! $this->sanitizePlatform($platform) || ! $this->sanitizeVersion($version)) {
            return [];
        }

        return collect($this->getAllPages($platform, $version))
            ->filter(fn ($page) => $page['section'] === 'edge-components'
                || str_starts_with($page['section'], 'edge-components/'))
            ->sortBy('order')
            ->values()
            ->toArray();
    }

    /**
     * Every EDGE element tag documented for a platform/version, keyed by tag
     * (`list-item`), with the page that documents it. Built from the same
     * markdown the docs render, so it can't drift from the published pages.
     *
     * A tag belongs to the H2 section named after it (`## List Item` on the
     * list page), else to the page whose slug matches it, else to the page
     * that names it most often in prose (`<native:filled-text-input>` on the
     * text-input page).
     *
     * @return array<string, array{tag: string, page: array<string, mixed>, section: ?string}>
     */
    public function edgeElements(string $platform, string $version): array
    {
        $claims = [];

        foreach ($this->listEdgeComponents($platform, $version) as $page) {
            $sections = $this->splitH2Sections($page['content']);
            $tagsOnPage = $this->tagsIn($page['content']);

            foreach (array_keys($sections) as $heading) {
                $tag = Str::slug($heading);

                if ($heading !== '' && in_array($tag, $tagsOnPage, true)) {
                    $claims[$tag][] = ['page' => $page, 'section' => $heading, 'weight' => PHP_INT_MAX];
                }
            }

            foreach ($tagsOnPage as $tag) {
                if (str_replace('-', '', $tag) === str_replace('-', '', $page['slug'])) {
                    $claims[$tag][] = ['page' => $page, 'section' => null, 'weight' => PHP_INT_MAX - 1];
                }
            }

            preg_match_all('/```[\s\S]*?```/', $page['content'], $codeBlocks);
            $tagsInCode = $this->tagsIn(implode("\n", $codeBlocks[0]));
            $prose = preg_replace('/```[\s\S]*?```/', '', $page['content']);
            preg_match_all('/`<native:([a-z][a-z0-9-]*[a-z0-9])[\s>\/`]/', $prose, $mentions);

            foreach (array_count_values($mentions[1]) as $tag => $count) {
                if (in_array($tag, $tagsInCode, true)) {
                    $claims[$tag][] = ['page' => $page, 'section' => null, 'weight' => $count];
                }
            }
        }

        $elements = [];

        foreach ($claims as $tag => $tagClaims) {
            usort($tagClaims, fn ($a, $b) => $b['weight'] <=> $a['weight']);

            $elements[$tag] = [
                'tag' => $tag,
                'page' => $tagClaims[0]['page'],
                'section' => $tagClaims[0]['section'],
            ];
        }

        ksort($elements);

        return $elements;
    }

    /**
     * The reference for one EDGE element: its props, events, children and
     * fluent API as documented, without the examples. Accepts `list-item`,
     * `<native:list-item>`, `list_item` or `ListItem`.
     *
     * @return array{tag: string, title: string, path: string, section: ?string, other_tags: array<int, string>, php_classes: array<int, string>, reference: string}|null
     */
    public function getEdgeElement(string $platform, string $version, string $tag): ?array
    {
        $tag = $this->normalizeTag($tag);
        $elements = $this->edgeElements($platform, $version);

        if ($tag === '' || ! isset($elements[$tag])) {
            return null;
        }

        $element = $elements[$tag];
        $page = $element['page'];
        $sections = $this->splitH2Sections($page['content']);

        $siblings = collect($elements)
            ->filter(fn ($other) => $other['page']['id'] === $page['id'] && $other['tag'] !== $tag);

        if ($element['section'] !== null) {
            $content = "## {$element['section']}\n".$sections[$element['section']]
                .$this->fluentApiFor(Str::studly($tag), $sections);

            preg_match_all('/Native\\\\Mobile\\\\[A-Za-z\\\\]+\\\\Elements\\\\'.Str::studly($tag).'\b/', $page['content'], $classes);
        } else {
            $ownedElsewhere = $siblings->pluck('section')->filter()->all();

            $content = collect($sections)
                ->reject(fn ($body, $heading) => $heading === 'Examples' || in_array($heading, $ownedElsewhere, true))
                ->map(fn ($body, $heading) => $heading === '' ? $body : "## {$heading}\n{$body}")
                ->implode("\n");

            preg_match_all('/Native\\\\Mobile\\\\[A-Za-z\\\\]+\\\\Elements\\\\\w+/', $content, $classes);
        }

        $reference = preg_replace('/```[\s\S]*?```\n?/', '', $content);
        $reference = trim(preg_replace("/\n{3,}/", "\n\n", $reference));

        return [
            'tag' => $tag,
            'title' => $page['title'],
            'path' => $page['id'],
            'section' => $element['section'],
            'other_tags' => $siblings->keys()->values()->all(),
            'php_classes' => array_values(array_unique($classes[0])),
            'reference' => $reference,
        ];
    }

    /**
     * The H3 block documenting a sub-element's fluent methods, which lives in
     * the page's Element section (`### \`ListItem\` methods`) rather than in
     * the sub-element's own section.
     *
     * @param  array<string, string>  $sections
     */
    protected function fluentApiFor(string $class, array $sections): string
    {
        $pattern = '/^###\s+`'.preg_quote($class, '/').'`[^\n]*\n[\s\S]*?(?=^###?\s|\z)/m';

        foreach ($sections as $body) {
            if (preg_match($pattern, $body, $match)) {
                return "\n".$match[0];
            }
        }

        return '';
    }

    /**
     * Split markdown into its H2 sections, keyed by heading text. Text before
     * the first H2 is keyed ''. Headings inside fenced code are ignored.
     *
     * @return array<string, string>
     */
    protected function splitH2Sections(string $content): array
    {
        $sections = ['' => ''];
        $current = '';
        $inFence = false;

        foreach (explode("\n", $content) as $line) {
            if (str_starts_with(ltrim($line), '```')) {
                $inFence = ! $inFence;
            }

            if (! $inFence && preg_match('/^##\s+(.+?)\s*$/', $line, $match)) {
                $current = $match[1];
                $sections[$current] = '';

                continue;
            }

            $sections[$current] .= $line."\n";
        }

        if (trim($sections['']) === '') {
            unset($sections['']);
        }

        return $sections;
    }

    /**
     * @return array<int, string>
     */
    protected function tagsIn(string $content): array
    {
        preg_match_all('/<native:([a-z][a-z0-9-]*[a-z0-9])\b/', $content, $matches);

        return array_values(array_unique($matches[1]));
    }

    protected function normalizeTag(string $tag): string
    {
        $tag = trim($tag);
        $tag = preg_replace('/^<?\/?(native:)?/', '', $tag);
        $tag = rtrim($tag, ' />');
        $tag = Str::kebab(str_replace('_', '-', $tag));

        return preg_match('/^[a-z][a-z0-9-]*$/', $tag) ? $tag : '';
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

        // Order the sections themselves to match the sidebar (each section
        // directory's `_index.md` front-matter `order` — the same source
        // ShowDocumentationController sorts by). Nested subsections (e.g.
        // plugins/core) rank right after their parent. JSON objects keep key
        // order, so API consumers get the sidebar order for free. Unknown
        // sections trail in their original grouping order (stable sort).
        $rank = $this->sectionRanks($platform, $version);
        $slugs = array_keys($sections);
        usort($slugs, fn ($a, $b) => ($rank[$a] ?? PHP_INT_MAX) <=> ($rank[$b] ?? PHP_INT_MAX));

        $ordered = [];
        foreach ($slugs as $slug) {
            $ordered[$slug] = $sections[$slug];
        }

        return $ordered;
    }

    /**
     * Sidebar rank per section slug for one platform/version: top-level
     * sections rank by their `_index.md` front-matter `order` (scaled so
     * children can interleave); a nested subsection ranks just after its
     * parent, offset by its own `order`. Sections without an `_index.md`
     * get no rank (callers push them to the end).
     *
     * @return array<string, int>
     */
    protected function sectionRanks(string $platform, string $version): array
    {
        $base = "{$this->docsPath}/{$platform}/{$version}";
        $rank = [];

        foreach (glob("{$base}/*/_index.md") ?: [] as $index) {
            $slug = basename(dirname($index));
            $order = YamlFrontMatter::parse(file_get_contents($index))->matter('order') ?? 9999;
            $rank[$slug] = $order * 10000;

            foreach (glob("{$base}/{$slug}/*/_index.md") ?: [] as $nested) {
                $nestedSlug = basename(dirname($nested));
                $nestedOrder = YamlFrontMatter::parse(file_get_contents($nested))->matter('order') ?? 9999;
                $rank["{$slug}/{$nestedSlug}"] = $rank[$slug] + 1 + min($nestedOrder, 9998);
            }
        }

        return $rank;
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
            'desktop' => (string) (config('docs.latest_versions.desktop') ?? collect($versions['desktop'] ?? [])->sort()->last() ?? '2'),
            'mobile' => (string) (config('docs.latest_versions.mobile') ?? collect($versions['mobile'] ?? [])->sort()->last() ?? '3'),
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

        // v3 keys: prose now keeps inline code such as `@press`, so pages cached
        // with those event names stripped must not be reused after a deploy.
        $cacheKey = 'mcp_docs_pages_v3_'.($platform ?? 'all').'_'.($version ?? 'all');

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
                    // Relative to the version directory, so a page nested in a
                    // subsection keeps its full section path (`plugins/core`)
                    // and the ids we hand out stay resolvable by getPage().
                    $section = $file->getRelativePath();
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
        // Fenced code blocks are preserved verbatim: they document the component
        // API itself (<native:*> tags, @press/@change directives, {{ }} bindings),
        // and Jump renders them as live examples. Only prose is cleaned — stripping
        // directives from a code block turns `@press="save"` into `="save"`.
        $segments = preg_split('/(```[\s\S]*?```)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($segments as $i => $segment) {
            if (str_starts_with($segment, '```')) {
                continue; // code block — leave untouched
            }

            $segments[$i] = $this->stripBladeFromProse($segment);
        }

        return implode('', $segments);
    }

    /**
     * Clean Blade out of prose while keeping inline code spans. Inline code is
     * where the docs name event attributes (`@press`, `@change`) and show
     * escaped echoes (`@{{ $name }}`), so it is unescaped the way Blade would
     * render it instead of being stripped with the surrounding directives.
     */
    protected function stripBladeFromProse(string $prose): string
    {
        // Remove <x-component>...</x-component> tags
        $prose = preg_replace('/<x-[^>]+>[\s\S]*?<\/x-[^>]+>/s', '', $prose);
        // Remove self-closing <x-component /> tags
        $prose = preg_replace('/<x-[^\/]+\/>/s', '', $prose);

        $parts = preg_split('/(`[^`\n]+`)/', $prose, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts as $i => $part) {
            if (str_starts_with($part, '`')) {
                $parts[$i] = str_replace(['@{{', '@@'], ['{{', '@'], $part);

                continue;
            }

            // Remove {{ }} blade echoes
            $part = preg_replace('/\{\{.*?\}\}/s', '', $part);
            // Remove {!! !!} unescaped echoes
            $part = preg_replace('/\{!![\s\S]*?!!\}/s', '', $part);
            // Remove @directives (@verbatim wrappers, @php, etc.)
            $parts[$i] = preg_replace('/@\w+(\([^)]*\))?/', '', $part);
        }

        return implode('', $parts);
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
                $snippet = mb_strcut($content, $start, $length, 'UTF-8');

                if ($start > 0) {
                    $snippet = '...'.$snippet;
                }
                if ($start + $length < mb_strlen($content, '8bit')) {
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

    /**
     * Validate a section path, which may nest (e.g. `plugins/core`). Every
     * segment is checked on its own so traversal can't hide behind a separator.
     */
    protected function sanitizeSectionPath(?string $section): ?string
    {
        if ($section === null || $section === '') {
            return null;
        }

        $segments = explode('/', $section);

        foreach ($segments as $segment) {
            if (! $this->sanitizePathSegment($segment)) {
                return null;
            }
        }

        return implode('/', $segments);
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
