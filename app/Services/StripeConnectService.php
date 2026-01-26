<?php

namespace App\Services;

use App\Enums\PayoutStatus;
use App\Enums\StripeConnectStatus;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\PluginPrice;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripeConnectService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    public function createConnectAccount(User $user): DeveloperAccount
    {
        $account = $this->stripe->accounts->create([
            'type' => 'express',
            'email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
            ],
            'capabilities' => [
                'transfers' => ['requested' => true],
            ],
        ]);

        return DeveloperAccount::create([
            'user_id' => $user->id,
            'stripe_connect_account_id' => $account->id,
            'stripe_connect_status' => StripeConnectStatus::Pending,
            'payouts_enabled' => false,
            'charges_enabled' => false,
        ]);
    }

    public function createOnboardingLink(DeveloperAccount $account): string
    {
        $accountLink = $this->stripe->accountLinks->create([
            'account' => $account->stripe_connect_account_id,
            'refresh_url' => route('customer.developer.onboarding.refresh'),
            'return_url' => route('customer.developer.onboarding.return'),
            'type' => 'account_onboarding',
        ]);

        return $accountLink->url;
    }

    public function refreshAccountStatus(DeveloperAccount $account): void
    {
        $stripeAccount = $this->stripe->accounts->retrieve($account->stripe_connect_account_id);

        $account->update([
            'payouts_enabled' => $stripeAccount->payouts_enabled,
            'charges_enabled' => $stripeAccount->charges_enabled,
            'stripe_connect_status' => $this->determineStatus($stripeAccount),
            'onboarding_completed_at' => $stripeAccount->details_submitted ? now() : null,
        ]);

        Log::info('Refreshed developer account status', [
            'developer_account_id' => $account->id,
            'stripe_account_id' => $account->stripe_connect_account_id,
            'status' => $account->stripe_connect_status->value,
        ]);
    }

    public function createCheckoutSession(PluginPrice $price, User $buyer): \Stripe\Checkout\Session
    {
        $plugin = $price->plugin;
        $developerAccount = $plugin->developerAccount;

        // Ensure the buyer has a Stripe customer ID (required for Stripe Accounts V2)
        if (! $buyer->stripe_id) {
            $buyer->createAsStripeCustomer();
        }

        $productName = $plugin->name;
        if (! $price->isRegularTier()) {
            $productName .= ' ('.$price->tier->label().' pricing)';
        }

        $sessionParams = [
            'mode' => 'payment',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($price->currency),
                        'unit_amount' => $price->amount,
                        'product_data' => [
                            'name' => $productName,
                            'description' => $plugin->description ?? 'NativePHP Plugin',
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'success_url' => route('plugins.purchase.success', $plugin->routeParams()).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('plugins.purchase.cancel', $plugin->routeParams()),
            'customer' => $buyer->stripe_id,
            'customer_update' => [
                'name' => 'auto',
                'address' => 'auto',
            ],
            'metadata' => [
                'plugin_id' => $plugin->id,
                'user_id' => $buyer->id,
                'price_id' => $price->id,
                'price_tier' => $price->tier->value,
            ],
            'allow_promotion_codes' => true,
            'billing_address_collection' => 'required',
            'tax_id_collection' => ['enabled' => true],
            'invoice_creation' => [
                'enabled' => true,
                'invoice_data' => [
                    'description' => 'NativePHP Plugin Purchase',
                    'footer' => 'Thank you for your purchase!',
                ],
            ],
        ];

        if ($developerAccount && $developerAccount->canReceivePayouts()) {
            $split = PluginPayout::calculateSplit($price->amount);

            $sessionParams['payment_intent_data'] = [
                'transfer_data' => [
                    'destination' => $developerAccount->stripe_connect_account_id,
                    'amount' => $split['developer_amount'],
                ],
            ];
        }

        return $this->stripe->checkout->sessions->create($sessionParams);
    }

    public function processSuccessfulPayment(string $sessionId): PluginLicense
    {
        $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
            'expand' => ['payment_intent'],
        ]);

        $pluginId = $session->metadata->plugin_id;
        $userId = $session->metadata->user_id;
        $priceId = $session->metadata->price_id;

        $plugin = Plugin::findOrFail($pluginId);
        $user = User::findOrFail($userId);
        $price = PluginPrice::findOrFail($priceId);

        $license = PluginLicense::create([
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
            'stripe_payment_intent_id' => $session->payment_intent->id,
            'price_paid' => $session->amount_total,
            'currency' => strtoupper($session->currency),
            'is_grandfathered' => false,
            'purchased_at' => now(),
        ]);

        if ($plugin->developerAccount && $plugin->developerAccount->canReceivePayouts()) {
            $this->createPayout($license, $plugin->developerAccount);
        }

        $user->getPluginLicenseKey();

        Log::info('Created plugin license from successful payment', [
            'license_id' => $license->id,
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
        ]);

        return $license;
    }

    /**
     * Process a multi-item cart payment and create licenses for each plugin.
     *
     * @return array<PluginLicense>
     */
    public function processMultiItemPayment(string $sessionId): array
    {
        $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
            'expand' => ['payment_intent', 'line_items'],
        ]);

        $userId = $session->metadata->user_id;
        $pluginIds = explode(',', $session->metadata->plugin_ids);
        $priceIds = explode(',', $session->metadata->price_ids);

        $user = User::findOrFail($userId);
        $licenses = [];

        // Get line items to match prices
        $lineItems = $session->line_items->data;

        foreach ($pluginIds as $index => $pluginId) {
            $plugin = Plugin::findOrFail($pluginId);
            $priceId = $priceIds[$index] ?? null;
            $price = $priceId ? PluginPrice::find($priceId) : null;

            // Get the amount from line items
            $amount = isset($lineItems[$index])
                ? $lineItems[$index]->amount_total
                : ($price ? $price->amount : 0);

            $license = PluginLicense::create([
                'user_id' => $user->id,
                'plugin_id' => $plugin->id,
                'stripe_payment_intent_id' => $session->payment_intent->id,
                'price_paid' => $amount,
                'currency' => strtoupper($session->currency),
                'is_grandfathered' => false,
                'purchased_at' => now(),
            ]);

            if ($plugin->developerAccount && $plugin->developerAccount->canReceivePayouts()) {
                $this->createPayout($license, $plugin->developerAccount);
            }

            $licenses[] = $license;

            Log::info('Created plugin license from cart payment', [
                'license_id' => $license->id,
                'user_id' => $user->id,
                'plugin_id' => $plugin->id,
            ]);
        }

        $user->getPluginLicenseKey();

        return $licenses;
    }

    public function createPayout(PluginLicense $license, DeveloperAccount $developerAccount): PluginPayout
    {
        $split = PluginPayout::calculateSplit($license->price_paid);

        return PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => $license->price_paid,
            'platform_fee' => $split['platform_fee'],
            'developer_amount' => $split['developer_amount'],
            'status' => PayoutStatus::Pending,
        ]);
    }

    public function processTransfer(PluginPayout $payout): bool
    {
        if (! $payout->isPending()) {
            return false;
        }

        $developerAccount = $payout->developerAccount;

        if (! $developerAccount->canReceivePayouts()) {
            Log::warning('Developer account cannot receive payouts', [
                'payout_id' => $payout->id,
                'developer_account_id' => $developerAccount->id,
            ]);

            return false;
        }

        // Get the charge ID from the payment intent to use as source_transaction
        // This ensures the transfer uses funds from this specific charge and waits for them to be available
        $chargeId = $this->getChargeIdFromPayout($payout);

        try {
            $transferParams = [
                'amount' => $payout->developer_amount,
                'currency' => 'usd',
                'destination' => $developerAccount->stripe_connect_account_id,
                'metadata' => [
                    'payout_id' => $payout->id,
                    'plugin_license_id' => $payout->plugin_license_id,
                ],
            ];

            // Link transfer to the source charge - Stripe will wait for funds to be available
            if ($chargeId) {
                $transferParams['source_transaction'] = $chargeId;
            }

            $transfer = $this->stripe->transfers->create($transferParams);

            $payout->markAsTransferred($transfer->id);

            Log::info('Processed transfer for payout', [
                'payout_id' => $payout->id,
                'transfer_id' => $transfer->id,
                'amount' => $payout->developer_amount,
                'source_transaction' => $chargeId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to process transfer', [
                'payout_id' => $payout->id,
                'charge_id' => $chargeId,
                'error' => $e->getMessage(),
            ]);

            $payout->markAsFailed();

            return false;
        }
    }

    protected function getChargeIdFromPayout(PluginPayout $payout): ?string
    {
        $license = $payout->pluginLicense;

        if (! $license || ! $license->stripe_payment_intent_id) {
            return null;
        }

        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($license->stripe_payment_intent_id);

            return $paymentIntent->latest_charge;
        } catch (\Exception $e) {
            Log::warning('Could not retrieve charge ID from payment intent', [
                'payment_intent_id' => $license->stripe_payment_intent_id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function determineStatus(\Stripe\Account $account): StripeConnectStatus
    {
        if ($account->payouts_enabled && $account->charges_enabled) {
            return StripeConnectStatus::Active;
        }

        if ($account->requirements?->disabled_reason) {
            return StripeConnectStatus::Disabled;
        }

        return StripeConnectStatus::Pending;
    }

    public function createProductAndPrice(Plugin $plugin, int $amountCents, string $currency = 'usd'): PluginPrice
    {
        $product = $this->stripe->products->create([
            'name' => $plugin->name,
            'description' => $plugin->description,
            'metadata' => [
                'plugin_id' => $plugin->id,
            ],
        ]);

        $price = $this->stripe->prices->create([
            'product' => $product->id,
            'unit_amount' => $amountCents,
            'currency' => $currency,
        ]);

        return PluginPrice::create([
            'plugin_id' => $plugin->id,
            'stripe_price_id' => $price->id,
            'amount' => $amountCents,
            'currency' => strtoupper($currency),
            'is_active' => true,
        ]);
    }
}
