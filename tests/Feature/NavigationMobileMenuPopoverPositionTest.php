<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationMobileMenuPopoverPositionTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_popover_is_positioned_when_opened_rather_than_at_alpine_init(): void
    {
        $response = $this->get('/page-that-does-not-exist');

        $response->assertStatus(404);

        // The 404 page renders the shared navigation with the menu popover
        $response->assertSee('id="mobile-menu-popover"', false);

        // Positioning must happen inside the open-time updater, where the layout
        // is measurable. An x-bind:style binding evaluates during Alpine init while
        // <body x-cloak> is still display:none, so every measured rect is 0 and the
        // popover ends up off-screen.
        $response->assertDontSee('x-bind:style', false);
        $response->assertSee('$refs.mobilePopover.style.top', false);
        $response->assertSee('$refs.mobilePopover.style.right', false);
    }

    public function test_menu_popover_repositions_while_open_on_scroll_and_resize(): void
    {
        $response = $this->get('/page-that-does-not-exist');

        $response->assertStatus(404);

        $response->assertSee("window.addEventListener('scroll', updatePopoverPosition", false);
        $response->assertSee("window.addEventListener('resize', updatePopoverPosition)", false);
        $response->assertSee("window.removeEventListener('scroll', updatePopoverPosition)", false);
        $response->assertSee("window.removeEventListener('resize', updatePopoverPosition)", false);
    }
}
