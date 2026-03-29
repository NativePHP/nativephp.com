<?php

namespace App\Http\Controllers;

use App\Enums\Subscription;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\StripeClient;

class LicenseRenewalController extends Controller
{
    public function show(Request $request, string $licenseKey): View
    {
        $license = License::where('key', $licenseKey)
            ->whereNull('subscription_item_id') // Only legacy licenses
            ->whereNotNull('expires_at') // Must have an expiry date
            ->with('user')
            ->firstOrFail();

        if ($license->user_id !== auth()->id()) {
            abort(403, 'You can only renew your own licenses.');
        }

        return view('license.renewal', [
            'license' => $license,
        ]);
    }

    public function createCheckoutSession(Request $request, string $licenseKey)
    {
        $request->validate([
            'billing_period' => ['required', 'in:yearly,monthly'],
        ]);

        $license = License::where('key', $licenseKey)
            ->whereNull('subscription_item_id') // Only legacy licenses
            ->whereNotNull('expires_at') // Must have an expiry date
            ->with('user')
            ->firstOrFail();

        if ($license->user_id !== auth()->id()) {
            abort(403, 'You can only renew your own licenses.');
        }

        $user = $license->user;

        // Ensure the user has a Stripe customer ID
        if (! $user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        // Always upgrade to Ultra (Max) - EAP yearly or standard monthly
        $ultra = Subscription::Max;
        $priceId = $request->billing_period === 'monthly'
            ? $ultra->stripePriceId(interval: 'month')
            : $ultra->stripePriceId(forceEap: true);

        // Create Stripe checkout session
        $stripe = new StripeClient(config('cashier.secret'));

        $checkoutSession = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('license.renewal.success', ['license' => $licenseKey]).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('license.renewal', ['license' => $licenseKey]),
            'customer' => $user->stripe_id,
            'customer_update' => [
                'name' => 'auto',
                'address' => 'auto',
            ],
            'metadata' => [
                'license_key' => $licenseKey,
                'license_id' => $license->id,
                'renewal' => 'true',
            ],
            'consent_collection' => [
                'terms_of_service' => 'required',
            ],
            'tax_id_collection' => [
                'enabled' => true,
            ],
            'subscription_data' => [
                'metadata' => [
                    'license_key' => $licenseKey,
                    'license_id' => $license->id,
                    'renewal' => 'true',
                ],
            ],
        ]);

        return redirect($checkoutSession->url);
    }
}
