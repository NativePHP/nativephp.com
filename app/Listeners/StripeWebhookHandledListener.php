<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookHandled;

class StripeWebhookHandledListener
{
    public function handle(WebhookHandled $event): void
    {
        Log::debug('Webhook handled', $event->payload);
    }
}
