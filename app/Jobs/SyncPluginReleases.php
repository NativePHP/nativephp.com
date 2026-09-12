<?php

namespace App\Jobs;

use App\Enums\PermissionExpansionMode;
use App\Enums\PluginActivityType;
use App\Models\Plugin;
use App\Models\PluginVersion;
use App\Services\PluginManifestParser;
use App\Services\SatisService;
use App\Support\CommonMark\CommonMark;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPluginReleases implements ShouldQueue
{
    use Concerns\ResolvesGitHubToken, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    protected bool $hasNewReleases = false;

    public function __construct(
        public Plugin $plugin,
        public bool $triggerSatisBuild = true
    ) {}

    public function handle(SatisService $satisService, PluginManifestParser $manifestParser): void
    {
        Log::info('[SyncPluginReleases] Starting sync', [
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'plugin_type' => $this->plugin->type?->value,
            'repository_url' => $this->plugin->repository_url,
            'trigger_satis_build' => $this->triggerSatisBuild,
        ]);

        $repo = $this->plugin->getRepositoryOwnerAndName();

        if (! $repo) {
            Log::warning('[SyncPluginReleases] No valid repository URL, aborting', [
                'plugin_id' => $this->plugin->id,
                'plugin_name' => $this->plugin->name,
                'repository_url' => $this->plugin->repository_url,
            ]);

            return;
        }

        $token = $this->getGitHubToken();

        Log::info('[SyncPluginReleases] Fetching releases from GitHub', [
            'plugin_id' => $this->plugin->id,
            'owner' => $repo['owner'],
            'repo' => $repo['repo'],
            'token_source' => $token ? ($this->plugin->user?->hasGitHubToken() ? 'user_oauth' : 'platform') : 'none',
        ]);

        $releases = $this->fetchReleases($repo['owner'], $repo['repo'], $token);

        Log::info('[SyncPluginReleases] Fetched releases', [
            'plugin_id' => $this->plugin->id,
            'release_count' => count($releases),
            'tags' => collect($releases)->pluck('tag_name')->all(),
        ]);

        $newCount = 0;
        $skippedCount = 0;

        foreach ($releases as $release) {
            if ($this->processRelease($release, $repo['owner'], $repo['repo'], $token, $manifestParser)) {
                $newCount++;
            } else {
                $skippedCount++;
            }
        }

        $updateData = ['last_synced_at' => now()];

        if ($this->hasNewReleases) {
            $latestVersion = $this->plugin->versions()->visible()->latest('published_at')->first();

            if ($latestVersion) {
                $updateData['latest_version'] = $latestVersion->version;
            }
        }

        $this->plugin->update($updateData);

        Log::info('[SyncPluginReleases] Processing complete', [
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'new_releases' => $newCount,
            'skipped_existing' => $skippedCount,
            'has_new_releases' => $this->hasNewReleases,
        ]);

        if ($this->triggerSatisBuild && $this->plugin->isPaid()) {
            Log::info('[SyncPluginReleases] Triggering Satis build', [
                'plugin_id' => $this->plugin->id,
                'plugin_name' => $this->plugin->name,
            ]);

            $result = $satisService->build([$this->plugin], $this->getGitHubToken());

            if ($result['success'] ?? false) {
                $this->plugin->update(['satis_synced_at' => now()]);
            }

            Log::info('[SyncPluginReleases] Satis build result', [
                'plugin_id' => $this->plugin->id,
                'result' => $result,
            ]);
        } elseif ($this->triggerSatisBuild && $this->hasNewReleases && ! $this->plugin->isPaid()) {
            Log::info('[SyncPluginReleases] Skipping Satis build - plugin is not paid', [
                'plugin_id' => $this->plugin->id,
                'plugin_name' => $this->plugin->name,
                'plugin_type' => $this->plugin->type?->value,
            ]);
        } elseif (! $this->hasNewReleases) {
            Log::info('[SyncPluginReleases] Skipping Satis build - no new releases', [
                'plugin_id' => $this->plugin->id,
                'plugin_name' => $this->plugin->name,
            ]);
        }
    }

    protected function fetchReleases(string $owner, string $repo, ?string $token): array
    {
        $request = Http::timeout(30);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/releases", [
            'per_page' => 30,
        ]);

        if ($response->failed()) {
            Log::error('[SyncPluginReleases] GitHub API request failed', [
                'plugin_id' => $this->plugin->id,
                'owner' => $owner,
                'repo' => $repo,
                'status' => $response->status(),
                'response' => $response->json(),
                'rate_limit_remaining' => $response->header('X-RateLimit-Remaining'),
            ]);

            return [];
        }

        Log::debug('[SyncPluginReleases] GitHub API response', [
            'plugin_id' => $this->plugin->id,
            'status' => $response->status(),
            'rate_limit_remaining' => $response->header('X-RateLimit-Remaining'),
        ]);

        return $response->json() ?? [];
    }

    protected function processRelease(array $release, string $owner, string $repo, ?string $token, PluginManifestParser $manifestParser): bool
    {
        $tagName = $release['tag_name'];
        $version = ltrim($tagName, 'v');

        $existingVersion = $this->plugin->versions()
            ->where('tag_name', $tagName)
            ->first();

        if ($existingVersion) {
            Log::debug('[SyncPluginReleases] Skipping existing version', [
                'plugin_id' => $this->plugin->id,
                'tag' => $tagName,
            ]);

            return false;
        }

        $previousManifest = $this->plugin->versions()->visible()->latest('published_at')->first()?->manifest_permissions;

        $nativephpJson = $this->fetchNativephpJsonAtRef($owner, $repo, $tagName, $token);
        $manifest = $manifestParser->permissionManifest($nativephpJson);
        $expanded = $manifestParser->hasExpandedPermissions($previousManifest, $manifest);
        $requiresReview = $expanded && PermissionExpansionMode::from(config('plugins.permission_expansion_mode')) === PermissionExpansionMode::Gate;

        PluginVersion::create([
            'plugin_id' => $this->plugin->id,
            'version' => $version,
            'tag_name' => $tagName,
            'release_notes' => $release['body'] ?? null,
            'release_notes_html' => filled($release['body'] ?? null) ? CommonMark::convertToHtml($release['body']) : null,
            'github_release_id' => (string) $release['id'],
            'commit_sha' => $release['target_commitish'] ?? null,
            'published_at' => $release['published_at'] ? now()->parse($release['published_at']) : null,
            'manifest_permissions' => $manifest,
            'permissions_expanded' => $expanded,
            'requires_review' => $requiresReview,
        ]);

        Log::info('[SyncPluginReleases] Created new version', [
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'version' => $version,
            'tag' => $tagName,
            'commit_sha' => $release['target_commitish'] ?? null,
            'published_at' => $release['published_at'] ?? null,
            'permissions_expanded' => $expanded,
            'requires_review' => $requiresReview,
        ]);

        if ($expanded) {
            $this->plugin->activities()->create([
                'type' => PluginActivityType::PermissionsExpanded,
                'from_status' => $this->plugin->status->value,
                'to_status' => $this->plugin->status->value,
                'note' => $requiresReview
                    ? "Version {$version} adds new permissions/entitlements and is held for admin review."
                    : "Version {$version} adds new permissions/entitlements.",
                'causer_id' => null,
            ]);
        }

        $this->hasNewReleases = true;

        return true;
    }

    protected function fetchNativephpJsonAtRef(string $owner, string $repo, string $ref, ?string $token): ?array
    {
        $request = Http::timeout(15);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$owner}/{$repo}/contents/nativephp.json", [
            'ref' => $ref,
        ]);

        if ($response->failed()) {
            return null;
        }

        $content = $response->json('content');
        $encoding = $response->json('encoding');

        if ($encoding !== 'base64' || ! $content) {
            return null;
        }

        return json_decode(base64_decode($content), true);
    }
}
