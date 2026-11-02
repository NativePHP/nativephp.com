<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobileRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, false);
    }

    #[Test]
    public function pricing_route_returns_pricing_page()
    {
        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertOk()
            ->assertSeeLivewire('mobile-pricing');
    }

    #[Test]
    public function pricing_page_lists_the_third_party_plugin_discount()
    {
        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertOk()
            ->assertSee('Around 30% off third-party plugins')
            ->assertSee('around 30% off');
    }
}
