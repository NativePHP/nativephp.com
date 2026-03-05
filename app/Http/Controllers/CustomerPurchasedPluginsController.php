<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerPurchasedPluginsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();

        $pluginLicenseKey = $user->getPluginLicenseKey();

        $pluginLicenses = $user->pluginLicenses()
            ->with('plugin', 'pluginBundle')
            ->orderBy('purchased_at', 'desc')
            ->get();

        // Team plugins for team members
        $teamPlugins = collect();
        $teamOwnerName = null;
        $teamMembership = $user->teamMembership;

        if ($teamMembership) {
            $teamOwner = $teamMembership->team->owner;
            $teamOwnerName = $teamOwner->display_name;
            $teamPlugins = $teamOwner->pluginLicenses()
                ->active()
                ->with('plugin')
                ->get()
                ->pluck('plugin')
                ->filter()
                ->unique('id');
        }

        return view('customer.purchased-plugins.index', compact(
            'pluginLicenses',
            'pluginLicenseKey',
            'teamPlugins',
            'teamOwnerName',
        ));
    }
}
