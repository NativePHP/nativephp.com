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

        $nameParts = explode(' ', $user->name ?? '', 2);
        $firstName = $nameParts[0] ?: null;
        $lastName = $nameParts[1] ?? null;

        dispatch(new CreateAnystackLicenseJob(
            $user,
            $subscriptionPlan,
            $firstName,
            $lastName,
        ));
    }

    protected function constructStripeSubscription(): ?Subscription
    {
        return Subscription::constructFrom($this->webhook->payload['data']['object']);
    }
}
