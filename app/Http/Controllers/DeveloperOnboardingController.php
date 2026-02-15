<?php

namespace App\Http\Controllers;

use App\Services\StripeConnectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeveloperOnboardingController extends Controller
{
    public function __construct(protected StripeConnectService $stripeConnectService) {}

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $developerAccount = $user->developerAccount;

        if ($developerAccount && $developerAccount->hasCompletedOnboarding()) {
            return to_route('customer.developer.dashboard')
                ->with('message', 'Your developer account is already set up.');
        }

        return view('customer.developer.onboarding', [
            'developerAccount' => $developerAccount,
            'hasExistingAccount' => $developerAccount !== null,
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $user = $request->user();
        $developerAccount = $user->developerAccount;

        if (! $developerAccount) {
            $developerAccount = $this->stripeConnectService->createConnectAccount($user);
        }

        try {
            $onboardingUrl = $this->stripeConnectService->createOnboardingLink($developerAccount);

            return redirect($onboardingUrl);
        } catch (\Exception $e) {
            return to_route('customer.developer.onboarding')
                ->with('error', 'Unable to start onboarding. Please try again.');
        }
    }

    public function return(Request $request): RedirectResponse
    {
        $user = $request->user();
        $developerAccount = $user->developerAccount;

        if (! $developerAccount) {
            return to_route('customer.developer.onboarding')
                ->with('error', 'Developer account not found.');
        }

        $this->stripeConnectService->refreshAccountStatus($developerAccount);

        if ($developerAccount->hasCompletedOnboarding()) {
            // Link any existing paid plugins that don't have a developer account
            $user->plugins()
                ->where('type', 'paid')
                ->whereNull('developer_account_id')
                ->update(['developer_account_id' => $developerAccount->id]);

            return to_route('customer.developer.dashboard')
                ->with('success', 'Your developer account is now active!');
        }

        return to_route('customer.developer.onboarding')
            ->with('message', 'Onboarding is not complete. Please finish the remaining steps.');
    }

    public function refresh(Request $request): RedirectResponse
    {
        $user = $request->user();
        $developerAccount = $user->developerAccount;

        if (! $developerAccount) {
            return to_route('customer.developer.onboarding');
        }

        try {
            $onboardingUrl = $this->stripeConnectService->createOnboardingLink($developerAccount);

            return redirect($onboardingUrl);
        } catch (\Exception $e) {
            return to_route('customer.developer.onboarding')
                ->with('error', 'Unable to refresh onboarding. Please try again.');
        }
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $developerAccount = $user->developerAccount;

        if (! $developerAccount || ! $developerAccount->hasCompletedOnboarding()) {
            return to_route('customer.developer.onboarding');
        }

        $this->stripeConnectService->refreshAccountStatus($developerAccount);

        $plugins = $user->plugins()->withCount('licenses')->get();
        $payouts = $developerAccount->payouts()->with('pluginLicense.plugin')->latest()->limit(10)->get();

        $totalEarnings = $developerAccount->payouts()
            ->where('status', 'transferred')
            ->sum('developer_amount');

        $pendingEarnings = $developerAccount->payouts()
            ->where('status', 'pending')
            ->sum('developer_amount');

        return view('customer.developer.dashboard', [
            'developerAccount' => $developerAccount,
            'plugins' => $plugins,
            'payouts' => $payouts,
            'totalEarnings' => $totalEarnings,
            'pendingEarnings' => $pendingEarnings,
        ]);
    }
}
