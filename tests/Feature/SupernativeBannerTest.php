<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupernativeBannerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_banner_announces_supernative_v4()
    {
        $this->blade('<x-supernative-banner />')
            ->assertSee('NativePHP for Mobile v4')
            ->assertSee('/docs/mobile/4/architecture/super-native', escape: false)
            ->assertSee('supernative_banner_click', escape: false);
    }

    /**
     * The site banner slot now carries the newsletter discount offer, which keeps
     * the v4 headline but sends the click to the signup modal instead of the docs.
     * The replacement is covered by NewsletterSignupTest.
     */
    #[Test]
    public function the_homepage_no_longer_links_the_banner_straight_to_the_supernative_docs()
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('supernative_banner_click', escape: false);
    }
}
