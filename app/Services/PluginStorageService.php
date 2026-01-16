<?php

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PluginStorageService
{
    protected string $disk = 'r2_plugins';

    public function uploadPackage(Plugin $plugin, string $version, string $zipPath): string
    {
        $storagePath = $this->getPackagePath($plugin, $version);

        Storage::disk($this->disk)->put($storagePath, file_get_contents($zipPath));

        Log::info('Uploaded plugin package', [
            'plugin_id' => $plugin->id,
            'plugin_name' => $plugin->name,
            'version' => $version,
            'path' => $storagePath,
        ]);

        return $storagePath;
    }

    public function uploadPackagesJson(string $content): string
    {
        $path = 'packages.json';

        Storage::disk($this->disk)->put($path, $content);

        Log::info('Uploaded packages.json');

        return $path;
    }

    public function uploadPackageMetadata(Plugin $plugin, string $content): string
    {
        [$vendor, $package] = explode('/', $plugin->name);

        $path = "p2/{$vendor}/{$package}.json";

        Storage::disk($this->disk)->put($path, $content);

        return $path;
    }

    public function getPackageUrl(Plugin $plugin, string $version): string
    {
        $path = $this->getPackagePath($plugin, $version);

        return Storage::disk($this->disk)->url($path);
    }

    public function generateSignedUrl(Plugin $plugin, string $version, int $expirationMinutes = 15): string
    {
        $path = $this->getPackagePath($plugin, $version);

        return Storage::disk($this->disk)->temporaryUrl(
            $path,
            now()->addMinutes($expirationMinutes)
        );
    }

    public function deletePackage(Plugin $plugin, ?string $version = null): bool
    {
        if ($version) {
            $path = $this->getPackagePath($plugin, $version);

            return Storage::disk($this->disk)->delete($path);
        }

        [$vendor, $package] = explode('/', $plugin->name);
        $directory = "dist/{$vendor}/{$package}";

        return Storage::disk($this->disk)->deleteDirectory($directory);
    }

    public function packageExists(Plugin $plugin, string $version): bool
    {
        $path = $this->getPackagePath($plugin, $version);

        return Storage::disk($this->disk)->exists($path);
    }

    public function getPackageSize(Plugin $plugin, string $version): ?int
    {
        $path = $this->getPackagePath($plugin, $version);

        if (! Storage::disk($this->disk)->exists($path)) {
            return null;
        }

        return Storage::disk($this->disk)->size($path);
    }

    public function listPackageVersions(Plugin $plugin): array
    {
        [$vendor, $package] = explode('/', $plugin->name);
        $directory = "dist/{$vendor}/{$package}";

        $files = Storage::disk($this->disk)->files($directory);

        $versions = [];
        foreach ($files as $file) {
            if (preg_match('/\/([^\/]+)\.zip$/', $file, $matches)) {
                $versions[] = $matches[1];
            }
        }

        return $versions;
    }

    protected function getPackagePath(Plugin $plugin, string $version): string
    {
        [$vendor, $package] = explode('/', $plugin->name);

        return "dist/{$vendor}/{$package}/{$version}.zip";
    }

    public function getPackagesJsonContent(): ?string
    {
        if (! Storage::disk($this->disk)->exists('packages.json')) {
            return null;
        }

        return Storage::disk($this->disk)->get('packages.json');
    }

    public function getPackageMetadataContent(string $vendor, string $package): ?string
    {
        $path = "p2/{$vendor}/{$package}.json";

        if (! Storage::disk($this->disk)->exists($path)) {
            return null;
        }

        return Storage::disk($this->disk)->get($path);
    }
}
