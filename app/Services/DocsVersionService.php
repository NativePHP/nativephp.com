<?php

namespace App\Services;

class DocsVersionService
{
    public function determineCanonicalUrl(string $platform, string $page): string
    {
        $latestVersion = $platform === 'mobile' ? config('docs.latest_versions.mobile') : config('docs.latest_versions.desktop');

        $page = $this->resolveOldVersionApisWithPluginsCorePage($platform, $latestVersion, $page);

        $latestPagePath = resource_path("views/docs/{$platform}/{$latestVersion}/{$page}.md");

        $page = file_exists($latestPagePath) ? $page : 'getting-started/introduction';

        return route('docs.show', [
            'platform' => $platform,
            'version' => $latestVersion,
            'page' => $page,
        ]);
    }

    /**
     * Handle renamed paths (e.g., apis/* moved to plugins/core/*)
     */
    public function resolveOldVersionApisWithPluginsCorePage(string $platform, string $latestVersion, string $page): string
    {
        if (str_starts_with($page, 'apis/')) {
            $remappedPage = 'plugins/core/'.substr($page, 5);
            $remappedPath = resource_path("views/docs/{$platform}/{$latestVersion}/{$remappedPage}.md");

            if (file_exists($remappedPath)) {
                $page = $remappedPage;
            }
        }

        return $page;
    }
}
