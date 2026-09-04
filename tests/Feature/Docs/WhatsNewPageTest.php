<?php

namespace Tests\Feature\Docs;

use App\Support\DocsLabels;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WhatsNewPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_for_mobile_v4_and_lists_a_known_labelled_page(): void
    {
        $this->get('/docs/mobile/4/whats-new')
            ->assertStatus(200)
            ->assertSee('4.2')
            ->assertSee('Layout');
    }

    #[Test]
    public function it_renders_for_desktop_v2_with_no_labelled_pages_yet(): void
    {
        $this->get('/docs/desktop/2/whats-new')
            ->assertStatus(200)
            ->assertSee('Nothing labelled yet');
    }

    #[Test]
    public function it_404s_for_an_unknown_platform_version(): void
    {
        $this->get('/docs/mobile/99/whats-new')->assertNotFound();
    }

    #[Test]
    public function docs_labels_resolves_the_whats_new_url_from_the_current_route(): void
    {
        $request = Request::create('/docs/mobile/4/getting-started/introduction');
        $route = app('router')->getRoutes()->match($request);
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        app()->instance('request', $request);

        $this->assertSame(
            route('docs.whats-new', ['platform' => 'mobile', 'version' => '4']),
            DocsLabels::whatsNewUrl(),
        );
    }
}
