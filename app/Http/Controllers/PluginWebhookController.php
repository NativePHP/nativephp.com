<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Services\PluginSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluginWebhookController extends Controller
{
    public function __invoke(Request $request, string $secret, PluginSyncService $syncService): JsonResponse
    {
        $plugin = Plugin::where('webhook_secret', $secret)->first();

        if (! $plugin) {
            return response()->json(['error' => 'Invalid webhook secret'], 404);
        }

        if (! $plugin->isApproved()) {
            return response()->json(['error' => 'Plugin is not approved'], 403);
        }

        $event = $request->header('X-GitHub-Event');

        if ($event === 'release') {
            // Sync plugin metadata to update latest_version
            $syncService->sync($plugin);

            // Queue release sync for version records
            dispatch(new \App\Jobs\SyncPluginReleases($plugin));

            return response()->json([
                'success' => true,
                'message' => 'Release sync queued',
                'synced_at' => $plugin->fresh()->last_synced_at->toIso8601String(),
            ]);
        }

        if ($event === 'push') {
            $synced = $syncService->sync($plugin);

            if (! $synced) {
                return response()->json(['error' => 'Failed to sync plugin'], 500);
            }

            return response()->json([
                'success' => true,
                'synced_at' => $plugin->fresh()->last_synced_at->toIso8601String(),
            ]);
        }

        $synced = $syncService->sync($plugin);

        if (! $synced) {
            return response()->json(['error' => 'Failed to sync plugin'], 500);
        }

        dispatch(new \App\Jobs\SyncPluginReleases($plugin));

        return response()->json([
            'success' => true,
            'synced_at' => $plugin->fresh()->last_synced_at->toIso8601String(),
            'releases_sync' => 'queued',
        ]);
    }
}
