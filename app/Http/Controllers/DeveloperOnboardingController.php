<?php

namespace App\Http\Controllers;

use App\Models\DeveloperAccount;
use App\Services\StripeConnectService;
use App\Support\StripeConnectCountries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeveloperOnboardingController extends Controller
{
    public function __construct(protected StripeConnectService $stripeConnectService) {}

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $developerAccount = $user->developerAccount;

        if ($developerAccount && $developerAccount->hasCompletedOnboarding() && $developerAccount->hasAcceptedCurrentTerms()) {
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
        $request->validate([
            'accepted_plugin_terms' => ['required', 'accepted'],
            'country' => ['required', 'string', 'size:2', Rule::in(StripeConnectCountries::supportedCountryCodes())],
            'payout_currency' => ['required', 'string', 'size:3'],
        ], [
            'accepted_plugin_terms.required' => 'You must accept the Plugin Developer Terms and Conditions.',
            'accepted_plugin_terms.accepted' => 'You must accept the Plugin Developer Terms and Conditions.',
            'country.required' => 'Please select your country.',
            'country.in' => 'The selected country is not supported for Stripe Connect.',
            'payout_currency.required' => 'Please select a payout currency.',
        ]);

        $country = strtoupper($request->input('country'));
        $payoutCurrency = strtoupper($request->input('payout_currency'));

        if (! StripeConnectCountries::isValidCurrencyForCountry($country, $payoutCurrency)) {
            return back()->withErrors(['payout_currency' => 'The selected currency is not available for your country.']);
        }

        $user = $request->user();
        $developerAccount = $user->developerAccount;

        if (! $developerAccount) {
            $developerAccount = $this->stripeConnectService->createConnectAccount($user, $country, $payoutCurrency);
        } else {
            $developerAccount->update([
                'country' => $country,
                'payout_currency' => $payoutCurrency,
            ]);
        }

        if (! $developerAccount->hasAcceptedCurrentTerms()) {
            $developerAccount->update([
                'accepted_plugin_terms_at' => now(),
                'plugin_terms_version' => DeveloperAccount::CURRENT_PLUGIN_TERMS_VERSION,
            ]);
        }

        // If Stripe onboarding is already complete, skip the Stripe redirect
        if ($developerAccount->hasCompletedOnboarding()) {
            return to_route('customer.plugins.create')
                ->with('success', 'Terms accepted! You can now submit plugins.');
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
