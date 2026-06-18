<?php

namespace App\Jobs;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReviewPluginRepository implements ShouldQueue
{
    use Concerns\ResolvesGitHubToken, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public Plugin $plugin) {}

    public function handle(): array
    {
        $repo = $this->plugin->getRepositoryOwnerAndName();

        $failedChecks = [
            'has_license_file' => false,
            'has_release_version' => false,
            'release_version' => null,
            'supports_ios' => false,
            'supports_android' => false,
            'supports_js' => false,
            'requires_mobile_sdk' => false,
            'mobile_sdk_constraint' => null,
            'has_ios_min_version' => false,
            'ios_min_version' => null,
            'has_android_min_version' => false,
            'android_min_version' => null,
        ];

        if (! $repo) {
            Log::warning('[ReviewPluginRepository] No valid repository URL', [
                'plugin_id' => $this->plugin->id,
            ]);

            $this->plugin->update(['review_checks' => $failedChecks, 'reviewed_at' => now()]);

            return $failedChecks;
        }

        $token = $this->getGitHubToken();
        $owner = $repo['owner'];
        $repoName = $repo['repo'];

        $defaultBranch = $this->fetchDefaultBranch($owner, $repoName, $token);

        if (! $defaultBranch) {
            Log::warning('[ReviewPluginRepository] Could not determine default branch', [
                'plugin_id' => $this->plugin->id,
            ]);

            $this->plugin->update(['review_checks' => $failedChecks, 'reviewed_at' => now()]);

            return $failedChecks;
        }

        $tree = $this->fetchRepoTree($owner, $repoName, $defaultBranch, $token);
        $composerJson = $this->fetchComposerJson($owner, $repoName, $token);
        $nativephpJson = $this->fetchNativephpJson($owner, $repoName, $token);

        $checks = [
            'has_license_file' => $this->checkHasLicenseFile($tree),
            'has_release_version' => false,
            'release_version' => null,
            'supports_ios' => $this->checkDirectoryHasFiles($tree, 'resources/ios/'),
            'supports_android' => $this->checkDirectoryHasFiles($tree, 'resources/android/'),
            'supports_js' => $this->checkDirectoryHasFiles($tree, 'resources/js/'),
            'requires_mobile_sdk' => false,
            'mobile_sdk_constraint' => null,
            'has_ios_min_version' => false,
            'ios_min_version' => null,
            'has_android_min_version' => false,
            'android_min_version' => null,
        ];

        $latestTag = $this->fetchLatestTag($owner, $repoName, $token);
        if ($latestTag) {
            $checks['has_release_version'] = true;
            $checks['release_version'] = $latestTag;
        }

        if ($composerJson) {
            $mobileConstraint = $composerJson['require']['nativephp/mobile'] ?? null;
            $checks['requires_mobile_sdk'] = $mobileConstraint !== null;
            $checks['mobile_sdk_constraint'] = $mobileConstraint;
        }

        if ($nativephpJson) {
            $iosMinVersion = $nativephpJson['ios']['min_version'] ?? null;
            $checks['has_ios_min_version'] = $iosMinVersion !== null;
            $checks['ios_min_version'] = $iosMinVersion;

            $androidMinVersion = $nativephpJson['android']['min_version'] ?? null;
            $checks['has_android_min_version'] = $androidMinVersion !== null;
            $checks['android_min_version'] = $androidMinVersion;
        }

        $this->plugin->update([
            'review_checks' => $checks,
            'reviewed_at' => now(),
        ]);

        Log::info('[ReviewPluginRepository] Review complete', [
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'checks' => $checks,
        ]);

        return $checks;
    }

    protected function fetchDefaultBranch(string $owner, string $repo, ?string $token): ?string
    {
        $request = Http::timeout(30);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}");

        if ($response->failed()) {
            Log::warning('[ReviewPluginRepository] Failed to fetch repo metadata', [
                'plugin_id' => $this->plugin->id,
                'status' => $response->status(),
            ]);

            return null;
        }

        return $response->json('default_branch', 'main');
    }

    /**
     * @return array<int, array{path: string, type: string}>
     */
    protected function fetchRepoTree(string $owner, string $repo, string $branch, ?string $token): array
    {
        $request = Http::timeout(30);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/git/trees/{$branch}", [
            'recursive' => '1',
        ]);

        if ($response->failed()) {
            Log::warning('[ReviewPluginRepository] Failed to fetch repo tree', [
                'plugin_id' => $this->plugin->id,
                'status' => $response->status(),
            ]);

            return [];
        }

        return $response->json('tree', []);
    }

    protected function fetchComposerJson(string $owner, string $repo, ?string $token): ?array
    {
        $request = Http::timeout(30);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/contents/composer.json");

        if ($response->failed()) {
            return null;
        }

        $content = $response->json('content');
        $encoding = $response->json('encoding');

        if ($encoding === 'base64' && $content) {
            $decoded = base64_decode($content);

            return json_decode($decoded, true);
        }

        return null;
    }

    protected function fetchNativephpJson(string $owner, string $repo, ?string $token): ?array
    {
        $request = Http::timeout(30);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/contents/nativephp.json");

        if ($response->failed()) {
            return null;
        }

        $content = $response->json('content');
        $encoding = $response->json('encoding');

        if ($encoding === 'base64' && $content) {
            $decoded = base64_decode($content);

            return json_decode($decoded, true);
        }

        return null;
    }

    protected function checkDirectoryHasFiles(array $tree, string $prefix): bool
    {
        foreach ($tree as $item) {
            $path = $item['path'] ?? '';
            $type = $item['type'] ?? '';

            if ($type === 'blob' && str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    protected function checkHasLicenseFile(array $tree): bool
    {
        $licenseNames = ['LICENSE', 'LICENSE.md', 'LICENSE.txt', 'license', 'license.md', 'license.txt'];

        foreach ($tree as $item) {
            $path = $item['path'] ?? '';
            $type = $item['type'] ?? '';

            if ($type === 'blob' && in_array($path, $licenseNames, true)) {
                return true;
            }
        }

        return false;
    }

    protected function fetchLatestTag(string $owner, string $repo, ?string $token): ?string
    {
        $request = Http::timeout(10);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/releases/latest");

        if ($response->successful()) {
            return $response->json('tag_name');
        }

        $tagsResponse = Http::timeout(10);

        if ($token) {
            $tagsResponse = $tagsResponse->withToken($token);
        }

        $tagsResponse = $tagsResponse->get("https://api.github.com/repos/{$owner}/{$repo}/tags", [
            'per_page' => 1,
        ]);

        if ($tagsResponse->successful() && count($tagsResponse->json()) > 0) {
            return $tagsResponse->json()[0]['name'];
        }

        return null;
    }
}
