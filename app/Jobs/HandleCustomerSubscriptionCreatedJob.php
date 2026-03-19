<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\SubscriptionItem;
use Stripe\Subscription;

class HandleCustomerSubscriptionCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WebhookHandled $webhook) {}

    public function handle(): void
    {
        $stripeSubscription = $this->constructStripeSubscription();

        if (! $stripeSubscription) {
            $this->fail('The Stripe webhook payload could not be constructed into a Stripe Subscription object.');

            return;
        }

        /** @var User $user */
        $user = Cashier::findBillable($stripeSubscription->customer);

        if (! $user || ! $user->email) {
            $this->fail('Failed to find user from Stripe subscription customer.');

            return;
        }

        $subscriptionPlan = \App\Enums\Subscription::fromStripeSubscription($stripeSubscription);
        $cashierSubscriptionItemId = SubscriptionItem::query()
            ->where('stripe_id', $stripeSubscription->items->first()->id)
            ->first()
            ->id;

        // Check if this is a renewal (has renewal metadata)
        $isRenewal = isset($stripeSubscription->metadata['renewal']) && $stripeSubscription->metadata['renewal'] === 'true';
        $licenseKey = $stripeSubscription->metadata['license_key'] ?? null;
        $licenseId = $stripeSubscription->metadata['license_id'] ?? null;

        if ($isRenewal && $licenseKey && $licenseId) {
            // This is a renewal - link the subscription to the existing license
            $license = \App\Models\License::where('id', $licenseId)
                ->where('key', $licenseKey)
                ->where('user_id', $user->id) // Ensure user owns the license
                ->first();

            if ($license) {
                // Link the subscription to the existing license
                $license->update([
                    'subscription_item_id' => $cashierSubscriptionItemId,
                ]);

                // Log this renewal
                logger('License renewal completed', [
                    'license_id' => $license->id,
                    'license_key' => $license->key,
                    'user_id' => $user->id,
                    'subscription_item_id' => $cashierSubscriptionItemId,
                ]);

                return; // Exit early - don't create a new license
            } else {
                // License not found - log error but continue with normal flow
                logger('Renewal license not found, creating new license instead', [
                    'license_key' => $licenseKey,
                    'license_id' => $licenseId,
                    'user_id' => $user->id,
                ]);
            }
        }

        // Normal flow - create a new license
        $nameParts = explode(' ', $user->name ?? '', 2);
        $firstName = $nameParts[0] ?: null;
        $lastName = $nameParts[1] ?? null;

        dispatch(new CreateAnystackLicenseJob(
            $user,
            $subscriptionPlan,
            $cashierSubscriptionItemId,
            $firstName,
            $lastName,
        ));
    }

    protected function constructStripeSubscription(): ?Subscription
    {
        return Subscription::constructFrom($this->webhook->payload['data']['object']);
    }
}
