<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocsPlatform;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Aggregates every <x-docs.version-badge> across a platform's pages into a
 * per-minor-version "what's new" listing, so a badge marking a single line
 * on a single page becomes discoverable without already knowing which page
 * to look on. The versioning-policy page itself is excluded — its badges
 * are illustrative examples of the labelling system, not real feature history.
 */
final class DocsChangelogService
{
    public const TYPE_LABELS = [
        'added' => 'Added',
        'changed' => 'Changed',
        'deprecated' => 'Deprecated',
        'removed' => 'Removed',
    ];

    private const ATTRIBUTE_TYPES = [
        'since' => 'added',
        'changed' => 'changed',
        'deprecated' => 'deprecated',
        'removed' => 'removed',
    ];

    public function __construct(private DocsNavigationService $navigation) {}

    /**
     * @return array<string, array<string, array<int, array<string, string>>>> minor version, desc order => badge type => [{title, path}]
     */
    public function badgesForVersion(DocsPlatform $platform, int $major): array
    {
        $basePath = resource_path('views');
        $versionPath = sprintf('%s/docs/%s/%d', $basePath, $platform->value, $major);

        $entries = [];

        $files = (new Finder)
            ->files()
            ->name('*.md')
            ->notName('_index.md')
            ->notName('versioning.md')
            ->in($versionPath);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $document = YamlFrontMatter::parse($file->getContents());
            $title = $document->matter('title') ?: pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $path = $this->navigation->pagePath($basePath, $file);

            foreach ($this->badgesInBody($document->body()) as [$type, $minor]) {
                $entries[$minor][$type][$path] = ['title' => $title, 'path' => $path];
            }
        }

        krsort($entries, SORT_NATURAL);

        foreach ($entries as $minor => $types) {
            foreach ($types as $type => $pages) {
                $entries[$minor][$type] = collect($pages)->sortBy('title')->values()->all();
            }
        }

        return $entries;
    }

    /**
     * @return array<int, array{0: string, 1: string}> [type, minor version] pairs
     */
    private function badgesInBody(string $body): array
    {
        if (! preg_match_all('/<x-docs\.version-badge\b[^>]*\/>/', $body, $tags)) {
            return [];
        }

        $badges = [];

        foreach ($tags[0] as $tag) {
            foreach (self::ATTRIBUTE_TYPES as $attribute => $type) {
                if (! preg_match('/\b'.$attribute.'="([0-9]+)\.([0-9]+)"/', $tag, $match)) {
                    continue;
                }

                // x.0 never renders on the page itself (see version-badge.blade.php's
                // "everything in a major's tree was there at x.0" rule) — keeping it
                // out of the aggregate too avoids listing a badge nobody can see.
                if ((int) $match[2] === 0) {
                    continue;
                }

                $badges[] = [$type, "{$match[1]}.{$match[2]}"];
            }
        }

        return $badges;
    }
}
