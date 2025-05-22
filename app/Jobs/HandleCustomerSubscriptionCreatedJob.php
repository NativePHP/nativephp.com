<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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

        if ($stripeSubscription->status !== 'active') {
            Log::info("The subscription for customer [{$stripeSubscription->customer}] is not active. Not proceeding to license creation.");

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
