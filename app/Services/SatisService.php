<?php

namespace App\Services;

use App\Enums\PluginType;
use App\Jobs\Concerns\ResolvesGitHubToken;
use App\Models\Plugin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SatisService
{
    use ResolvesGitHubToken;

    protected string $apiUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.satis.url');
        $this->apiKey = config('services.satis.api_key');
    }

    /**
     * Rebuild every approved paid plugin.
     *
     * Each plugin is built individually using its owner's GitHub token. A single
     * satis build can only authenticate to github.com as one identity, but the
     * plugins live in private repos across different owners/orgs — so one shared
     * token can't clone them all and falls back to unauthenticated requests that
     * hit the 60/hour rate limit. Per-plugin builds are partial (merging) on the
     * satis side, so a failure never overwrites the published index with an
     * incomplete set.
     */
    public function buildAll(): array
    {
        $plugins = Plugin::query()
            ->approved()
            ->where('type', PluginType::Paid)
            ->with('user')
            ->get();

        if ($plugins->isEmpty()) {
            return [
                'success' => false,
                'error' => 'No plugins to build',
                'plugins_count' => 0,
                'failed' => [],
                'results' => [],
            ];
        }

        $results = [];
        $failed = [];

        foreach ($plugins as $plugin) {
            $result = $this->buildForPlugin($plugin);
            $results[$plugin->name] = $result;

            if (! ($result['success'] ?? false)) {
                $failed[] = $plugin->name;
            }
        }

        return [
            'success' => empty($failed),
            'plugins_count' => $plugins->count(),
            'failed' => $failed,
            'results' => $results,
        ];
    }

    /**
     * Build a single plugin using its owner's GitHub token.
     */
    public function buildForPlugin(Plugin $plugin): array
    {
        return $this->build([$plugin], $this->resolveGitHubTokenFor($plugin));
    }

    /**
     * Trigger a satis build for specific plugins.
     *
     * @param  array<int, Plugin>|Collection  $plugins
     */
    public function build($plugins, ?string $githubToken = null): array
    {
        $pluginData = collect($plugins)->map(fn (Plugin $plugin) => [
            'name' => $plugin->name,
            'repository_url' => $plugin->repository_url,
            'is_official' => $plugin->is_official ?? false,
        ])->values()->all();

        return $this->triggerBuild($pluginData, $githubToken);
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
     * @param  array<int, array{name: string, repository_url: string, type: string, is_official?: bool}>  $plugins
     */
    protected function triggerBuild(array $plugins, ?string $githubToken = null, bool $fullBuild = false): array
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

        $githubToken ??= config('services.github.token');

        try {
            $payload = [
                'plugins' => $plugins,
                'full_build' => $fullBuild,
            ];

            if ($githubToken) {
                $payload['github_token'] = $githubToken;
            }

            $response = Http::withToken($this->apiKey)
                ->accept('application/json')
                ->timeout(30)
                ->post("{$this->apiUrl}/api/build", $payload);

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
