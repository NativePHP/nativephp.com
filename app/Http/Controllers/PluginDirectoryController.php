<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Models\PluginBundle;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PluginDirectoryController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $featuredPlugins = Plugin::query()
            ->approved()
            ->where('is_active', true)
            ->featured()
            ->latest()
            ->take(16)
            ->get()
            ->filter(fn (Plugin $plugin) => $plugin->isFree() || $plugin->hasAccessiblePriceFor($user))
            ->take(8);

        $latestPlugins = Plugin::query()
            ->approved()
            ->where('is_active', true)
            ->where('featured', false)
            ->latest()
            ->take(16)
            ->get()
            ->filter(fn (Plugin $plugin) => $plugin->isFree() || $plugin->hasAccessiblePriceFor($user))
            ->take(8);

        $bundles = PluginBundle::query()
            ->active()
            ->with('plugins')
            ->latest()
            ->get()
            ->filter(fn (PluginBundle $bundle) => $bundle->hasAccessiblePriceFor($user));

        return view('plugins', [
            'featuredPlugins' => $featuredPlugins,
            'latestPlugins' => $latestPlugins,
            'bundles' => $bundles,
        ]);
    }

    public function show(string $vendor, string $package): View
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        $user = Auth::user();

        $isAdmin = $user?->isAdmin() ?? false;
        $isOwner = $user && $plugin->user_id === $user->id;

        abort_unless(($plugin->isApproved() && $plugin->is_active) || $isAdmin || $isOwner, 404);

        // For paid plugins, check if user has an accessible price (admins and owners bypass)
        if (! $isAdmin && ! $isOwner && $plugin->isPaid() && ! $plugin->hasAccessiblePriceFor($user)) {
            abort(404);
        }

        $bundles = $plugin->bundles()
            ->active()
            ->get()
            ->filter(fn (PluginBundle $bundle) => $bundle->hasAccessiblePriceFor($user));

        $bestPrice = $plugin->getBestPriceForUser($user);
        $regularPrice = $plugin->getRegularPrice();

        return view('plugin-show', [
            'plugin' => $plugin,
            'bundles' => $bundles,
            'bestPrice' => $bestPrice,
            'regularPrice' => $regularPrice,
            'hasDiscount' => $bestPrice && $regularPrice && $bestPrice->id !== $regularPrice->id,
            'isAdminPreview' => (! $plugin->isApproved() || ! $plugin->is_active) && ($isAdmin || $isOwner),
        ]);
    }

    public function license(string $vendor, string $package): View
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        abort_unless($plugin->isApproved(), 404);
        abort_unless($plugin->isPaid(), 404);
        abort_unless($plugin->license_html, 404);

        $user = Auth::user();

        // For paid plugins, check if user has an accessible price
        if (! $plugin->hasAccessiblePriceFor($user)) {
            abort(404);
        }

        return view('plugin-license', [
            'plugin' => $plugin,
        ]);
    }
}
