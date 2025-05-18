<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobileRouteTest extends TestCase
{
    #[Test]
    public function mobile_route_does_not_include_stripe_payment_links()
    {
        $this
            ->withoutVite()
            ->get(route('early-adopter'))
            ->assertDontSee('buy.stripe.com');
    }

    #[Test]
    public function mobile_route_includes_mobile_pricing_livewire_component()
    {
        $this
            ->withoutVite()
            ->get(route('early-adopter'))
            ->assertSeeLivewire('mobile-pricing');
    }
}
