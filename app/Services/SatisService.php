<?php

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SatisService
{
    protected string $apiUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.satis.url');
        $this->apiKey = config('services.satis.api_key');
    }

    /**
     * Trigger a full satis build with all approved plugins.
     */
    public function buildAll(): array
    {
        $plugins = $this->getApprovedPlugins();

        return $this->triggerBuild($plugins);
    }

    /**
     * Trigger a satis build for specific plugins.
     *
     * @param  array<int, Plugin>|\Illuminate\Support\Collection  $plugins
     */
    public function build($plugins): array
    {
        $pluginData = collect($plugins)->map(fn (Plugin $plugin) => [
            'name' => $plugin->name,
            'repository_url' => $plugin->repository_url,
            'is_official' => $plugin->is_official ?? false,
        ])->values()->all();

        return $this->triggerBuild($pluginData);
    }

    /**
     * Remove a package from satis.
     */
    public function removePackage(string $packageName): array
    {
        if (! $this->apiUrl || ! $this->apiKey) {
            return [
                'success' => false,
                'error' => 'Satis API not configured',
            ];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->delete("{$this->apiUrl}/api/packages/{$packageName}");

            if ($response->successful()) {
                Log::info('Satis package removal triggered', [
                    'package' => $packageName,
                    'job_id' => $response->json('job_id'),
                ]);

                return [
                    'success' => true,
                    'job_id' => $response->json('job_id'),
                    'message' => $response->json('message'),
                ];
            }

            Log::error('Satis package removal failed', [
                'package' => $packageName,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('error') ?? 'Unknown error',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Satis package removal exception', [
                'package' => $packageName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get all approved plugins formatted for satis.
     *
     * @return array<int, array{name: string, repository_url: string, type: string, is_official: bool}>
     */
    protected function getApprovedPlugins(): array
    {
        return Plugin::query()
            ->approved()
            ->get()
            ->map(fn (Plugin $plugin) => [
                'name' => $plugin->name,
                'repository_url' => $plugin->repository_url,
                'is_official' => $plugin->is_official ?? false,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{name: string, repository_url: string, type: string, is_official?: bool}>  $plugins
     */
    protected function triggerBuild(array $plugins): array
    {
        if (! $this->apiUrl || ! $this->apiKey) {
            return [
                'success' => false,
                'error' => 'Satis API not configured. Set SATIS_URL and SATIS_API_KEY in .env',
            ];
        }

        if (empty($plugins)) {
            return [
                'success' => false,
                'error' => 'No plugins to build',
            ];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->accept('application/json')
                ->timeout(30)
                ->post("{$this->apiUrl}/api/build", [
                    'plugins' => $plugins,
                ]);

            if ($response->successful()) {
                Log::info('Satis build triggered', [
                    'plugins_count' => count($plugins),
                    'job_id' => $response->json('job_id'),
                ]);

                return [
                    'success' => true,
                    'job_id' => $response->json('job_id'),
                    'message' => $response->json('message'),
                    'plugins_count' => count($plugins),
                ];
            }

            Log::error('Satis build trigger failed', [
                'url' => "{$this->apiUrl}/api/build",
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('error') ?? $response->body() ?: "HTTP {$response->status()}",
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Satis build trigger exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
