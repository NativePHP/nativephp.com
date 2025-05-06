<?php

namespace App\Listeners;

use App\Jobs\HandleCustomerSubscriptionCreatedJob;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookHandled;

class StripeWebhookHandledListener
{
    public function handle(WebhookHandled $event): void
    {
        Log::debug('Webhook handled', $event->payload);

        match ($event->payload['type']) {
            'customer.subscription.created' => dispatch(new HandleCustomerSubscriptionCreatedJob($event)),
            default => null,
        };
    }
}
