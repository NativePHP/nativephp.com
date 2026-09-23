<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TheVibesBannerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_banner_counts_down_to_ticket_sales_closing_at_midnight_in_boston()
    {
        $this->travelTo(Carbon::parse('2026-07-29T23:59:59-04:00'));

        $this->blade('<x-the-vibes-banner />')
            ->assertSee('2026-07-30T00:00:00-04:00', false)
            ->assertSeeInOrder(['x-text="days"', 'x-text="hours"', 'x-text="minutes"', 'x-text="seconds"'], false);
    }

    #[Test]
    public function the_banner_disappears_once_ticket_sales_close()
    {
        $this->travelTo(Carbon::parse('2026-07-30T00:00:00-04:00'));

        $this->blade('<x-the-vibes-banner />')
            ->assertDontSee('2026-07-30T00:00:00-04:00', false)
            ->assertDontSee('Get your ticket');
    }

    /**
     * The site banner slot now announces Mobile v4 and SuperNative, so the
     * Vibes banner is no longer mounted on the page — even while tickets are
     * still on sale. Its countdown behaviour stays covered above, and the
     * replacement is covered by SupernativeBannerTest.
     */
    #[Test]
    public function the_homepage_no_longer_carries_the_vibes_banner()
    {
        $this->travelTo(Carbon::parse('2026-07-23T12:00:00-04:00'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('2026-07-30T00:00:00-04:00', false)
            ->assertDontSee('Get your ticket');
    }
}
