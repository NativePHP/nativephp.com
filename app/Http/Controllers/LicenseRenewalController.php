<?php

namespace App\Http\Controllers;

use App\Enums\Subscription;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseRenewalController extends Controller
{
    public function show(Request $request, string $licenseKey): View
    {
        $license = License::where('key', $licenseKey)
            ->whereNull('subscription_item_id') // Only legacy licenses
            ->whereNotNull('expires_at') // Must have an expiry date
            ->where('expires_at', '>', now()) // Must not already be expired
            ->with('user')
            ->firstOrFail();

        // Ensure the user owns this license (if they're logged in)
        if (auth()->check() && $license->user_id !== auth()->id()) {
            abort(403, 'You can only renew your own licenses.');
        }

        $subscriptionType = Subscription::from($license->policy_name);
        $isNearExpiry = $license->expires_at->diffInDays(now()) <= 30;

        return view('license.renewal', [
            'license' => $license,
            'subscriptionType' => $subscriptionType,
            'isNearExpiry' => $isNearExpiry,
            'stripePriceId' => $subscriptionType->stripePriceId(forceEap: true), // Will use EAP pricing
            'stripePublishableKey' => config('cashier.key'),
        ]);
    }

    public function createCheckoutSession(Request $request, string $licenseKey)
    {
        $license = License::where('key', $licenseKey)
            ->whereNull('subscription_item_id') // Only legacy licenses
            ->whereNotNull('expires_at') // Must have an expiry date
            ->where('expires_at', '>', now()) // Must not already be expired
            ->with('user')
            ->firstOrFail();

        // Ensure the user owns this license (if they're logged in)
        if (auth()->check() && $license->user_id !== auth()->id()) {
            abort(403, 'You can only renew your own licenses.');
        }

        $subscriptionType = Subscription::from($license->policy_name);

        // Create Stripe checkout session
        $stripe = new \Stripe\StripeClient(config('cashier.secret'));

        $checkoutSession = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $subscriptionType->stripePriceId(forceEap: true), // Uses EAP pricing
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('license.renewal.success', ['license' => $licenseKey]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('license.renewal', ['license' => $licenseKey]),
            'metadata' => [
                'license_key' => $licenseKey,
                'license_id' => $license->id,
                'renewal' => 'true', // Flag this as a renewal, not a new purchase
            ],
            'consent_collection' => [
                'terms_of_service' => 'required',
            ],
            'tax_id_collection' => [
                'enabled' => true,
            ],
            'customer_email' => $license->user->email,
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
