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
        $this->travelTo(Carbon::parse('2026-12-01T07:59:59Z'));

        $this->get('/')
            ->assertOk()
            ->assertSee('bifrost_black_friday_banner_click', escape: false)
            ->assertDontSee('bifrost_birthday_banner_click', escape: false);
    }

    #[Test]
    public function the_waitlist_banner_replaces_the_black_friday_banner_once_the_sale_ends(): void
    {
        $this->travelTo(Carbon::parse('2026-12-01T08:00:00Z'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('bifrost_black_friday_banner_click', escape: false)
            ->assertDontSee('bifrost_birthday_banner_click', escape: false)
            ->assertSee('bifrost_waitlist_banner_click', escape: false);
    }

    #[Test]
    public function the_waitlist_banner_opens_the_waitlist_modal(): void
    {
        $this->blade('<x-bifrost-waitlist-banner />')
            ->assertSee('Ship your PHP app to the App Store without the usual pain.')
            ->assertSee('open-bifrost-waitlist-modal', escape: false)
            ->assertSee('bifrost_waitlist_banner_click', escape: false)
            ->assertDontSee('href=', escape: false);
    }

    #[Test]
    public function the_waitlist_modal_posts_name_and_email_to_the_kit_form(): void
    {
        config(['services.kit.waitlist_subscribe_url' => 'https://app.kit.com/forms/123/subscriptions']);

        $this->blade('<x-bifrost-waitlist-modal />')
            ->assertSee('action="https://app.kit.com/forms/123/subscriptions"', escape: false)
            ->assertSee('name="email_address"', escape: false)
            ->assertSee('name="fields[first_name]"', escape: false)
            ->assertSee('@open-bifrost-waitlist-modal.window', escape: false);
    }

    #[Test]
    public function the_waitlist_modal_opens_itself_from_the_waitlist_link(): void
    {
        $this->get('/?waitlist=1')
            ->assertOk()
            ->assertSee('bifrost-waitlist-modal-heading', escape: false)
            ->assertSee('x-init="show()"', escape: false);
    }

    #[Test]
    public function every_page_carries_the_waitlist_modal(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('@open-bifrost-waitlist-modal.window', escape: false);
    }
}
