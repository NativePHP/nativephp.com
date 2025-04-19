<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobileRouteTest extends TestCase
{
    #[Test]
    public function mobile_route_includes_stripe_payment_links()
    {
        $mockLinks = [
            'mini' => ['stripe_payment_link' => 'https://buy.stripe.com/mini-payment'],
            'pro' => ['stripe_payment_link' => 'https://buy.stripe.com/pro-payment'],
            'max' => ['stripe_payment_link' => 'https://buy.stripe.com/max-payment'],
        ];

        Config::set('subscriptions.plans', $mockLinks);

        $response = $this->withoutVite()->get(route('early-adopter'))->getContent();

        $this->assertStringContainsString($mockLinks['mini']['stripe_payment_link'], $response);
        $this->assertStringContainsString($mockLinks['pro']['stripe_payment_link'], $response);
        $this->assertStringContainsString($mockLinks['max']['stripe_payment_link'], $response);
    }
}
