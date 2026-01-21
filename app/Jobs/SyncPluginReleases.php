<?php

namespace App\Jobs;

use App\Models\Plugin;
use App\Models\PluginVersion;
use App\Services\SatisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPluginReleases implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    protected bool $hasNewReleases = false;

    public function __construct(
        public Plugin $plugin,
        public bool $triggerSatisBuild = true
    ) {}

    public function handle(SatisService $satisService): void
    {
        $repo = $this->plugin->getRepositoryOwnerAndName();

        if (! $repo) {
            Log::warning("Plugin {$this->plugin->id} has no valid repository URL");

            return;
        }

        $token = $this->getGitHubToken();

        $releases = $this->fetchReleases($repo['owner'], $repo['repo'], $token);

        foreach ($releases as $release) {
            $this->processRelease($release);
        }

        $this->plugin->update(['last_synced_at' => now()]);

        // Trigger satis build if we have new releases
        if ($this->triggerSatisBuild && $this->hasNewReleases && $this->plugin->isPaid()) {
            $satisService->build([$this->plugin]);
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
            Log::warning("Failed to fetch releases for {$owner}/{$repo}", [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [];
        }

        return $response->json();
    }

    protected function processRelease(array $release): void
    {
        $tagName = $release['tag_name'];
        $version = ltrim($tagName, 'v');

        $existingVersion = $this->plugin->versions()
            ->where('tag_name', $tagName)
            ->first();

        if ($existingVersion) {
            return;
        }

        PluginVersion::create([
            'plugin_id' => $this->plugin->id,
            'version' => $version,
            'tag_name' => $tagName,
            'release_notes' => $release['body'] ?? null,
            'github_release_id' => (string) $release['id'],
            'commit_sha' => $release['target_commitish'] ?? null,
            'published_at' => $release['published_at'] ? now()->parse($release['published_at']) : null,
        ]);

        Log::info('Created plugin version', [
            'plugin_id' => $this->plugin->id,
            'version' => $version,
        ]);

        $this->hasNewReleases = true;
    }

    protected function getGitHubToken(): ?string
    {
        $user = $this->plugin->user;

        if ($user && $user->hasGitHubToken()) {
            return $user->getGitHubToken();
        }

        return config('services.github.token');
    }
}
