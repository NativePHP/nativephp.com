<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Stripe\StripeClient;

class OrderSuccessController extends Controller
{
    public function __invoke(Request $request, string $checkoutSessionId)
    {
        $stripe = app(StripeClient::class);
        $checkoutSession = $stripe->checkout->sessions->retrieve($checkoutSessionId);

        if (! $checkoutSession) {
            return to_route('early-adopter');
        }

        $request->session()->put('customer_email', $email = $checkoutSession->customer_details->email);

        if ($licenseKey = Cache::get($email.'.license_key')) {
            $request->session()->put('license_key', $licenseKey);
        }

        return view('order-success', [
            'email' => $email,
            'licenseKey' => $licenseKey,
        ]);
    }
}
