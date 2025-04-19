<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Stripe\StripeClient;

class OrderSuccessController extends Controller
{
    public function __invoke(Request $request, string $checkoutSessionId)
    {
        $email = $this->getEmail($request, $checkoutSessionId);

        $licenseKey = $this->getLicenseKey($request, $email);

        return view('order-success', [
            'email' => $email,
            'licenseKey' => $licenseKey,
        ]);
    }

    private function getEmail(Request $request, string $checkoutSessionId): ?string
    {
        if ($email = $request->session()->get('customer_email')) {
            return $email;
        }

        $stripe = app(StripeClient::class);
        $checkoutSession = $stripe->checkout->sessions->retrieve($checkoutSessionId);

        if (! ($email = $checkoutSession?->customer_details?->email)) {
            return null;
        }

        $request->session()->put('customer_email', $email);

        return $email;
    }

    private function getLicenseKey(Request $request, ?string $email): ?string
    {
        if ($licenseKey = $request->session()->get('license_key')) {
            return $licenseKey;
        }

        if (! $email) {
            return null;
        }

        if ($licenseKey = Cache::get($email.'.license_key')) {
            $request->session()->put('license_key', $licenseKey);
        }

        return $licenseKey;
    }
}
