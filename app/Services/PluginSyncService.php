<?php

namespace App\Services;

use App\Models\Plugin;
use App\Support\CommonMark\CommonMark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PluginSyncService
{
    public function sync(Plugin $plugin): bool
    {
        $repo = $plugin->getRepositoryOwnerAndName();

        if (! $repo) {
            Log::warning("Plugin {$plugin->id} has no valid repository URL");

            return false;
        }

        $baseUrl = "https://raw.githubusercontent.com/{$repo['owner']}/{$repo['repo']}/main";

        $readme = $this->fetchFile("{$baseUrl}/README.md");
        $composerJson = $this->fetchFile("{$baseUrl}/composer.json");
        $nativephpJson = $this->fetchFile("{$baseUrl}/nativephp.json");

        if (! $composerJson) {
            Log::warning("Plugin {$plugin->id}: Could not fetch composer.json");

            return false;
        }

        $composerData = json_decode($composerJson, true);
        $nativephpData = $nativephpJson ? json_decode($nativephpJson, true) : null;

        $updateData = [
            'composer_data' => $composerData,
            'nativephp_data' => $nativephpData,
            'last_synced_at' => now(),
        ];

        if ($composerData) {
            if (isset($composerData['description'])) {
                $updateData['description'] = $composerData['description'];
            }
        }

        if ($nativephpData) {
            $updateData['ios_version'] = $this->extractIosVersion($nativephpData);
            $updateData['android_version'] = $this->extractAndroidVersion($nativephpData);
        }

        if ($readme) {
            $updateData['readme_html'] = CommonMark::convertToHtml($readme);
        }

        $plugin->update($updateData);

        Log::info("Plugin {$plugin->id} synced successfully");

        return true;
    }

    protected function fetchFile(string $url): ?string
    {
        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Exception $e) {
            Log::warning("Failed to fetch {$url}: {$e->getMessage()}");
        }

        return null;
    }

    protected function extractIosVersion(array $nativephpData): ?string
    {
        return $nativephpData['ios']['min_version'] ?? null;
    }

    protected function extractAndroidVersion(array $nativephpData): ?string
    {
        return $nativephpData['android']['min_version'] ?? null;
    }
}
