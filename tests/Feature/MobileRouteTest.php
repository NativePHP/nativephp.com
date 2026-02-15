<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobileRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable the auth feature flag since the pricing page might reference it
        Feature::define(ShowAuthButtons::class, false);
    }

    #[Test]
    public function mobile_route_does_not_include_stripe_payment_links()
    {
        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertRedirect('/blog/nativephp-for-mobile-is-now-free');
    }

    #[Test]
    public function mobile_route_includes_mobile_pricing_livewire_component()
    {
        $signedUrl = URL::signedRoute('alt-pricing');

        $this
            ->withoutVite()
            ->get($signedUrl)
            ->assertStatus(200)
            ->assertSeeLivewire('mobile-pricing');
    }
}
