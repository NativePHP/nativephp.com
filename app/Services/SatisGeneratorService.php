<?php

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SatisGeneratorService
{
    public function generate(?Plugin $specificPlugin = null): bool
    {
        $plugins = $this->getPluginsForSatis($specificPlugin);

        if ($plugins->isEmpty()) {
            Log::info('No plugins to include in Satis repository');

            return true;
        }

        return $this->triggerSatisBuild($plugins);
    }

    public function rebuildAll(): bool
    {
        return $this->generate();
    }

    public function rebuildPlugin(Plugin $plugin): bool
    {
        return $this->generate($plugin);
    }

    /**
     * @return Collection<int, Plugin>
     */
    protected function getPluginsForSatis(?Plugin $specificPlugin = null): Collection
    {
        $query = Plugin::query()
            ->approved()
            ->where('satis_included', true);

        if ($specificPlugin) {
            $query->where('id', $specificPlugin->id);
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, Plugin>  $plugins
     */
    protected function triggerSatisBuild(Collection $plugins): bool
    {
        $satisApiUrl = config('services.satis.url');
        $satisApiKey = config('services.satis.api_key');

        if (! $satisApiUrl || ! $satisApiKey) {
            Log::error('Satis API not configured', [
                'url_set' => ! empty($satisApiUrl),
                'key_set' => ! empty($satisApiKey),
            ]);

            return false;
        }

        $payload = [
            'plugins' => $plugins->map(function (Plugin $plugin) {
                return [
                    'name' => $plugin->name,
                    'repository_url' => $plugin->repository_url,
                    'type' => $plugin->type->value,
                    'is_official' => $plugin->is_official,
                ];
            })->values()->all(),
        ];

        try {
            $response = Http::timeout(30)
                ->withToken($satisApiKey)
                ->post("{$satisApiUrl}/api/build", $payload);

            if ($response->successful()) {
                Log::info('Satis build triggered successfully', [
                    'plugins_count' => $plugins->count(),
                    'job_id' => $response->json('job_id'),
                ]);

                return true;
            }

            Log::error('Satis API returned error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to trigger Satis build', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function markPluginForInclusion(Plugin $plugin): void
    {
        $plugin->update(['satis_included' => true]);
    }

    public function removePluginFromInclusion(Plugin $plugin): void
    {
        $plugin->update(['satis_included' => false]);

        $this->triggerPluginRemoval($plugin);
    }

    protected function triggerPluginRemoval(Plugin $plugin): bool
    {
        $satisApiUrl = config('services.satis.url');
        $satisApiKey = config('services.satis.api_key');

        if (! $satisApiUrl || ! $satisApiKey) {
            return false;
        }

        try {
            $response = Http::timeout(30)
                ->withToken($satisApiKey)
                ->delete("{$satisApiUrl}/api/packages/{$plugin->name}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to trigger plugin removal from Satis', [
                'plugin' => $plugin->name,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
