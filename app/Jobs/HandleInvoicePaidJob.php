<?php

namespace App\Jobs;

use App\Enums\Subscription;
use App\Exceptions\InvalidStateException;
use App\Models\License;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        match ($this->invoice->billing_reason) {
            Invoice::BILLING_REASON_SUBSCRIPTION_CREATE => $this->handleSubscriptionCreated(),
            Invoice::BILLING_REASON_SUBSCRIPTION_UPDATE => null, // TODO: Handle subscription update
            Invoice::BILLING_REASON_SUBSCRIPTION_CYCLE => $this->handleSubscriptionRenewal(),
            Invoice::BILLING_REASON_MANUAL => null,
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
        $newExpiryDate = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end);

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
        sleep(10);

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
        $newExpiryDate = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end);

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
