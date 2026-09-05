<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocsPlatform;
use App\Support\GitHub;
use App\Support\GitHub\Release;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
     * Links each minor version to the latest patch release listed for it on the
     * platform's changelog, so "4.2" on the What's New page lands on 4.2.3's
     * entry rather than the top of a long page.
     *
     * Majors whose changelog isn't built from a GitHub release feed, and minors
     * with no release to point at, get no link — the heading stands on its own.
     *
     * @param  array<int, string>  $minors
     * @return array<string, array{version: string, url: string, label: string}>
     */
    public function changelogLinks(DocsPlatform $platform, int $major, array $minors): array
    {
        $changelog = config("docs.changelog.{$platform->value}.{$major}");

        if ($changelog === null || ! file_exists(resource_path("views/docs/{$platform->value}/{$major}/{$changelog['page']}.md"))) {
            return [];
        }

        $releases = $this->releaseNames($changelog['repository']);

        $changelogUrl = route('docs.show', [
            'platform' => $platform->value,
            'version' => $major,
            'page' => $changelog['page'],
        ]);

        $links = [];

        foreach ($minors as $minor) {
            $latest = $releases
                ->filter(fn (string $name) => str_starts_with(ltrim($name, 'v'), "{$minor}."))
                ->sort(fn (string $a, string $b) => version_compare(ltrim($a, 'v'), ltrim($b, 'v')))
                ->last();

            if ($latest === null) {
                continue;
            }

            $links[$minor] = [
                'version' => $latest,
                'url' => $changelogUrl.'#'.Str::slug($latest),
                'label' => $changelog['label'],
            ];
        }

        return $links;
    }

    /**
     * The release names as the changelog page prints them in its headings —
     * HeadingRenderer slugs that heading text into the anchor, so the link has
     * to be built from the name rather than the tag.
     *
     * @return Collection<int, string>
     */
    private function releaseNames(string $repository): Collection
    {
        return (new GitHub($repository))->releases()
            ->map(fn (Release $release) => (string) ($release->name ?: $release->tag_name))
            ->filter()
            ->values();
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
