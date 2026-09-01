<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewsletterSignupTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_site_banner_offers_the_discount_and_opens_the_signup_modal()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Celebrate')
            ->assertSee('SuperNative!')
            ->assertSee('Join the newsletter')
            ->assertSee('10% off everything')
            ->assertSee('open-newsletter-modal', escape: false)
            ->assertSee('newsletter_banner_click', escape: false);
    }

    #[Test]
    public function the_footer_newsletter_card_opens_the_signup_modal_instead_of_leaving_the_site()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('newsletter_footer_click', escape: false)
            ->assertSee('10% discount code')
            ->assertDontSee('href="/newsletter"', escape: false);
    }

    #[Test]
    public function the_modal_posts_to_the_mailcoach_list_with_an_empty_honeypot()
    {
        config([
            'services.mailcoach.newsletter_subscribe_url' => 'https://example.mailcoach.app/subscribe/test-list',
            'services.mailcoach.honeypot_field' => 'pet',
        ]);

        $this->blade('<x-newsletter-modal />')
            ->assertSee('action="https://example.mailcoach.app/subscribe/test-list"', escape: false)
            ->assertSee('method="post"', escape: false)
            ->assertSee('name="pet"', escape: false)
            ->assertSee('name="email"', escape: false)
            // The honeypot must arrive empty, so it never carries a value.
            ->assertDontSee('name="pet" value', escape: false);
    }

    #[Test]
    public function the_modal_sends_mailcoach_back_to_our_own_pages()
    {
        $this->blade('<x-newsletter-modal />')
            ->assertSee('value="'.route('newsletter.confirm').'"', escape: false)
            ->assertSee('value="'.route('newsletter.subscribed').'"', escape: false)
            ->assertSee('value="'.route('newsletter.already-subscribed').'"', escape: false)
            ->assertSeeInOrder([
                'redirect_after_subscription_pending',
                'redirect_after_subscribed',
                'redirect_after_already_subscribed',
            ], escape: false);
    }

    #[Test]
    public function the_modal_is_available_on_every_page_of_the_site()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('newsletter-modal-heading', escape: false)
            ->assertSee(config('services.mailcoach.newsletter_subscribe_url'), escape: false);
    }

    #[Test]
    public function the_confirmation_page_tells_people_to_check_their_inbox()
    {
        $this->get(route('newsletter.confirm'))
            ->assertOk()
            ->assertSee('Check your inbox')
            ->assertSee('confirm your subscription')
            ->assertSee('10% discount code')
            ->assertSee('noindex, nofollow', escape: false);
    }

    #[Test]
    public function the_subscribed_page_confirms_the_discount_code_is_on_its_way()
    {
        $this->get(route('newsletter.subscribed'))
            ->assertOk()
            ->assertSee("You're in!")
            ->assertSee('10% discount code')
            ->assertSee(route('pricing'), escape: false);
    }

    #[Test]
    public function the_already_subscribed_page_explains_there_is_nothing_to_do()
    {
        $this->get(route('newsletter.already-subscribed'))
            ->assertOk()
            ->assertSee("You're already on the list")
            ->assertSee('already subscribed');
    }

    #[Test]
    public function the_unsubscribed_page_confirms_the_removal_and_offers_a_way_back()
    {
        $this->get(route('newsletter.unsubscribed'))
            ->assertOk()
            ->assertSee("You've been unsubscribed")
            ->assertSee('subscribe again')
            ->assertSee('open-newsletter-modal', escape: false);
    }
}
