<?php

namespace App\Jobs\StripeWebhooks;

use App\Jobs\CreateAnystackLicenseJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Subscription;

class HandleCustomerSubscriptionCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WebhookCall $webhook) {}

    public function handle(): void
    {
        $stripeSubscription = $this->constructStripeSubscription();

        if (! $stripeSubscription) {
            $this->fail('The Stripe webhook payload could not be constructed into a Stripe Subscription object.');

            return;
        }

        $customer = app(StripeClient::class)
            ->customers
            ->retrieve($stripeSubscription->customer);

        if (! $customer || ! ($email = $customer->email)) {
            $this->fail('Failed to retrieve customer information or customer has no email.');

            return;
        }

        $subscriptionPlan = \App\Enums\Subscription::fromStripeSubscription($stripeSubscription);

        $nameParts = explode(' ', $customer->name ?? '', 2);
        $firstName = $nameParts[0] ?: null;
        $lastName = $nameParts[1] ?? null;

        dispatch(new CreateAnystackLicenseJob(
            $email,
            $subscriptionPlan,
            $firstName,
            $lastName,
        ));
    }

    protected function constructStripeSubscription(): ?Subscription
    {
        return Event::constructFrom($this->webhook->payload)->data?->object;
    }
}
