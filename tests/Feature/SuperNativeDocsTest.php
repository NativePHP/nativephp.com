<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperNativeDocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_native_edge_page_renders_beta_banner(): void
    {
        $response = $this->get('/docs/mobile/3/edge-components/button');

        $response->assertOk();
        $response->assertSee('This is a Super Native feature');
        $response->assertSee('role="note"', false);
    }

    public function test_legacy_edge_pages_do_not_render_beta_banner(): void
    {
        // top-bar pre-dates Super Native; web-view existed on main as a basics page.
        $this->get('/docs/mobile/3/edge-components/top-bar')
            ->assertOk()
            ->assertDontSee('This is a Super Native feature');

        $this->get('/docs/mobile/3/edge-components/web-view')
            ->assertOk()
            ->assertDontSee('This is a Super Native feature');
    }

    public function test_navigation_marks_only_super_native_labels_with_a_shield(): void
    {
        // Use a legacy page so the only source of the shield icon is the nav badges,
        // not a beta banner in the page body.
        $response = $this->get('/docs/mobile/3/edge-components/top-bar');
        $content = $response->getContent();

        $response->assertOk();
        $response->assertDontSee('This is a Super Native feature');

        // A Super Native component label carries the shield badge.
        $this->assertMatchesRegularExpression(
            '#edge-components/button">Button<svg[^>]*lucide-super-native#s',
            $content,
        );

        // A legacy component label does not.
        $this->assertDoesNotMatchRegularExpression(
            '#edge-components/top-bar">Top Bar<svg[^>]*lucide-super-native#s',
            $content,
        );

        $this->assertDoesNotMatchRegularExpression(
            '#edge-components/web-view">Web View<svg[^>]*lucide-super-native#s',
            $content,
        );
    }
}
