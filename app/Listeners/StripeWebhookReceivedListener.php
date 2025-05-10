<?php

namespace App\Listeners;

use App\Jobs\CreateUserFromStripeCustomer;
use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Customer;

class StripeWebhookReceivedListener
{
    public function handle(WebhookReceived $event): void
    {
        Log::debug('Webhook received', $event->payload);

        match ($event->payload['type']) {
            // 'customer.created' must be dispatched sync so the user is
            // created before the cashier webhook handling is executed.
            'customer.created' => dispatch_sync(new CreateUserFromStripeCustomer(
                Customer::constructFrom($event->payload['data']['object'])
            )),
            'customer.subscription.created' => $this->createUserIfNotExists($event->payload['data']['object']['customer']),
            default => null,
        };
    }

    private function createUserIfNotExists(string $stripeCustomerId): void
    {
        if (Cashier::findBillable($stripeCustomerId)) {
            return;
        }

        $customer = Customer::retrieve($stripeCustomerId);

        if (! $customer) {
            throw new Exception(
                'A user needed to be created for customer.subscription.created but was unable to retrieve the customer from Stripe.'
            );
        }

        dispatch_sync(new CreateUserFromStripeCustomer($customer));
    }
}
