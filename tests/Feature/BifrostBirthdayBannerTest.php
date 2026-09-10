<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BifrostBirthdayBannerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_banner_offers_the_birthday_discount_on_annual_bifrost_plans(): void
    {
        $this->blade('<x-bifrost-birthday-banner />')
            ->assertSee('Bifrost turns 1!')
            ->assertSee('30% off')
            ->assertSee('HAPPYBIRTHDAY')
            ->assertSee('https://bifrost.nativephp.com/pricing?billing=yearly', escape: false)
            ->assertSee('bifrost_birthday_banner_click', escape: false);
    }

    #[Test]
    public function the_homepage_carries_the_birthday_banner_instead_of_the_newsletter_offer_during_the_sale(): void
    {
        $this->travelTo(Carbon::parse('2026-09-30T23:59:59Z'));

        $this->get('/')
            ->assertOk()
            ->assertSee('HAPPYBIRTHDAY')
            ->assertDontSee('newsletter_banner_click', escape: false);
    }

    #[Test]
    public function the_newsletter_offer_takes_the_banner_slot_back_once_the_sale_ends(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01T00:00:00Z'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('HAPPYBIRTHDAY')
            ->assertSee('newsletter_banner_click', escape: false);
    }
}
