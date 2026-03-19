<?php

namespace App\Jobs;

use App\Enums\PayoutStatus;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\PluginPrice;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPluginCheckoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $checkoutSessionId,
        public array $metadata,
        public int $amountTotal,
        public string $currency,
        public ?string $paymentIntentId = null
    ) {}

    public function handle(): void
    {
        $userId = $this->metadata['user_id'] ?? null;
        $cartId = $this->metadata['cart_id'] ?? null;

        if (! $userId) {
            Log::error('No user_id in checkout session metadata', ['session_id' => $this->checkoutSessionId]);

            return;
        }

        $user = User::find($userId);

        if (! $user) {
            Log::error('User not found for checkout session', ['session_id' => $this->checkoutSessionId, 'user_id' => $userId]);

            return;
        }

        // Handle bundle checkout
        if (isset($this->metadata['bundle_ids']) && ! empty($this->metadata['bundle_ids'])) {
            $this->processBundleCheckout($user);
        }

        // Handle cart checkout (individual plugins)
        if ($cartId && isset($this->metadata['plugin_ids']) && ! empty($this->metadata['plugin_ids'])) {
            $this->processCartCheckout($user);

            return;
        }

        // Handle single plugin checkout
        if (isset($this->metadata['plugin_id'])) {
            // Check if single plugin already processed
            if (PluginLicense::where('stripe_checkout_session_id', $this->checkoutSessionId)->exists()) {
                Log::info('Single plugin checkout already processed', ['session_id' => $this->checkoutSessionId]);

                return;
            }

            $this->processSinglePluginCheckout($user);

            return;
        }

        Log::error('Unknown checkout session format', ['session_id' => $this->checkoutSessionId, 'metadata' => $this->metadata]);
    }

    protected function processCartCheckout(User $user): void
    {
        Log::info('Starting cart checkout processing', [
            'session_id' => $this->checkoutSessionId,
            'metadata' => $this->metadata,
        ]);

        $pluginIds = explode(',', $this->metadata['plugin_ids']);
        $priceIds = explode(',', $this->metadata['price_ids'] ?? '');

        Log::info('Parsed plugin IDs from metadata', [
            'session_id' => $this->checkoutSessionId,
            'raw_plugin_ids' => $this->metadata['plugin_ids'],
            'parsed_plugin_ids' => $pluginIds,
            'plugin_count' => count($pluginIds),
        ]);

        // Get already processed plugin IDs for this session
        $alreadyProcessedPluginIds = PluginLicense::where('stripe_checkout_session_id', $this->checkoutSessionId)
            ->pluck('plugin_id')
            ->toArray();

        $processedCount = 0;
        $skippedCount = count($alreadyProcessedPluginIds);

        foreach ($pluginIds as $index => $pluginId) {
            Log::info('Processing plugin in cart', [
                'session_id' => $this->checkoutSessionId,
                'index' => $index,
                'plugin_id' => $pluginId,
                'already_processed' => in_array((int) $pluginId, $alreadyProcessedPluginIds),
            ]);

            // Skip if this plugin was already processed for this session
            if (in_array((int) $pluginId, $alreadyProcessedPluginIds)) {
                continue;
            }

            $plugin = Plugin::find($pluginId);

            if (! $plugin) {
                Log::warning('Plugin not found during checkout processing', ['plugin_id' => $pluginId]);

                continue;
            }

            $priceId = $priceIds[$index] ?? null;
            $price = $priceId ? PluginPrice::find($priceId) : $plugin->activePrice;
            $amount = $price ? $price->amount : 0;

            try {
                $this->createLicense($user, $plugin, $amount);
                $processedCount++;
                Log::info('Successfully created license for plugin', [
                    'session_id' => $this->checkoutSessionId,
                    'plugin_id' => $pluginId,
                    'plugin_name' => $plugin->name,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create license for plugin', [
                    'session_id' => $this->checkoutSessionId,
                    'plugin_id' => $pluginId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        }

        // Ensure user has a plugin license key
        $user->getPluginLicenseKey();

        Log::info('Processed cart checkout', [
            'session_id' => $this->checkoutSessionId,
            'user_id' => $user->id,
            'total_plugins' => count($pluginIds),
            'processed_now' => $processedCount,
            'already_processed' => $skippedCount,
        ]);
    }

    protected function processBundleCheckout(User $user): void
    {
        $bundleIds = array_filter(explode(',', $this->metadata['bundle_ids']));
        $bundlePluginIds = json_decode($this->metadata['bundle_plugin_ids'] ?? '{}', true);

        Log::info('Processing bundle checkout', [
            'session_id' => $this->checkoutSessionId,
            'bundle_ids' => $bundleIds,
        ]);

        foreach ($bundleIds as $bundleId) {
            $bundle = PluginBundle::with(['plugins.developerAccount', 'plugins.activePrice'])
                ->find($bundleId);

            if (! $bundle) {
                Log::warning('Bundle not found during checkout processing', ['bundle_id' => $bundleId]);

                continue;
            }

            // Check how many plugins are already processed for this bundle in this session
            $existingLicenseCount = PluginLicense::where('stripe_checkout_session_id', $this->checkoutSessionId)
                ->where('plugin_bundle_id', $bundleId)
                ->count();

            if ($existingLicenseCount === $bundle->plugins->count()) {
                Log::info('Bundle already fully processed', [
                    'session_id' => $this->checkoutSessionId,
                    'bundle_id' => $bundleId,
                ]);

                continue;
            }

            // Calculate proportional allocation for developer payouts
            $allocations = $bundle->calculateProportionalAllocation();

            foreach ($bundle->plugins as $plugin) {
                // Skip if license already exists for this plugin in this session
                if (PluginLicense::where('stripe_checkout_session_id', $this->checkoutSessionId)
                    ->where('plugin_id', $plugin->id)
                    ->where('plugin_bundle_id', $bundleId)
                    ->exists()) {
                    continue;
                }

                $allocatedAmount = $allocations[$plugin->id] ?? 0;

                $this->createBundleLicense($user, $plugin, $bundle, $allocatedAmount);
            }

            Log::info('Processed bundle checkout', [
                'session_id' => $this->checkoutSessionId,
                'bundle_id' => $bundleId,
                'bundle_name' => $bundle->name,
                'plugin_count' => $bundle->plugins->count(),
            ]);
        }

        $user->getPluginLicenseKey();
    }

    protected function createBundleLicense(User $user, Plugin $plugin, PluginBundle $bundle, int $allocatedAmount): PluginLicense
    {
        $license = PluginLicense::create([
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
            'plugin_bundle_id' => $bundle->id,
            'stripe_checkout_session_id' => $this->checkoutSessionId,
            'stripe_payment_intent_id' => $this->paymentIntentId,
            'price_paid' => $allocatedAmount,
            'currency' => strtoupper($this->currency),
            'is_grandfathered' => false,
            'purchased_at' => now(),
        ]);

        // Create proportional payout for developer
        if ($plugin->developerAccount && $plugin->developerAccount->canReceivePayouts() && $allocatedAmount > 0) {
            $split = PluginPayout::calculateSplit($allocatedAmount);

            $payout = PluginPayout::create([
                'plugin_license_id' => $license->id,
                'developer_account_id' => $plugin->developerAccount->id,
                'gross_amount' => $allocatedAmount,
                'platform_fee' => $split['platform_fee'],
                'developer_amount' => $split['developer_amount'],
                'status' => PayoutStatus::Pending,
            ]);

            $stripeConnectService = resolve(StripeConnectService::class);
            $stripeConnectService->processTransfer($payout);
        }

        Log::info('Created bundle license', [
            'session_id' => $this->checkoutSessionId,
            'bundle_id' => $bundle->id,
            'plugin_id' => $plugin->id,
            'allocated_amount' => $allocatedAmount,
        ]);

        return $license;
    }

    protected function processSinglePluginCheckout(User $user): void
    {
        $pluginId = $this->metadata['plugin_id'];
        $priceId = $this->metadata['price_id'] ?? null;

        $plugin = Plugin::find($pluginId);

        if (! $plugin) {
            Log::error('Plugin not found for single checkout', ['plugin_id' => $pluginId]);

            return;
        }

        $price = $priceId ? PluginPrice::find($priceId) : $plugin->activePrice;
        $amount = $price ? $price->amount : $this->amountTotal;

        $this->createLicense($user, $plugin, $amount);

        $user->getPluginLicenseKey();

        Log::info('Processed single plugin checkout', [
            'session_id' => $this->checkoutSessionId,
            'user_id' => $user->id,
            'plugin_id' => $pluginId,
        ]);
    }

    protected function createLicense(User $user, Plugin $plugin, int $amount): PluginLicense
    {
        $license = PluginLicense::create([
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
            'stripe_checkout_session_id' => $this->checkoutSessionId,
            'stripe_payment_intent_id' => $this->paymentIntentId,
            'price_paid' => $amount,
            'currency' => strtoupper($this->currency),
            'is_grandfathered' => false,
            'purchased_at' => now(),
        ]);

        // Create payout record for developer if applicable
        if ($plugin->developerAccount && $plugin->developerAccount->canReceivePayouts()) {
            $split = PluginPayout::calculateSplit($amount);

            $payout = PluginPayout::create([
                'plugin_license_id' => $license->id,
                'developer_account_id' => $plugin->developerAccount->id,
                'gross_amount' => $amount,
                'platform_fee' => $split['platform_fee'],
                'developer_amount' => $split['developer_amount'],
                'status' => PayoutStatus::Pending,
            ]);

            // For cart checkouts, we need to manually transfer since transfer_data wasn't used
            // For single plugin checkouts, transfer_data already handled the transfer at checkout time
            $isCartCheckout = isset($this->metadata['cart_id']);

            if ($isCartCheckout) {
                $stripeConnectService = resolve(StripeConnectService::class);
                $stripeConnectService->processTransfer($payout);
            } else {
                // Single plugin purchase - transfer already happened via transfer_data
                // Just mark the payout as transferred for tracking
                $payout->update([
                    'status' => PayoutStatus::Transferred,
                    'transferred_at' => now(),
                ]);
            }
        }

        return $license;
    }
}
