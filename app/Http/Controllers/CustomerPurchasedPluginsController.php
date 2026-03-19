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

        return view('customer.purchased-plugins.index', compact('pluginLicenses', 'pluginLicenseKey'));
    }
}
