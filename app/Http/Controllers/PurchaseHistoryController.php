<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PurchaseHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();

        // Fetch subscription licenses
        $licenses = $user->licenses()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($license) {
                return [
                    'type' => 'subscription',
                    'name' => $license->name ?? $license->policy_name,
                    'description' => $license->name ? $license->policy_name : null,
                    'price' => null,
                    'currency' => null,
                    'purchased_at' => $license->created_at,
                    'expires_at' => $license->expires_at,
                    'is_active' => ! $license->is_suspended && (! $license->expires_at || $license->expires_at->isFuture()),
                    'href' => route('customer.licenses.show', $license->key),
                ];
            });

        // Fetch plugin licenses
        $pluginLicenses = $user->pluginLicenses()
            ->with('plugin', 'pluginBundle')
            ->orderBy('purchased_at', 'desc')
            ->get()
            ->map(function ($pluginLicense) {
                $name = $pluginLicense->plugin->name ?? 'Plugin';
                $description = null;

                if ($pluginLicense->wasPurchasedAsBundle() && $pluginLicense->pluginBundle) {
                    $description = 'Part of '.$pluginLicense->pluginBundle->name;
                }

                return [
                    'type' => 'plugin',
                    'name' => $name,
                    'description' => $description,
                    'price' => $pluginLicense->price_paid,
                    'currency' => $pluginLicense->currency,
                    'purchased_at' => $pluginLicense->purchased_at,
                    'expires_at' => $pluginLicense->expires_at,
                    'is_active' => $pluginLicense->isActive(),
                    'href' => $pluginLicense->plugin ? route('plugins.show', $pluginLicense->plugin->routeParams()) : null,
                    'is_grandfathered' => $pluginLicense->is_grandfathered,
                ];
            });

        // Combine and sort by purchased_at descending
        $purchases = $licenses->concat($pluginLicenses)
            ->sortByDesc('purchased_at')
            ->values();

        return view('customer.purchase-history.index', compact('purchases'));
    }
}
