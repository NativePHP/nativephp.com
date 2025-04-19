<?php

namespace Tests\Feature\Stripe;

use App\Jobs\StripeWebhooks\HandleCustomerSubscriptionCreatedJob;
use Tests\TestCase;

class StripeWebhookConfigurationTest extends TestCase
{
    /** @test */
    public function it_has_customer_subscription_created_job_configured()
    {
        $stripeWebhookConfig = config('stripe-webhooks.jobs');

        $this->assertArrayHasKey('customer_subscription_created', $stripeWebhookConfig);
        $this->assertEquals(
            HandleCustomerSubscriptionCreatedJob::class,
            $stripeWebhookConfig['customer_subscription_created']
        );
    }
}
