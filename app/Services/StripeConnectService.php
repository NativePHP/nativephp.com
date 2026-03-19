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
use Laravel\Cashier\Cashier;

/**
 * Service for managing Stripe Connect accounts and processing developer payouts.
 */
class StripeConnectService
{
    public function createConnectAccount(User $user): DeveloperAccount
    {
        $account = Cashier::stripe()->accounts->create([
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
        $accountLink = Cashier::stripe()->accountLinks->create([
            'account' => $account->stripe_connect_account_id,
            'refresh_url' => route('customer.developer.onboarding.refresh'),
            'return_url' => route('customer.developer.onboarding.return'),
            'type' => 'account_onboarding',
        ]);

        return $accountLink->url;
    }

    public function refreshAccountStatus(DeveloperAccount $account): void
    {
        $stripeAccount = Cashier::stripe()->accounts->retrieve($account->stripe_connect_account_id);

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

            $transfer = Cashier::stripe()->transfers->create($transferParams);

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
            $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($license->stripe_payment_intent_id);

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
        $product = Cashier::stripe()->products->create([
            'name' => $plugin->name,
            'description' => $plugin->description,
            'metadata' => [
                'plugin_id' => $plugin->id,
            ],
        ]);

        $price = Cashier::stripe()->prices->create([
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
