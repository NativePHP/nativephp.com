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
        Log::info('[PluginSync] Starting sync', ['plugin_id' => $plugin->id, 'name' => $plugin->name]);

        $repo = $plugin->getRepositoryOwnerAndName();

        if (! $repo) {
            Log::warning('[PluginSync] No valid repository URL', ['plugin_id' => $plugin->id]);

            return false;
        }

        Log::info('[PluginSync] Fetching from GitHub', [
            'plugin_id' => $plugin->id,
            'owner' => $repo['owner'],
            'repo' => $repo['repo'],
        ]);

        $token = $this->getGitHubToken($plugin);

        Log::info('[PluginSync] Token resolved', [
            'plugin_id' => $plugin->id,
            'has_token' => $token !== null,
        ]);

        $readme = $this->fetchFileFromGitHub($repo['owner'], $repo['repo'], 'README.md', $token);
        $license = $this->fetchLicenseFile($repo['owner'], $repo['repo'], $token);
        $composerJson = $this->fetchFileFromGitHub($repo['owner'], $repo['repo'], 'composer.json', $token);
        $nativephpJson = $this->fetchFileFromGitHub($repo['owner'], $repo['repo'], 'nativephp.json', $token);

        Log::info('[PluginSync] Fetch results', [
            'plugin_id' => $plugin->id,
            'has_readme' => $readme !== null,
            'readme_length' => $readme ? strlen($readme) : 0,
            'has_license' => $license !== null,
            'has_composer_json' => $composerJson !== null,
            'has_nativephp_json' => $nativephpJson !== null,
        ]);

        if (! $composerJson) {
            Log::warning('[PluginSync] Could not fetch composer.json', ['plugin_id' => $plugin->id]);

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
            if (isset($composerData['name']) && ! $plugin->name) {
                $updateData['name'] = $composerData['name'];
            }

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

        if ($license) {
            $updateData['license_html'] = CommonMark::convertToHtml($license);
        }

        // Fetch the latest tag/release
        $latestTag = $this->fetchLatestTag($repo['owner'], $repo['repo'], $token);
        if ($latestTag) {
            $updateData['latest_version'] = ltrim($latestTag, 'v');
        }

        Log::info('[PluginSync] Updating plugin', [
            'plugin_id' => $plugin->id,
            'fields' => array_keys($updateData),
            'has_readme_html' => isset($updateData['readme_html']),
            'latest_version' => $updateData['latest_version'] ?? null,
            'ios_version' => $updateData['ios_version'] ?? null,
            'android_version' => $updateData['android_version'] ?? null,
        ]);

        $plugin->update($updateData);

        Log::info('[PluginSync] Sync complete', ['plugin_id' => $plugin->id, 'name' => $plugin->name]);

        return true;
    }

    public function fetchLatestTag(string $owner, string $repo, ?string $token): ?string
    {
        try {
            $request = Http::timeout(10);

            if ($token) {
                $request = $request->withToken($token);
            }

            // First try to get the latest release
            $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/releases/latest");

            if ($response->successful()) {
                return $response->json('tag_name');
            }

            // Fall back to tags if no releases exist
            $tagsResponse = Http::timeout(10)
                ->when($token, fn ($http) => $http->withToken($token))
                ->get("https://api.github.com/repos/{$owner}/{$repo}/tags", [
                    'per_page' => 1,
                ]);

            if ($tagsResponse->successful() && count($tagsResponse->json()) > 0) {
                return $tagsResponse->json()[0]['name'];
            }
        } catch (\Exception $e) {
            Log::warning("Failed to fetch latest tag for {$owner}/{$repo}: {$e->getMessage()}");
        }

        return null;
    }

    protected function fetchFileFromGitHub(string $owner, string $repo, string $path, ?string $token): ?string
    {
        try {
            $request = Http::timeout(10);

            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/contents/{$path}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['content'])) {
                    return base64_decode($data['content']);
                }
            }

            $baseUrl = "https://raw.githubusercontent.com/{$owner}/{$repo}/main";
            $fallbackResponse = Http::timeout(10)->get("{$baseUrl}/{$path}");

            if ($fallbackResponse->successful()) {
                return $fallbackResponse->body();
            }
        } catch (\Exception $e) {
            Log::warning("Failed to fetch {$path} from {$owner}/{$repo}: {$e->getMessage()}");
        }

        return null;
    }

    protected function getGitHubToken(Plugin $plugin): ?string
    {
        $user = $plugin->user;

        if ($user && $user->hasGitHubToken()) {
            return $user->getGitHubToken();
        }

        return config('services.github.token');
    }

    protected function extractIosVersion(array $nativephpData): ?string
    {
        return $nativephpData['ios']['min_version'] ?? null;
    }

    protected function extractAndroidVersion(array $nativephpData): ?string
    {
        return $nativephpData['android']['min_version'] ?? null;
    }

    protected function fetchLicenseFile(string $owner, string $repo, ?string $token): ?string
    {
        // Try common license file names
        $licenseFiles = ['LICENSE.md', 'LICENSE', 'LICENSE.txt', 'license.md', 'license', 'license.txt'];

        foreach ($licenseFiles as $filename) {
            $content = $this->fetchFileFromGitHub($owner, $repo, $filename, $token);

            if ($content) {
                return $content;
            }
        }

        return null;
    }
}
