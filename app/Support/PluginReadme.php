<?php

namespace App\Support;

use App\Models\Plugin;

class PluginReadme
{
    /**
     * File extensions a license file is commonly given.
     */
    protected const LICENSE_EXTENSIONS = 'md|markdown|mdown|txt|rst|html?';

    /**
     * Matches LICENSE, LICENCE, UNLICENSE, COPYING and suffixed variants such as LICENSE-MIT.
     */
    protected const LICENSE_NAME = '/^(?:(?:un)?licen[sc]e|copying)(?:[-_][a-z0-9.]+)?$/';

    /**
     * Point links to a plugin's license file at the license page we host for it.
     */
    public static function rewriteLicenseLinks(string $html, Plugin $plugin): string
    {
        if ($html === '') {
            return $html;
        }

        $rewritten = preg_replace_callback(
            '/(<a\b[^>]*?\bhref\s*=\s*)(["\'])(.*?)\2/i',
            function (array $matches) use ($plugin): string {
                $url = static::licenseUrlFor(htmlspecialchars_decode($matches[3], ENT_QUOTES), $plugin);

                return $url === null
                    ? $matches[0]
                    : $matches[1].$matches[2].e($url).$matches[2];
            },
            $html
        );

        return $rewritten ?? $html;
    }

    /**
     * Resolve the URL a license link should point at, or null if it isn't a license link.
     */
    protected static function licenseUrlFor(string $href, Plugin $plugin): ?string
    {
        $file = static::licenseFile($href, $plugin);

        if ($file === null) {
            return null;
        }

        if ($plugin->hasLicensePage()) {
            return route('plugins.license', $plugin->routeParams());
        }

        return $plugin->getRepositoryFileUrl($file);
    }

    /**
     * Extract the license file a link refers to, or null if it points elsewhere.
     */
    protected static function licenseFile(string $href, Plugin $plugin): ?string
    {
        $href = trim($href);

        if ($href === '' || str_starts_with($href, '#')) {
            return null;
        }

        $parts = parse_url($href);

        if ($parts === false) {
            return null;
        }

        $file = isset($parts['scheme']) || isset($parts['host'])
            ? static::repositoryFile($parts, $plugin)
            : static::rootRelativeFile($parts['path'] ?? '');

        return $file !== null && static::looksLikeLicenseFile($file) ? $file : null;
    }

    /**
     * Resolve a relative link that sits alongside the README at the repository root.
     */
    protected static function rootRelativeFile(string $path): ?string
    {
        $path = ltrim(preg_replace('#^(?:\./)+#', '', $path) ?? '', '/');

        return $path === '' || str_contains($path, '/') ? null : $path;
    }

    /**
     * Resolve an absolute GitHub link back to a file at the plugin's repository root.
     *
     * @param  array<string, mixed>  $parts
     */
    protected static function repositoryFile(array $parts, Plugin $plugin): ?string
    {
        if (! in_array(strtolower($parts['scheme'] ?? 'https'), ['http', 'https'], true)) {
            return null;
        }

        $repo = $plugin->getRepositoryOwnerAndName();

        if (! $repo) {
            return null;
        }

        // github.com/{owner}/{repo}/blob/{ref}/{file} or raw.githubusercontent.com/{owner}/{repo}/{ref}/{file}
        $fileIndex = match (strtolower((string) ($parts['host'] ?? ''))) {
            'github.com', 'www.github.com' => 4,
            'raw.githubusercontent.com' => 3,
            default => null,
        };

        $segments = array_values(array_filter(explode('/', (string) ($parts['path'] ?? '')), 'strlen'));

        if ($fileIndex === null || count($segments) !== $fileIndex + 1) {
            return null;
        }

        if (strcasecmp($segments[0], $repo['owner']) !== 0 || strcasecmp($segments[1], $repo['repo']) !== 0) {
            return null;
        }

        if ($fileIndex === 4 && ! in_array(strtolower($segments[2]), ['blob', 'raw'], true)) {
            return null;
        }

        return $segments[$fileIndex];
    }

    protected static function looksLikeLicenseFile(string $file): bool
    {
        $name = mb_strtolower(rawurldecode($file));
        $name = preg_replace('/\.(?:'.static::LICENSE_EXTENSIONS.')$/', '', $name) ?? $name;

        return (bool) preg_match(static::LICENSE_NAME, $name);
    }
}
