<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Services\PluginStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PluginRepositoryController extends Controller
{
    public function __construct(protected PluginStorageService $storageService) {}

    public function packagesJson(): Response
    {
        $content = $this->storageService->getPackagesJsonContent();

        if (! $content) {
            return response('Repository not found', 404);
        }

        return response($content, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function packageMetadata(Request $request, string $vendor, string $package): Response
    {
        $content = $this->storageService->getPackageMetadataContent($vendor, $package);

        if (! $content) {
            return response('Package not found', 404);
        }

        return response($content, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function download(Request $request, string $vendor, string $package, string $version): Response
    {
        $plugin = $request->attributes->get('plugin');

        if (! $plugin instanceof Plugin) {
            $pluginName = "{$vendor}/{$package}";
            $plugin = Plugin::where('name', $pluginName)->approved()->first();

            if (! $plugin) {
                return response('Package not found', 404);
            }
        }

        $version = str_replace('.zip', '', $version);

        if (! $this->storageService->packageExists($plugin, $version)) {
            return response('Version not found', 404);
        }

        try {
            $signedUrl = $this->storageService->generateSignedUrl($plugin, $version);

            Log::info('Plugin package download', [
                'plugin_id' => $plugin->id,
                'plugin_name' => $plugin->name,
                'version' => $version,
                'user_id' => $request->user()?->id,
            ]);

            return response('', 302, [
                'Location' => $signedUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate signed URL for plugin download', [
                'plugin_id' => $plugin->id,
                'version' => $version,
                'error' => $e->getMessage(),
            ]);

            return response('Download failed', 500);
        }
    }
}
