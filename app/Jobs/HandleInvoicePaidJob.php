<?php

namespace App\Jobs;

use App\Enums\PayoutStatus;
use App\Enums\Subscription;
use App\Exceptions\InvalidStateException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\License;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\PluginPrice;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use App\Services\StripeConnectService;
use App\Support\GitHubOAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\SubscriptionItem;
use Stripe\Invoice;
use UnexpectedValueException;

class HandleInvoicePaidJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $maxExceptions = 1;

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        Log::info('HandleInvoicePaidJob started', [
            'invoice_id' => $this->invoice->id,
            'billing_reason' => $this->invoice->billing_reason,
            'metadata' => (array) $this->invoice->metadata,
        ]);

        match ($this->invoice->billing_reason) {
            Invoice::BILLING_REASON_SUBSCRIPTION_CREATE => $this->handleSubscriptionCreated(),
            Invoice::BILLING_REASON_SUBSCRIPTION_UPDATE => null, // TODO: Handle subscription update
            Invoice::BILLING_REASON_SUBSCRIPTION_CYCLE => $this->handleSubscriptionRenewal(),
            Invoice::BILLING_REASON_MANUAL => $this->handleManualInvoice(),
            default => null,
        };
    }

    private function handleSubscriptionCreated(): void
    {
        // Get the subscription to check for renewal metadata
        $subscription = Cashier::stripe()->subscriptions->retrieve($this->invoice->subscription);

        // Check if this is our "renewal" process (new subscription for existing legacy license)
        $isRenewal = isset($subscription->metadata['renewal']) && $subscription->metadata['renewal'] === 'true';
        $licenseKey = $subscription->metadata['license_key'] ?? null;
        $licenseId = $subscription->metadata['license_id'] ?? null;

        if ($isRenewal && $licenseKey && $licenseId) {
            $this->handleLegacyLicenseRenewal($subscription, $licenseKey, $licenseId);

            return;
        }

        // Normal flow - create a new license
        $this->createLicense();
    }

    private function handleLegacyLicenseRenewal($subscription, string $licenseKey, string $licenseId): void
    {
        $user = $this->billable();

        // Find the existing legacy license
        $license = License::where('id', $licenseId)
            ->where('key', $licenseKey)
            ->where('user_id', $user->id) // Ensure user owns the license
            ->first();

        if (! $license) {
            logger('Legacy license renewal failed - license not found', [
                'license_key' => $licenseKey,
                'license_id' => $licenseId,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);
            // Fallback to creating a new license
            $this->createLicense();

            return;
        }

        // Get the subscription item
        if (blank($subscriptionItemId = $this->invoice->lines->first()->subscription_item)) {
            throw new UnexpectedValueException('Failed to retrieve the Stripe subscription item id from invoice lines.');
        }

        $subscriptionItemModel = SubscriptionItem::query()->where('stripe_id', $subscriptionItemId)->firstOrFail();

        // Calculate new expiry date from subscription period end
        $newExpiryDate = \Illuminate\Support\Facades\Date::createFromTimestamp($subscription->current_period_end);

        // Link the subscription to the existing license (expiry will be updated by Anystack job)
        $license->update([
            'subscription_item_id' => $subscriptionItemModel->id,
        ]);

        // Update the Anystack license expiry date (which will also update the database on success)
        dispatch(new UpdateAnystackLicenseExpiryJob($license, $newExpiryDate));

        logger('Legacy license renewal completed', [
            'license_id' => $license->id,
            'license_key' => $license->key,
            'user_id' => $user->id,
            'subscription_item_id' => $subscriptionItemModel->id,
            'subscription_id' => $subscription->id,
            'old_expiry' => $license->getOriginal('expires_at'),
            'new_expiry' => $newExpiryDate,
        ]);
    }

    private function createLicense(): void
    {
        // Add some delay to allow all the Stripe events to come in
        \Illuminate\Support\Sleep::sleep(10);

        // Assert the invoice line item is for a price_id that relates to a license plan.
        $plan = Subscription::fromStripePriceId($this->invoice->lines->first()->price->id);

        // Assert the invoice line item relates to a subscription and has a subscription item id.
        if (blank($subscriptionItemId = $this->invoice->lines->first()->subscription_item)) {
            throw new UnexpectedValueException('Failed to retrieve the Stripe subscription item id from invoice lines.');
        }

        // Assert we have a subscription item record for this subscription item id.
        $subscriptionItemModel = SubscriptionItem::query()->where('stripe_id', $subscriptionItemId)->firstOrFail();

        // Assert we don't already have an existing license for this subscription item.
        if ($license = License::query()->whereBelongsTo($subscriptionItemModel)->first()) {
            throw new InvalidStateException("A license [{$license->id}] already exists for subscription item [{$subscriptionItemModel->id}].");
        }

        $user = $this->billable();

        dispatch(new CreateAnystackLicenseJob(
            $user,
            $plan,
            $subscriptionItemModel->id,
            $user->first_name,
            $user->last_name,
        ));
    }

    private function handleSubscriptionRenewal(): void
    {
        // Get the subscription item ID from the invoice line
        if (blank($subscriptionItemId = $this->invoice->lines->first()->subscription_item)) {
            throw new UnexpectedValueException('Failed to retrieve the Stripe subscription item id from invoice lines.');
        }

        // Find the subscription item model
        $subscriptionItemModel = SubscriptionItem::query()->where('stripe_id', $subscriptionItemId)->firstOrFail();

        // Find the license associated with this subscription item
        $license = License::query()->whereBelongsTo($subscriptionItemModel)->first();

        if (! $license) {
            // No existing license found - this might be a new subscription, handle as create
            $this->createLicense();

            return;
        }

        // Get the subscription to find the current period end
        $subscription = Cashier::stripe()->subscriptions->retrieve($this->invoice->subscription);

        // Update the license expiry date to match the subscription's current period end
        $newExpiryDate = \Illuminate\Support\Facades\Date::createFromTimestamp($subscription->current_period_end);

        // Update the Anystack license expiry date (which will also update the database on success)
        dispatch(new UpdateAnystackLicenseExpiryJob($license, $newExpiryDate));

        logger('License renewal processed', [
            'license_id' => $license->id,
            'license_key' => $license->key,
            'old_expiry' => $license->getOriginal('expires_at'),
            'new_expiry' => $newExpiryDate,
            'subscription_id' => $this->invoice->subscription,
            'invoice_id' => $this->invoice->id,
        ]);
    }

    private function handleManualInvoice(): void
    {
        $metadata = $this->invoice->metadata->toArray();

        // Check for cart-based purchase first (new approach)
        if (! empty($metadata['cart_id'])) {
            $this->processCartPurchase($metadata['cart_id']);

            return;
        }

        // Legacy: Only process if this is a plugin purchase (has plugin metadata)
        if (empty($metadata['plugin_ids']) && empty($metadata['bundle_ids'])) {
            return;
        }

        $user = $this->billable();

        Log::info('Processing manual invoice for plugin purchase (legacy metadata)', [
            'invoice_id' => $this->invoice->id,
            'user_id' => $user->id,
            'metadata' => $metadata,
        ]);

        // Handle bundle purchases
        if (! empty($metadata['bundle_ids'])) {
            $this->processPluginBundles($user, $metadata);
        }

        // Handle individual plugin purchases
        if (! empty($metadata['plugin_ids'])) {
            $this->processPluginPurchases($user, $metadata);
        }

        // Ensure user has a plugin license key
        $user->getPluginLicenseKey();
    }

    private function processCartPurchase(string $cartId): void
    {
        $cart = Cart::with([
            'items.plugin.developerAccount',
            'items.pluginBundle.plugins.developerAccount',
            'items.product',
        ])->find($cartId);

        if (! $cart) {
            Log::error('Cart not found for invoice', [
                'invoice_id' => $this->invoice->id,
                'cart_id' => $cartId,
            ]);

            return;
        }

        // Idempotency: skip if cart already completed
        if ($cart->isCompleted()) {
            Log::info('Cart already processed, skipping', [
                'invoice_id' => $this->invoice->id,
                'cart_id' => $cartId,
            ]);

            return;
        }

        $user = $cart->user;

        if (! $user) {
            Log::error('Cart has no associated user', [
                'invoice_id' => $this->invoice->id,
                'cart_id' => $cartId,
            ]);

            return;
        }

        Log::info('Processing cart purchase from invoice', [
            'invoice_id' => $this->invoice->id,
            'cart_id' => $cartId,
            'user_id' => $user->id,
            'item_count' => $cart->items->count(),
        ]);

        foreach ($cart->items as $item) {
            if ($item->isProduct()) {
                $this->processCartProductItem($user, $item);
            } elseif ($item->isBundle()) {
                $this->processCartBundleItem($user, $item);
            } else {
                $this->processCartPluginItem($user, $item);
            }
        }

        // Mark cart as completed
        $cart->markAsCompleted();

        // Ensure user has a plugin license key
        $user->getPluginLicenseKey();

        Log::info('Cart purchase completed', [
            'invoice_id' => $this->invoice->id,
            'cart_id' => $cartId,
            'user_id' => $user->id,
        ]);
    }

    private function processCartPluginItem(User $user, CartItem $item): void
    {
        $plugin = $item->plugin;

        if (! $plugin) {
            Log::warning('Plugin not found for cart item', ['cart_item_id' => $item->id]);

            return;
        }

        // Check if license already exists for this invoice + plugin
        if (PluginLicense::where('stripe_invoice_id', $this->invoice->id)
            ->where('plugin_id', $plugin->id)
            ->whereNull('plugin_bundle_id')
            ->exists()) {
            Log::info('License already exists for plugin', [
                'invoice_id' => $this->invoice->id,
                'plugin_id' => $plugin->id,
            ]);

            return;
        }

        $this->createPluginLicense($user, $plugin, $item->price_at_addition);
    }

    private function processCartBundleItem(User $user, CartItem $item): void
    {
        $bundle = $item->pluginBundle;

        if (! $bundle) {
            Log::warning('Bundle not found for cart item', ['cart_item_id' => $item->id]);

            return;
        }

        // Load plugins with developer accounts if not already loaded
        $bundle->loadMissing('plugins.developerAccount');

        // Calculate proportional allocation based on price at time of addition
        $allocations = $bundle->calculateProportionalAllocation($item->bundle_price_at_addition);

        foreach ($bundle->plugins as $plugin) {
            // Check if license already exists for this invoice + plugin + bundle
            if (PluginLicense::where('stripe_invoice_id', $this->invoice->id)
                ->where('plugin_id', $plugin->id)
                ->where('plugin_bundle_id', $bundle->id)
                ->exists()) {
                continue;
            }

            $allocatedAmount = $allocations[$plugin->id] ?? 0;

            $this->createBundlePluginLicense($user, $plugin, $bundle, $allocatedAmount);
        }

        Log::info('Processed bundle from cart', [
            'invoice_id' => $this->invoice->id,
            'bundle_id' => $bundle->id,
            'bundle_name' => $bundle->name,
        ]);
    }

    private function processCartProductItem(User $user, CartItem $item): void
    {
        $product = $item->product;

        if (! $product) {
            Log::warning('Product not found for cart item', ['cart_item_id' => $item->id]);

            return;
        }

        // Check if license already exists for this invoice + product
        if (ProductLicense::where('stripe_invoice_id', $this->invoice->id)
            ->where('product_id', $product->id)
            ->exists()) {
            Log::info('License already exists for product', [
                'invoice_id' => $this->invoice->id,
                'product_id' => $product->id,
            ]);

            return;
        }

        $this->createProductLicense($user, $product, $item->product_price_at_addition);
    }

    private function createProductLicense(User $user, Product $product, int $amount): ProductLicense
    {
        $license = ProductLicense::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'stripe_invoice_id' => $this->invoice->id,
            'stripe_payment_intent_id' => $this->invoice->payment_intent,
            'price_paid' => $amount,
            'currency' => strtoupper($this->invoice->currency),
            'purchased_at' => now(),
        ]);

        Log::info('Created product license from invoice', [
            'invoice_id' => $this->invoice->id,
            'license_id' => $license->id,
            'product_id' => $product->id,
        ]);

        // Auto-grant GitHub repo access if applicable
        if ($product->hasGitHubRepoAccess() && $user->github_username) {
            $this->grantProductGitHubAccess($user, $product);
        }

        return $license;
    }

    private function grantProductGitHubAccess(User $user, Product $product): void
    {
        $github = GitHubOAuth::make();
        $success = $github->inviteToRepo($product->github_repo, $user->github_username);

        if ($success) {
            // Update user's repo access timestamp based on the repo
            if ($product->github_repo === 'claude-code') {
                $user->update([
                    'claude_plugins_repo_access_granted_at' => now(),
                ]);
            }

            Log::info('Granted GitHub repo access for product purchase', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'github_repo' => $product->github_repo,
            ]);
        } else {
            Log::warning('Failed to grant GitHub repo access for product purchase', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'github_repo' => $product->github_repo,
            ]);
        }
    }

    private function processPluginPurchases(User $user, array $metadata): void
    {
        $pluginIds = array_filter(explode(',', $metadata['plugin_ids']));
        $priceIds = array_filter(explode(',', $metadata['price_ids'] ?? ''));

        // Get already processed plugin IDs for this invoice
        $alreadyProcessedPluginIds = PluginLicense::where('stripe_invoice_id', $this->invoice->id)
            ->whereNull('plugin_bundle_id')
            ->pluck('plugin_id')
            ->toArray();

        foreach ($pluginIds as $index => $pluginId) {
            if (in_array((int) $pluginId, $alreadyProcessedPluginIds)) {
                continue;
            }

            $plugin = Plugin::find($pluginId);

            if (! $plugin) {
                Log::warning('Plugin not found during invoice processing', ['plugin_id' => $pluginId]);

                continue;
            }

            $priceId = $priceIds[$index] ?? null;
            $price = $priceId ? PluginPrice::find($priceId) : $plugin->activePrice;
            $amount = $price ? $price->amount : 0;

            $this->createPluginLicense($user, $plugin, $amount);
        }
    }

    private function processPluginBundles(User $user, array $metadata): void
    {
        $bundleIds = array_filter(explode(',', $metadata['bundle_ids']));
        $bundlePluginIds = json_decode($metadata['bundle_plugin_ids'] ?? '{}', true);

        foreach ($bundleIds as $bundleId) {
            $bundle = PluginBundle::with(['plugins.developerAccount', 'plugins.activePrice'])
                ->find($bundleId);

            if (! $bundle) {
                Log::warning('Bundle not found during invoice processing', ['bundle_id' => $bundleId]);

                continue;
            }

            // Check if bundle already fully processed for this invoice
            $existingLicenseCount = PluginLicense::where('stripe_invoice_id', $this->invoice->id)
                ->where('plugin_bundle_id', $bundleId)
                ->count();

            if ($existingLicenseCount === $bundle->plugins->count()) {
                continue;
            }

            // Calculate proportional allocation for developer payouts
            $allocations = $bundle->calculateProportionalAllocation();

            foreach ($bundle->plugins as $plugin) {
                // Skip if license already exists for this plugin in this invoice
                if (PluginLicense::where('stripe_invoice_id', $this->invoice->id)
                    ->where('plugin_id', $plugin->id)
                    ->where('plugin_bundle_id', $bundleId)
                    ->exists()) {
                    continue;
                }

                $allocatedAmount = $allocations[$plugin->id] ?? 0;

                $this->createBundlePluginLicense($user, $plugin, $bundle, $allocatedAmount);
            }

            Log::info('Processed bundle from invoice', [
                'invoice_id' => $this->invoice->id,
                'bundle_id' => $bundleId,
                'bundle_name' => $bundle->name,
            ]);
        }
    }

    private function createPluginLicense(User $user, Plugin $plugin, int $amount): PluginLicense
    {
        $license = PluginLicense::create([
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
            'stripe_invoice_id' => $this->invoice->id,
            'stripe_payment_intent_id' => $this->invoice->payment_intent,
            'price_paid' => $amount,
            'currency' => strtoupper($this->invoice->currency),
            'is_grandfathered' => false,
            'purchased_at' => now(),
        ]);

        // Create payout record for developer if applicable
        if ($plugin->developerAccount && $plugin->developerAccount->canReceivePayouts() && $amount > 0) {
            $split = PluginPayout::calculateSplit($amount);

            $payout = PluginPayout::create([
                'plugin_license_id' => $license->id,
                'developer_account_id' => $plugin->developerAccount->id,
                'gross_amount' => $amount,
                'platform_fee' => $split['platform_fee'],
                'developer_amount' => $split['developer_amount'],
                'status' => PayoutStatus::Pending,
            ]);

            $stripeConnectService = resolve(StripeConnectService::class);
            $stripeConnectService->processTransfer($payout);
        }

        Log::info('Created plugin license from invoice', [
            'invoice_id' => $this->invoice->id,
            'license_id' => $license->id,
            'plugin_id' => $plugin->id,
        ]);

        return $license;
    }

    private function createBundlePluginLicense(User $user, Plugin $plugin, PluginBundle $bundle, int $allocatedAmount): PluginLicense
    {
        $license = PluginLicense::create([
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
            'plugin_bundle_id' => $bundle->id,
            'stripe_invoice_id' => $this->invoice->id,
            'stripe_payment_intent_id' => $this->invoice->payment_intent,
            'price_paid' => $allocatedAmount,
            'currency' => strtoupper($this->invoice->currency),
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

        Log::info('Created bundle plugin license from invoice', [
            'invoice_id' => $this->invoice->id,
            'bundle_id' => $bundle->id,
            'plugin_id' => $plugin->id,
            'allocated_amount' => $allocatedAmount,
        ]);

        return $license;
    }

    private function billable(): User
    {
        if ($user = Cashier::findBillable($this->invoice->customer)) {
            return $user;
        }

        $customer = Cashier::stripe()->customers->retrieve($this->invoice->customer);

        dispatch_sync(new CreateUserFromStripeCustomer($customer));

        return Cashier::findBillable($this->invoice->customer);
    }
}
