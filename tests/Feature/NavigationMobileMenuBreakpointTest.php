<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationMobileMenuBreakpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_menu_is_visible_on_all_screen_sizes(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // The mobile menu should not have xl:hidden — it's visible on all breakpoints
        $response->assertDontSee('class="relative z-40 xl:hidden"', false);
        $response->assertSee('class="relative z-40"', false);
    }

    public function test_navigation_bar_does_not_show_consulting_link(): void
    {
        $this->blade('<x-navigation-bar />')
            ->assertDontSee('Consulting');
    }

    public function test_mobile_menu_does_not_show_consulting_link(): void
    {
        $this->blade('<x-navbar.mobile-menu />')
            ->assertDontSee('Consulting');
    }

    public function test_resize_handler_does_not_auto_close_mobile_menu(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // The old breakpoint-based auto-close should be removed
        $response->assertDontSee("window.matchMedia('(min-width: 80rem)').matches", false);
    }
}
