<?php

namespace Tests\Feature\Stripe;

use App\Http\Middleware\VerifyCsrfToken;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StripeWebhookRouteTest extends TestCase
{
    #[Test]
    public function stripe_webhook_route_is_registered()
    {
        $response = $this->post('/stripe/webhook');

        $this->assertNotEquals(404, $response->getStatusCode());
    }

    #[Test]
    public function stripe_webhook_route_is_excluded_from_csrf_verification()
    {
        $reflection = new \ReflectionClass(VerifyCsrfToken::class);
        $property = $reflection->getProperty('except');
        $exceptPaths = $property->getValue(app(VerifyCsrfToken::class));

        $this->assertContains('stripe/webhook', $exceptPaths);
    }
}
