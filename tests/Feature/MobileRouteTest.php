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
    public function pricing_route_redirects_to_blog_post()
    {
        $this
            ->get(route('pricing'))
            ->assertRedirect('blog/nativephp-for-mobile-is-now-free');
    }

    #[Test]
    public function alt_pricing_route_does_not_include_stripe_payment_links()
    {
        $this
            ->withoutVite()
            ->get(URL::signedRoute('alt-pricing'))
            ->assertDontSee('buy.stripe.com');
    }

    #[Test]
    public function alt_pricing_route_includes_mobile_pricing_livewire_component()
    {
        $this
            ->withoutVite()
            ->get(URL::signedRoute('alt-pricing'))
            ->assertSeeLivewire('mobile-pricing');
    }
}
