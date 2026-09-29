<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Vite;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class ViteManifestTest extends TestCase
{
    /**
     * Images are pulled into the build by the `import.meta.glob` call in
     * resources/js/app.js. If that glob stops emitting them, every
     * Vite::asset() call in the app throws at runtime instead of at build
     * time, so assert the manifest actually carries them.
     */
    public function test_every_vite_asset_path_in_the_app_resolves_in_the_manifest(): void
    {
        if (! file_exists(public_path('build/manifest.json'))) {
            $this->markTestSkipped('Assets have not been built; run npm run build.');
        }

        $paths = $this->viteAssetPathsUsedInTheApp();

        $this->assertNotEmpty($paths, 'Expected to find Vite::asset() calls to check.');

        foreach ($paths as $path) {
            Vite::asset($path);
        }
    }

    public function test_the_manifest_contains_the_javascript_and_css_entry_points(): void
    {
        if (! file_exists(public_path('build/manifest.json'))) {
            $this->markTestSkipped('Assets have not been built; run npm run build.');
        }

        foreach (['resources/js/app.js', 'resources/css/app.css'] as $entry) {
            Vite::asset($entry);
        }

        $this->addToAssertionCount(1);
    }

    /**
     * @return list<string>
     */
    private function viteAssetPathsUsedInTheApp(): array
    {
        $files = Finder::create()
            ->files()
            ->in([resource_path(), app_path()])
            ->name(['*.php', '*.blade.php']);

        $paths = [];

        foreach ($files as $file) {
            preg_match_all(
                "/Vite::asset\(\s*'([^']+)'/",
                $file->getContents(),
                $matches,
            );

            foreach ($matches[1] as $path) {
                $paths[$path] = true;
            }
        }

        return array_keys($paths);
    }
}
