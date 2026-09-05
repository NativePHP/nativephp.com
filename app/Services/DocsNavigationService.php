<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocsPlatform;
use App\Support\CommonMark\CommonMark;
use Illuminate\Support\Str;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class DocsNavigationService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function build(DocsPlatform $platform, int $version): array
    {
        $basePath = resource_path('views');
        $path = sprintf('%s/docs/%s/%d', $basePath, $platform->value, $version);

        $navigation = collect();

        $mainPages = (new Finder)
            ->files()
            ->notName('_index.md')
            ->name('*.md')
            ->depth(0)
            ->in($path);

        /** @var SplFileInfo $mainPage */
        foreach ($mainPages as $mainPage) {
            $parsedSection = YamlFrontMatter::parse($mainPage->getContents());

            $navigation->push([
                'path' => $this->pagePath($basePath, $mainPage),
                'title' => $parsedSection->matter('title', ''),
                'order' => $parsedSection->matter('order', 0),
            ]);
        }

        $mainNavigation = (new Finder)
            ->files()
            ->name('_index.md')
            ->depth(1)
            ->in($path);

        /** @var SplFileInfo $section */
        foreach ($mainNavigation as $section) {
            $parsedSection = YamlFrontMatter::parse($section->getContents());

            $navigation->push([
                'relative_path' => $section->getRelativePath(),
                'title' => $parsedSection->matter('title', ''),
                // Sections without an explicit order trail rather than lead —
                // sectionRanks() (below) relies on this default for a section
                // that has no order of its own.
                'order' => $parsedSection->matter('order', 9999),
                'children' => $this->buildSectionChildren($basePath, $section->getPath()),
            ]);
        }

        return $navigation->sortBy('order')->values()->toArray();
    }

    /**
     * Scaled by 10000 so a nested subsection's own order can slot in right
     * after its parent without colliding with the next top-level section.
     *
     * @return array<string, int>
     */
    public function sectionRanks(DocsPlatform $platform, int $version): array
    {
        $rank = [];

        foreach ($this->build($platform, $version) as $entry) {
            if (! isset($entry['relative_path'])) {
                continue;
            }

            $slug = $entry['relative_path'];
            $rank[$slug] = $entry['order'] * 10000;

            foreach ($entry['children'] ?? [] as $child) {
                if (! ($child['is_subsection'] ?? false)) {
                    continue;
                }

                $rank[sprintf('%s/%s', $slug, $child['slug'])] = $rank[$slug] + 1 + min($child['order'], 9998);
            }
        }

        return $rank;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildSectionChildren(string $basePath, string $sectionPath): array
    {
        $children = collect();

        $subSections = (new Finder)
            ->files()
            ->notName('_index.md')
            ->name('*.md')
            ->depth(0)
            ->in($sectionPath);

        /** @var SplFileInfo $subSection */
        foreach ($subSections as $subSection) {
            $parsedSection = YamlFrontMatter::parse($subSection->getContents());

            $children->push([
                'path' => $this->pagePath($basePath, $subSection),
                'title' => $this->resolveTitle($parsedSection, $subSection->getContents()),
                'order' => $parsedSection->matter('order', 0),
            ]);
        }

        $nestedSections = (new Finder)
            ->files()
            ->name('_index.md')
            ->depth(1)
            ->in($sectionPath);

        /** @var SplFileInfo $nestedSection */
        foreach ($nestedSections as $nestedSection) {
            $parsedNested = YamlFrontMatter::parse($nestedSection->getContents());

            $nestedChildren = collect();

            $nestedPages = (new Finder)
                ->files()
                ->notName('_index.md')
                ->name('*.md')
                ->depth(0)
                ->in($nestedSection->getPath());

            /** @var SplFileInfo $nestedPage */
            foreach ($nestedPages as $nestedPage) {
                $parsedPage = YamlFrontMatter::parse($nestedPage->getContents());

                $nestedChildren->push([
                    'path' => $this->pagePath($basePath, $nestedPage),
                    'title' => $this->resolveTitle($parsedPage, $nestedPage->getContents()),
                    'order' => $parsedPage->matter('order', 0),
                ]);
            }

            $children->push([
                // The directory name, e.g. "core" for plugins/core — the key
                // sectionRanks() needs to match DocsSearchService's section
                // paths, which come straight from the filesystem too.
                'slug' => $nestedSection->getRelativePath(),
                'title' => $parsedNested->matter('title', ''),
                'order' => $parsedNested->matter('order', 9999),
                'is_subsection' => true,
                'children' => $nestedChildren->sortBy('order')->values()->toArray(),
            ]);
        }

        return $children->sortBy('order')->values()->toArray();
    }

    public function pagePath(string $basePath, SplFileInfo $file): string
    {
        return sprintf('%s/%s', Str::after($file->getPath(), $basePath), $file->getBasename('.md'));
    }

    private function resolveTitle(mixed $parsedPage, string $rawContents): string
    {
        $title = $parsedPage->matter('title', '');

        if ($title !== '') {
            return $title;
        }

        $html = CommonMark::convertToHtml($rawContents);

        preg_match('/<h1>([^<]+)/', $html, $matches);

        return $matches[1] ?? '';
    }
}
