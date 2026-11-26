<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BifrostBlackFridayBannerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_banner_sends_people_to_the_bifrost_newsletter_in_a_new_tab(): void
    {
        $this->blade('<x-black-friday-banner />')
            ->assertSee('Black Friday:')
            ->assertSee('50% off')
            ->assertSee('href="https://bifrost.nativephp.com/newsletter"', escape: false)
            ->assertSee('target="_blank"', escape: false)
            ->assertSee('rel="noopener"', escape: false)
            ->assertSee('bifrost_black_friday_banner_click', escape: false);
    }

    #[Test]
    public function the_banner_never_shows_a_discount_code(): void
    {
        $this->blade('<x-black-friday-banner />')
            ->assertDontSee('BLACKFRIDAY')
            ->assertDontSee('HAPPYBIRTHDAY');
    }

    #[Test]
    public function the_homepage_carries_the_black_friday_banner_until_the_sale_ends(): void
    {
        $this->travelTo(Carbon::parse('2026-12-01T11:59:59Z'));

        $this->get('/')
            ->assertOk()
            ->assertSee('bifrost_black_friday_banner_click', escape: false)
            ->assertDontSee('bifrost_birthday_banner_click', escape: false);
    }

    #[Test]
    public function the_birthday_banner_comes_back_once_the_sale_ends(): void
    {
        $this->travelTo(Carbon::parse('2026-12-01T12:00:00Z'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('bifrost_black_friday_banner_click', escape: false)
            ->assertSee('bifrost_birthday_banner_click', escape: false);
    }
}
