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

        // Dashboard summary data
        $licenseCount = $user->licenses()->count();
        $isEapCustomer = $user->isEapCustomer();
        $activeSubscription = $user->subscription();
        $pluginLicenseCount = $user->pluginLicenses()->count();

        // Get subscription plan name
        $subscriptionName = null;
        if ($activeSubscription) {
            try {
                $subscriptionName = \App\Enums\Subscription::fromStripePriceId($activeSubscription->stripe_price)->name();
            } catch (\RuntimeException) {
                $subscriptionName = ucfirst($activeSubscription->type);
            }
        }

        // For renewal CTA when no subscription
        $renewalLicenseKey = null;
        if (! $activeSubscription) {
            $highestTierLicense = $user->licenses()
                ->whereIn('policy_name', ['max', 'pro', 'mini'])
                ->orderByRaw("FIELD(policy_name, 'max', 'pro', 'mini')")
                ->first();
            $renewalLicenseKey = $highestTierLicense?->key;
        }

        // Connected accounts info
        $hasGitHub = $user->hasGitHubToken();
        $hasDiscord = $user->hasDiscordConnected();
        $connectedAccountsCount = ($hasGitHub ? 1 : 0) + ($hasDiscord ? 1 : 0);
        $connectedAccountsDescription = match (true) {
            $hasGitHub && $hasDiscord => 'GitHub & Discord',
            $hasGitHub => 'GitHub connected',
            $hasDiscord => 'Discord connected',
            default => 'No accounts connected',
        };

        // Total purchases (licenses + plugins)
        $totalPurchases = $licenseCount + $pluginLicenseCount;

        return view('customer.dashboard', compact(
            'licenseCount',
            'isEapCustomer',
            'activeSubscription',
            'subscriptionName',
            'pluginLicenseCount',
            'renewalLicenseKey',
            'connectedAccountsCount',
            'connectedAccountsDescription',
            'totalPurchases'
        ));
    }

    public function list(): View
    {
        $user = Auth::user();
        $licenses = $user->licenses()->orderBy('created_at', 'desc')->get();

        // Fetch sub-licenses assigned to this user's email (excluding those from licenses they own)
        $assignedSubLicenses = SubLicense::query()
            ->with('parentLicense')
            ->where('assigned_email', $user->email)
            ->whereHas('parentLicense', function ($query) use ($user): void {
                $query->where('user_id', '!=', $user->id);
            })->latest()
            ->get();

        return view('customer.licenses.list', compact('licenses', 'assignedSubLicenses'));
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

        return to_route('customer.licenses.show', $licenseKey)
            ->with('success', 'License name updated successfully!');
    }

    public function rotatePluginLicenseKey(): RedirectResponse
    {
        $user = Auth::user();
        $user->regeneratePluginLicenseKey();

        return to_route('customer.purchased-plugins.index')
            ->with('success', 'Your plugin license key has been rotated. Please update your Composer configuration with the new key.');
    }

    public function claimFreePlugins(): RedirectResponse
    {
        $user = Auth::user();

        // Check if offer has expired
        if (now()->gt('2026-02-28 23:59:59')) {
            return to_route('dashboard')
                ->with('error', 'This offer has expired.');
        }

        // Verify eligibility
        if (! $user->isEligibleForFreePluginsOffer()) {
            return to_route('dashboard')
                ->with('error', 'You are not eligible for this offer.');
        }

        // Get the free plugins
        $freePlugins = Plugin::query()
            ->whereIn('name', User::FREE_PLUGINS_OFFER)
            ->get();

        if ($freePlugins->isEmpty()) {
            return to_route('dashboard')
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
            return to_route('dashboard')
                ->with('message', 'You have already claimed all the free plugins.');
        }

        return to_route('dashboard')
            ->with('success', "Successfully claimed {$claimedCount} free plugin(s)! You can now install them via Composer.");
    }
}
