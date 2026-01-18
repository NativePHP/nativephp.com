<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Models\PluginBundle;
use Illuminate\View\View;

class PluginDirectoryController extends Controller
{
    public function index(): View
    {
        $featuredPlugins = Plugin::query()
            ->approved()
            ->featured()
            ->latest()
            ->take(3)
            ->get();

        $latestPlugins = Plugin::query()
            ->approved()
            ->where('featured', false)
            ->latest()
            ->take(3)
            ->get();

        $bundles = PluginBundle::query()
            ->active()
            ->with('plugins')
            ->latest()
            ->get();

        return view('plugins', [
            'featuredPlugins' => $featuredPlugins,
            'latestPlugins' => $latestPlugins,
            'bundles' => $bundles,
        ]);
    }

    public function show(Plugin $plugin): View
    {
        abort_unless($plugin->isApproved(), 404);

        $bundles = $plugin->bundles()->active()->get();

        return view('plugin-show', [
            'plugin' => $plugin,
            'bundles' => $bundles,
        ]);
    }
}
