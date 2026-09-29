<?php

namespace App\Services;

class DocsVersionService
{
    public function determineCanonicalUrl(string $platform, string $page): string
    {
        $latestVersion = $platform === 'mobile' ? config('docs.latest_versions.mobile') : config('docs.latest_versions.desktop');

        $page = $this->resolveOldVersionApisWithPluginsCorePage($platform, $latestVersion, $page);
        $page = $this->resolvePageForVersion($platform, $latestVersion, $page);

        $latestPagePath = resource_path("views/docs/{$platform}/{$latestVersion}/{$page}.md");

        $page = file_exists($latestPagePath) ? $page : 'getting-started/introduction';

        return route('docs.show', [
            'platform' => $platform,
            'version' => $latestVersion,
            'page' => $page,
        ]);
    }

    /**
     * Map a page to its equivalent path in the target version, following the
     * configured renames in either direction. A page renamed in v4 resolves
     * old-to-new when targeting v4 or later, and new-to-old when targeting
     * anything earlier.
     */
    public function resolvePageForVersion(string $platform, int|string $targetVersion, string $page): string
    {
        $renames = config("docs.renamed_pages.{$platform}", []);

        ksort($renames);

        foreach ($renames as $renamedInVersion => $map) {
            if ((int) $targetVersion >= (int) $renamedInVersion && isset($map[$page])) {
                $page = $map[$page];
            }
        }

        krsort($renames);

        foreach ($renames as $renamedInVersion => $map) {
            if ((int) $targetVersion < (int) $renamedInVersion) {
                $reversed = array_flip($map);

                if (isset($reversed[$page])) {
                    $page = $reversed[$page];
                }
            }
        }

        return $page;
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
