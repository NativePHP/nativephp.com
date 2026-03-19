<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationMobileMenuBreakpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_menu_resize_handler_uses_media_query_breakpoint(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee("window.matchMedia('(min-width: 80rem)').matches", false);
    }
}
