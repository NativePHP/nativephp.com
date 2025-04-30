<?php

namespace App\Listeners;

use App\Jobs\CreateUserFromStripeCustomer;
use Illuminate\Support\Facades\Log;
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
            default => null,
        };
    }
}
