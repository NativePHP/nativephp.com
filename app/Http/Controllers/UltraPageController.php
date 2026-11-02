<?php

namespace App\Http\Controllers;

use App\Enums\PluginType;
use App\Models\Plugin;
use Illuminate\View\View;

class UltraPageController extends Controller
{
    public function __invoke(): View
    {
        $includedPlugins = Plugin::query()
            ->approved()
            ->where('is_official', true)
            ->where('type', PluginType::Paid)
            ->with('activePrice')
            ->orderBy('name')
            ->get();

        return view('pricing', [
            'includedPlugins' => $includedPlugins,
            'includedPluginsValue' => (int) round($includedPlugins->sum(fn (Plugin $plugin): int => $plugin->activePrice?->amount ?? 0) / 100),
        ]);
    }
}
