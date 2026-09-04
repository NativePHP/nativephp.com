<?php

namespace App\Support;

use App\Enums\DocsPlatform;

/**
 * Platform/version come from the route rather than being passed in, since
 * BladeMarkdownPreprocessor hands page markdown to Blade with no view data
 * beyond the current user.
 */
final class DocsLabels
{
    public static function productName(): string
    {
        return DocsPlatform::tryFromRoute()?->label() ?? 'NativePHP';
    }

    /**
     * Null when that version's tree has no versioning page — an unlinked
     * label beats one that 404s.
     */
    public static function versioningPolicyUrl(): ?string
    {
        return self::pageUrl('getting-started/versioning', 'version-labels');
    }

    public static function jumpUrl(): ?string
    {
        return self::pageUrl('the-basics/jump');
    }

    private static function pageUrl(string $page, ?string $fragment = null): ?string
    {
        $platform = request()->route('platform');
        $version = request()->route('version');

        if (blank($platform) || blank($version)) {
            return null;
        }

        if (! file_exists(resource_path("views/docs/{$platform}/{$version}/{$page}.md"))) {
            return null;
        }

        $url = route('docs.show', [
            'platform' => $platform,
            'version' => $version,
            'page' => $page,
        ]);

        return $fragment ? "{$url}#{$fragment}" : $url;
    }
}
