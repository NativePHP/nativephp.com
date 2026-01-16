<?php

namespace App\Jobs;

use App\Models\PluginVersion;
use App\Services\PluginStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BuildPluginPackage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public int $timeout = 300;

    public function __construct(
        public PluginVersion $pluginVersion
    ) {}

    public function handle(PluginStorageService $storageService): void
    {
        $plugin = $this->pluginVersion->plugin;
        $repo = $plugin->getRepositoryOwnerAndName();

        if (! $repo) {
            Log::warning("Plugin {$plugin->id} has no valid repository URL");

            return;
        }

        $zipPath = $this->downloadReleaseArchive($repo['owner'], $repo['repo']);

        if (! $zipPath) {
            Log::error('Failed to download release archive', [
                'plugin_id' => $plugin->id,
                'version' => $this->pluginVersion->version,
            ]);

            return;
        }

        try {
            $storagePath = $storageService->uploadPackage(
                $plugin,
                $this->pluginVersion->version,
                $zipPath
            );

            $fileSize = filesize($zipPath);

            $this->pluginVersion->update([
                'storage_path' => $storagePath,
                'file_size' => $fileSize,
                'is_packaged' => true,
                'packaged_at' => now(),
            ]);

            Log::info('Plugin package built and uploaded', [
                'plugin_id' => $plugin->id,
                'version' => $this->pluginVersion->version,
                'storage_path' => $storagePath,
            ]);

            $this->triggerSatisBuild();
        } finally {
            @unlink($zipPath);
        }
    }

    protected function downloadReleaseArchive(string $owner, string $repo): ?string
    {
        $token = $this->getGitHubToken();
        $tagName = $this->pluginVersion->tag_name;

        $archiveUrl = "https://api.github.com/repos/{$owner}/{$repo}/zipball/{$tagName}";

        $request = Http::timeout(120)->withOptions([
            'stream' => true,
        ]);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get($archiveUrl);

        if ($response->failed()) {
            Log::warning('Failed to download release archive', [
                'url' => $archiveUrl,
                'status' => $response->status(),
            ]);

            return null;
        }

        $tempPath = storage_path('app/temp/plugin-'.uniqid().'.zip');

        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        file_put_contents($tempPath, $response->body());

        return $tempPath;
    }

    protected function triggerSatisBuild(): void
    {
        $satisUrl = config('services.satis.url');
        $satisApiKey = config('services.satis.api_key');

        if (! $satisUrl || ! $satisApiKey) {
            Log::info('Satis not configured, skipping build trigger');

            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$satisApiKey,
            ])
                ->timeout(30)
                ->post("{$satisUrl}/api/build");

            if ($response->failed()) {
                Log::warning('Failed to trigger Satis build', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to trigger Satis build', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function getGitHubToken(): ?string
    {
        $user = $this->pluginVersion->plugin->user;

        if ($user && $user->hasGitHubToken()) {
            return $user->getGitHubToken();
        }

        return config('services.github.token');
    }
}
