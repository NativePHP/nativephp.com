<?php

namespace App\Http\Middleware;

use App\Enums\GrandfatheringTier;
use App\Enums\PluginType;
use App\Models\Plugin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PluginAccessCheck
{
    /**
     * Handle an incoming request.
     *
     * Checks if the authenticated user has access to the requested plugin.
     * Expects the plugin to be available via route model binding or route parameters.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response('Authentication required', 401);
        }

        $plugin = $this->resolvePlugin($request);

        if (! $plugin) {
            return response('Plugin not found', 404);
        }

        if (! $this->userHasAccess($user, $plugin)) {
            return response('You do not have access to this plugin', 403);
        }

        $request->attributes->set('plugin', $plugin);

        return $next($request);
    }

    protected function resolvePlugin(Request $request): ?Plugin
    {
        if ($request->route('plugin') instanceof Plugin) {
            return $request->route('plugin');
        }

        $vendor = $request->route('vendor');
        $package = $request->route('package');

        if ($vendor && $package) {
            $packageName = "{$vendor}/{$package}";

            return Plugin::where('name', $packageName)->approved()->first();
        }

        return null;
    }

    protected function userHasAccess(User $user, Plugin $plugin): bool
    {
        if ($plugin->type === PluginType::Free) {
            return true;
        }

        if ($user->pluginLicenses()->forPlugin($plugin)->active()->exists()) {
            return true;
        }

        if ($plugin->is_official && $this->userHasGrandfatheredAccess($user)) {
            return true;
        }

        return false;
    }

    protected function userHasGrandfatheredAccess(User $user): bool
    {
        $purchaseHistory = $user->purchaseHistory;

        if (! $purchaseHistory) {
            return false;
        }

        return $purchaseHistory->grandfathering_tier === GrandfatheringTier::FreeOfficialPlugins;
    }
}
