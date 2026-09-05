---
title: Mobile Wallet
order: 1350
---

## Overview

The `MobileWallet` facade lets your app accept payments through Apple Pay (iOS) and Google Pay (Android) using
[Stripe](https://stripe.com) under the hood. Your Laravel backend creates a payment intent, the device presents the
native payment sheet for the user to authorize with Face ID, Touch ID, or their device PIN, and your backend confirms
the result.

```php
use Native\Mobile\Facades\MobileWallet;
```

## Checking availability

Not every device supports Apple Pay or Google Pay. Check before showing a "Pay" button:

```php
if (MobileWallet::isAvailable()) {
    // Show the wallet payment option
}
```

## Taking a payment

The flow has three steps: create a payment intent on your backend, present the native payment sheet, then confirm
the result.

```php
$intent = MobileWallet::createPaymentIntent(
    amount: 1999, // $19.99, in cents
    currency: 'usd',
    metadata: ['order_id' => $order->id],
);

$result = MobileWallet::presentPaymentSheet(
    clientSecret: $intent->client_secret,
    merchantDisplayName: 'Acme Widgets',
    publishableKey: config('services.stripe.key'),
    merchantId: 'merchant.com.acme.widgets', // Your Apple Pay merchant ID
);

$confirmation = MobileWallet::confirmPayment($intent->id);
```

- `createPaymentIntent()` — `$amount` is in the smallest currency unit (cents for USD), `$currency` is a lowercase
  ISO code, and `$metadata` is attached to the Stripe payment intent for your own bookkeeping.
- `presentPaymentSheet()` — shows the native card/wallet picker. `$merchantId` is your registered Apple Pay merchant
  identifier; `$merchantCountryCode` defaults to `US`.
- `confirmPayment($paymentIntentId)` — confirms the payment with whichever method the user selected.

You can also poll the current status of a payment at any time:

```php
$status = MobileWallet::getPaymentStatus($intent->id);
```

<aside>

Setting up Apple Pay requires a merchant ID and a payment processing certificate from your Apple Developer account.
See [Stripe's Apple Pay guide](https://stripe.com/docs/apple-pay) and
[Google Pay guide](https://stripe.com/docs/google-pay) for the account-side setup — `MobileWallet` handles the
device-side integration once that's in place.

</aside>

## Events

Alongside the return values above, the payment sheet also dispatches [native events](../../the-basics/events) as the
user completes, cancels, or fails a payment — listen for these if a screen other than the one that started the
payment needs to react:

- `Native\Mobile\Events\Wallet\PaymentCompleted` — `paymentIntentId`, `amount`, `currency`, `status`, `metadata`.
- `Native\Mobile\Events\Wallet\PaymentCancelled` — `paymentIntentId`, `reason`.
- `Native\Mobile\Events\Wallet\PaymentFailed` — `paymentIntentId`, `errorCode`, `errorMessage`, `metadata`.
