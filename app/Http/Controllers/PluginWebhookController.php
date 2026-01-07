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

        $synced = $syncService->sync($plugin);

        if (! $synced) {
            return response()->json(['error' => 'Failed to sync plugin'], 500);
        }

        return response()->json([
            'success' => true,
            'synced_at' => $plugin->fresh()->last_synced_at->toIso8601String(),
        ]);
    }
}
