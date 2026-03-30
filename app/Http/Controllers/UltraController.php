<?php

namespace App\Http\Controllers;

use App\Enums\PluginStatus;
use App\Enums\PluginType;
use App\Models\Plugin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UltraController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user->hasActiveUltraSubscription()) {
            return to_route('pricing');
        }

        $subscription = $user->subscription();

        $plugins = Plugin::query()
            ->where('is_official', true)
            ->where('is_active', true)
            ->where('status', PluginStatus::Approved)
            ->where('type', PluginType::Paid)
            ->orderBy('name')
            ->get();

        return view('customer.ultra.index', [
            'subscription' => $subscription,
            'plugins' => $plugins,
        ]);
    }
}
