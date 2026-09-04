<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocsPlatform;

final class DocsVersionRegistry
{
    public function latest(DocsPlatform $platform): int
    {
        return (int) config("docs.latest_versions.{$platform->value}");
    }

    public function isPrerelease(DocsPlatform $platform, int $version): bool
    {
        return in_array($version, config("docs.prerelease_versions.{$platform->value}", []), true);
    }

    /**
     * @return array<int, string> minor versions released for one major, e.g. ['4.0', '4.1', '4.2']
     */
    public function releasedMinors(DocsPlatform $platform, int $major): array
    {
        return config("docs.released_versions.{$platform->value}.{$major}", []);
    }

    /**
     * Version-switcher labels for every released major, e.g. [1 => '1.x', 2 => '2.x'].
     *
     * @return array<int, string>
     */
    public function switcherLabels(DocsPlatform $platform): array
    {
        $majors = array_keys(config("docs.released_versions.{$platform->value}", []));

        return array_combine($majors, array_map(fn (int $major): string => "{$major}.x", $majors));
    }

    /**
     * @return array<int, array<string, string>> renames keyed by the version the rename happened in
     */
    public function renames(DocsPlatform $platform): array
    {
        return config("docs.renamed_pages.{$platform->value}", []);
    }
}
