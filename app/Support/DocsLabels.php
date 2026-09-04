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

    public static function jumpUrl(): ?string
    {
        return self::pageUrl('the-basics/jump');
    }

    /**
     * The current platform/version is always a real directory when a badge
     * is rendering (it's rendering on a live page inside it), so unlike
     * pageUrl() this never needs a file_exists guard.
     */
    public static function whatsNewUrl(): ?string
    {
        $platform = request()->route('platform');
        $version = request()->route('version');

        if (blank($platform) || blank($version)) {
            return null;
        }

        return route('docs.whats-new', ['platform' => $platform, 'version' => $version]);
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
