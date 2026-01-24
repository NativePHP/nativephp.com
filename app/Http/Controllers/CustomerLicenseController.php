<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\SubLicense;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerLicenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();
        $licenses = $user->licenses()->orderBy('created_at', 'desc')->get();

        // Ensure user has a plugin license key (generates one if missing)
        $pluginLicenseKey = $user->getPluginLicenseKey();

        // Fetch sub-licenses assigned to this user's email (excluding those from licenses they own)
        $assignedSubLicenses = SubLicense::query()
            ->with('parentLicense')
            ->where('assigned_email', $user->email)
            ->whereHas('parentLicense', function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch plugin licenses (purchased plugins)
        $pluginLicenses = $user->pluginLicenses()
            ->with('plugin')
            ->orderBy('purchased_at', 'desc')
            ->get();

        return view('customer.licenses.index', compact('licenses', 'assignedSubLicenses', 'pluginLicenses', 'pluginLicenseKey'));
    }

    public function show(string $licenseKey): View
    {
        $user = Auth::user();
        $license = $user->licenses()
            ->with('subLicenses')
            ->where('key', $licenseKey)
            ->firstOrFail();

        return view('customer.licenses.show', compact('license'));
    }

    public function update(Request $request, string $licenseKey): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $license->update([
            'name' => $request->name,
        ]);

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', 'License name updated successfully!');
    }

    public function rotatePluginLicenseKey(): RedirectResponse
    {
        $user = Auth::user();
        $user->regeneratePluginLicenseKey();

        return redirect()->route('dashboard')
            ->with('success', 'Your plugin license key has been rotated. Please update your Composer configuration with the new key.');
    }

    public function claimFreePlugins(): RedirectResponse
    {
        $user = Auth::user();

        // Check if offer has expired
        if (now()->gt('2026-02-28 23:59:59')) {
            return redirect()->route('dashboard')
                ->with('error', 'This offer has expired.');
        }

        // Verify eligibility
        if (! $user->isEligibleForFreePluginsOffer()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not eligible for this offer.');
        }

        // Get the free plugins
        $freePlugins = Plugin::query()
            ->whereIn('name', User::FREE_PLUGINS_OFFER)
            ->get();

        if ($freePlugins->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'The free plugins are not currently available.');
        }

        $claimedCount = 0;

        foreach ($freePlugins as $plugin) {
            // Skip if user already has a license for this plugin
            $existingLicense = $user->pluginLicenses()
                ->where('plugin_id', $plugin->id)
                ->exists();

            if ($existingLicense) {
                continue;
            }

            // Create the plugin license
            PluginLicense::create([
                'user_id' => $user->id,
                'plugin_id' => $plugin->id,
                'price_paid' => 0,
                'currency' => 'USD',
                'is_grandfathered' => true,
                'purchased_at' => now(),
            ]);

            $claimedCount++;
        }

        if ($claimedCount === 0) {
            return redirect()->route('dashboard')
                ->with('message', 'You have already claimed all the free plugins.');
        }

        return redirect()->route('dashboard')
            ->with('success', "Successfully claimed {$claimedCount} free plugin(s)! You can now install them via Composer.");
    }
}
