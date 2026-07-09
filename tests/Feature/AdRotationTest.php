<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdRotationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_bifrost_ad_is_included_in_the_default_rotation()
    {
        $this->blade('<x-blog.ad-rotation />')
            ->assertSee('https://bifrost.nativephp.com/')
            ->assertSee("ad === 'bifrost'", false)
            ->assertSee('Cloud Platform')
            ->assertSee('Ship it!');
    }

    #[Test]
    public function the_bifrost_ad_can_be_rendered_on_its_own()
    {
        $this->blade('<x-blog.ad-rotation :ads="[\'bifrost\']" />')
            ->assertSee('https://bifrost.nativephp.com/')
            ->assertDontSee('/docs/mobile');
    }

    #[Test]
    public function the_bifrost_ad_is_omitted_when_not_in_the_ad_list()
    {
        $this->blade('<x-blog.ad-rotation :ads="[\'ultra\']" />')
            ->assertDontSee('https://bifrost.nativephp.com/')
            ->assertDontSee("ad === 'bifrost'", false);
    }

    #[Test]
    public function the_desktop_ad_is_included_in_the_default_rotation()
    {
        $this->blade('<x-blog.ad-rotation />')
            ->assertSee("ad === 'desktop'", false)
            ->assertSee('href="/docs/desktop"', false)
            ->assertSee('macbook');
    }

    #[Test]
    public function the_masterclass_ad_no_longer_shows_early_bird_pricing()
    {
        $this->blade('<x-blog.ad-rotation :ads="[\'masterclass\']" />')
            ->assertSee('The Masterclass')
            ->assertDontSee('Early Bird Pricing');
    }

    #[Test]
    public function desktop_docs_rotate_the_mobile_ad_but_never_the_desktop_ad()
    {
        $this->followingRedirects()
            ->get('/docs/desktop')
            ->assertOk()
            ->assertSee("ad === 'mobile'", false)
            ->assertDontSee("ad === 'desktop'", false);
    }

    #[Test]
    public function mobile_docs_rotate_the_desktop_ad_but_never_the_mobile_ad()
    {
        $this->followingRedirects()
            ->get('/docs/mobile')
            ->assertOk()
            ->assertSee("ad === 'desktop'", false)
            ->assertDontSee("ad === 'mobile'", false);
    }
}
