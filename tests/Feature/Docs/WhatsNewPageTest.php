<?php

namespace Tests\Feature\Docs;

use App\Support\DocsLabels;
use App\Support\GitHub\Release;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    #[Test]
    public function it_links_each_minor_version_at_its_latest_patch_in_the_changelog(): void
    {
        Cache::put('nativephp/mobile-air-releases', collect(['4.2.0', '4.2.3', '4.1.1'])->map(
            fn (string $name) => new Release(['tag_name' => $name, 'name' => $name]),
        ));

        $changelog = route('docs.show', ['platform' => 'mobile', 'version' => 4, 'page' => 'getting-started/changelog']);

        $this->get('/docs/mobile/4/whats-new')
            ->assertStatus(200)
            ->assertSee($changelog.'#423')
            ->assertSee('4.2.3 in the changelog')
            ->assertDontSee($changelog.'#420');
    }

    #[Test]
    public function it_renders_minor_headings_without_a_link_when_github_is_unreachable(): void
    {
        Cache::put('nativephp/mobile-air-releases', collect());

        $this->get('/docs/mobile/4/whats-new')
            ->assertStatus(200)
            ->assertSee('4.2')
            ->assertDontSee('in the changelog');
    }
}
