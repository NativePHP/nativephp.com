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
}
