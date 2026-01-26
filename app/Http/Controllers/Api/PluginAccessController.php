<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluginAccessController extends Controller
{
    /**
     * Validate user credentials and return accessible plugins.
     *
     * Expects HTTP Basic Auth with:
     * - Username: user's email address
     * - Password: user's plugin_license_key
     */
    public function index(Request $request): JsonResponse
    {
        $email = $request->getUser();
        $licenseKey = $request->getPassword();

        if (! $email || ! $licenseKey) {
            return response()->json([
                'error' => 'Authentication required',
                'message' => 'Please provide email and license key via HTTP Basic Auth',
            ], 401);
        }

        $user = User::where('email', $email)
            ->where('plugin_license_key', $licenseKey)
            ->first();

        if (! $user) {
            return response()->json([
                'error' => 'Invalid credentials',
                'message' => 'The provided email or license key is incorrect',
            ], 401);
        }

        $accessiblePlugins = $this->getAccessiblePlugins($user);

        return response()->json([
            'success' => true,
            'user' => [
                'email' => $user->email,
            ],
            'plugins' => $accessiblePlugins,
        ]);
    }

    /**
     * Check if user has access to a specific plugin.
     */
    public function checkAccess(Request $request, string $vendor, string $package): JsonResponse
    {
        $email = $request->getUser();
        $licenseKey = $request->getPassword();

        if (! $email || ! $licenseKey) {
            return response()->json([
                'error' => 'Authentication required',
            ], 401);
        }

        $user = User::where('email', $email)
            ->where('plugin_license_key', $licenseKey)
            ->first();

        if (! $user) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        $packageName = "{$vendor}/{$package}";
        $plugin = Plugin::where('name', $packageName)->first();

        if (! $plugin) {
            return response()->json([
                'error' => 'Plugin not found',
            ], 404);
        }

        $hasAccess = $user->hasPluginAccess($plugin);

        return response()->json([
            'success' => true,
            'package' => $packageName,
            'has_access' => $hasAccess,
        ]);
    }

    /**
     * Get all paid plugins the user has access to (submitted or purchased).
     *
     * @return array<int, array{name: string, access: string}>
     */
    protected function getAccessiblePlugins(User $user): array
    {
        $plugins = [];

        // Admins have access to ALL paid plugins (including pending) for review
        if ($user->isAdmin()) {
            $allPaidPlugins = Plugin::query()
                ->where('type', \App\Enums\PluginType::Paid)
                ->whereNotNull('name')
                ->get(['name', 'status']);

            foreach ($allPaidPlugins as $plugin) {
                $plugins[] = [
                    'name' => $plugin->name,
                    'access' => 'admin',
                ];
            }

            return $plugins;
        }

        // Paid plugins the user has submitted
        $submittedPlugins = Plugin::query()
            ->where('user_id', $user->id)
            ->where('type', \App\Enums\PluginType::Paid)
            ->get(['name']);

        foreach ($submittedPlugins as $plugin) {
            $plugins[] = [
                'name' => $plugin->name,
                'access' => 'author',
            ];
        }

        // Paid plugins the user has purchased (has licenses for)
        $licensedPlugins = $user->pluginLicenses()
            ->active()
            ->with('plugin:id,name')
            ->get()
            ->pluck('plugin')
            ->filter()
            ->unique('id');

        foreach ($licensedPlugins as $plugin) {
            // Avoid duplicates if user is also the author
            if (! collect($plugins)->contains('name', $plugin->name)) {
                $plugins[] = [
                    'name' => $plugin->name,
                    'access' => 'purchased',
                ];
            }
        }

        return $plugins;
    }
}
