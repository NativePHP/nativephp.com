<?php

namespace Tests\Feature;

use App\Services\DocsVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DocsPrereleaseVersionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // These tests render full docs pages that contain fenced code blocks.
        // Torchlight throws outside production when no token is configured (as
        // in CI), so give it a token and fake the API to force its offline
        // fallback — the pages render deterministically without a real token.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);
    }

    public function test_prerelease_pages_show_the_beta_notice(): void
    {
        $response = $this->get('/docs/mobile/4/edge-components/button');

        $response->assertOk();
        $response->assertSee('pre-release documentation');
        $response->assertSee('View the stable version (3.x)');
    }

    public function test_beta_notice_links_to_the_same_page_when_it_exists_on_stable(): void
    {
        // top-bar exists in v3 edge-components, so the notice should deep-link to it.
        $this->get('/docs/mobile/4/edge-components/top-bar')
            ->assertOk()
            ->assertSee('/docs/mobile/3/edge-components/top-bar');

        // button does not exist in v3, so the notice falls back to the introduction.
        $this->get('/docs/mobile/4/edge-components/button')
            ->assertOk()
            ->assertSee('/docs/mobile/3/getting-started/introduction');
    }

    public function test_stable_pages_do_not_show_the_beta_notice(): void
    {
        $this->get('/docs/mobile/3/getting-started/introduction')
            ->assertOk()
            ->assertDontSee('pre-release documentation');
    }

    public function test_unversioned_docs_urls_redirect_to_the_stable_version(): void
    {
        $this->get('/docs/mobile')
            ->assertRedirect('/docs/mobile/3/getting-started/introduction');

        $this->get('/docs/mobile/the-basics/web-view')
            ->assertRedirect('/docs/mobile/3/the-basics/web-view');
    }

    public function test_version_switcher_lists_newest_first_and_labels_beta(): void
    {
        $response = $this->get('/docs/mobile/4/getting-started/introduction');
        $content = $response->getContent();

        $response->assertOk();
        $response->assertSee('Version 4.x (beta)');

        // Newest version appears before older ones in the dropdown.
        $this->assertMatchesRegularExpression(
            '#<option value="4">[^<]*</option>\s*<option value="3">#s',
            $content,
        );
    }

    public function test_supernative_shield_badges_are_removed_from_navigation(): void
    {
        $this->get('/docs/mobile/4/edge-components/button')
            ->assertOk()
            ->assertDontSee('lucide-super-native')
            ->assertDontSee('This is a SuperNative feature');
    }

    public function test_renamed_pages_redirect_to_their_equivalent_within_a_version(): void
    {
        // Old slug on the new version forwards to the new slug.
        $this->get('/docs/mobile/4/the-basics/native-components')
            ->assertRedirect('/docs/mobile/4/the-basics/native-ui');

        // New slug on an old version maps back to the old slug.
        $this->get('/docs/mobile/3/the-basics/native-ui')
            ->assertRedirect('/docs/mobile/3/the-basics/native-components');
    }

    public function test_beta_notice_maps_renamed_pages_to_their_stable_equivalent(): void
    {
        $this->get('/docs/mobile/4/the-basics/native-ui')
            ->assertOk()
            ->assertSee('/docs/mobile/3/the-basics/native-components');
    }

    public function test_version_switcher_maps_renamed_pages_between_versions(): void
    {
        $service = app(DocsVersionService::class);

        $this->assertSame(
            'the-basics/native-ui',
            $service->resolvePageForVersion('mobile', 4, 'the-basics/native-components'),
        );

        $this->assertSame(
            'the-basics/native-components',
            $service->resolvePageForVersion('mobile', 3, 'the-basics/native-ui'),
        );

        $this->assertSame(
            'the-basics/overview',
            $service->resolvePageForVersion('mobile', 3, 'the-basics/overview'),
        );
    }

    public function test_concepts_pages_redirect_to_digging_deeper_within_v4(): void
    {
        $this->get('/docs/mobile/4/concepts/security')
            ->assertRedirect('/docs/mobile/4/digging-deeper/security');
    }

    public function test_old_super_native_urls_redirect_to_their_new_homes(): void
    {
        // The old SuperNative section root forwards to the overview page in Architecture.
        $this->get('/docs/mobile/4/super-native')
            ->assertRedirect('/docs/mobile/4/architecture/super-native');

        // The old SuperNative introduction now forwards to the Architecture page.
        $this->get('/docs/mobile/4/super-native/introduction')
            ->assertRedirect('/docs/mobile/4/architecture/super-native');

        // Navigation and Layouts moved into The Basics (Navigation renamed to Routing).
        $this->get('/docs/mobile/4/super-native/navigation')
            ->assertRedirect('/docs/mobile/4/the-basics/routing');
        $this->get('/docs/mobile/4/super-native/layouts')
            ->assertRedirect('/docs/mobile/4/the-basics/layouts');

        // Other former sub-pages forward to direct Digging Deeper pages.
        $this->get('/docs/mobile/4/super-native/data-binding')
            ->assertRedirect('/docs/mobile/4/digging-deeper/data-binding');
    }

    public function test_removed_architecture_overview_redirects_to_super_native(): void
    {
        $this->get('/docs/mobile/4/architecture/overview')
            ->assertRedirect('/docs/mobile/4/architecture/super-native');
    }

    public function test_deployment_page_moved_into_the_publishing_section(): void
    {
        // Old getting-started/deployment URL forwards to the new Publishing intro.
        $this->get('/docs/mobile/4/getting-started/deployment')
            ->assertRedirect('/docs/mobile/4/publishing/introduction');

        // The three split pages render.
        $this->get('/docs/mobile/4/publishing/introduction')->assertOk()->assertSee('Releasing');
        $this->get('/docs/mobile/4/publishing/android')->assertOk()->assertSee('Play Store');
        $this->get('/docs/mobile/4/publishing/ios')->assertOk()->assertSee('App Store');
    }

    public function test_digging_deeper_slug_maps_back_to_concepts_on_the_stable_version(): void
    {
        $this->get('/docs/mobile/3/digging-deeper/security')
            ->assertRedirect('/docs/mobile/3/concepts/security');
    }

    public function test_the_basics_routing_and_layouts_pages(): void
    {
        // Navigation was renamed to Routing; the old slug forwards to it.
        $this->get('/docs/mobile/4/the-basics/navigation')
            ->assertRedirect('/docs/mobile/4/the-basics/routing');

        // Routing and Layouts now render directly in The Basics.
        $this->get('/docs/mobile/4/the-basics/routing')->assertOk();
        $this->get('/docs/mobile/4/the-basics/layouts')->assertOk();
    }

    public function test_moved_core_builtin_docs_redirect_from_plugins_core_to_the_basics(): void
    {
        $this->get('/docs/mobile/4/plugins/core/device')
            ->assertRedirect('/docs/mobile/4/the-basics/device');
    }

    public function test_vibe_docs_moved_to_websockets_with_a_core_plugin_shortcut(): void
    {
        // The old Vibe plugin doc now lives at Digging Deeper > WebSockets.
        $this->get('/docs/mobile/4/plugins/vibe')
            ->assertRedirect('/docs/mobile/4/digging-deeper/websockets');
        $this->get('/docs/mobile/4/digging-deeper/websockets')->assertOk();

        // The Core Plugins shortcut forwards to the plugin directory page.
        $this->get('/docs/mobile/4/plugins/core/vibe')
            ->assertRedirect('/plugins/nativephp/mobile-vibe');
    }

    public function test_dialog_page_uses_the_dialogs_slug(): void
    {
        // Old plugins/core slug forwards straight to the new dialogs page.
        $this->get('/docs/mobile/4/plugins/core/dialog')
            ->assertRedirect('/docs/mobile/4/the-basics/dialogs');

        // The old the-basics/dialog slug forwards to the-basics/dialogs.
        $this->get('/docs/mobile/4/the-basics/dialog')
            ->assertRedirect('/docs/mobile/4/the-basics/dialogs');

        // The renamed page renders.
        $this->get('/docs/mobile/4/the-basics/dialogs')
            ->assertOk()
            ->assertSee('Dialogs');
    }

    public function test_flattened_digging_deeper_and_architecture_pages_render(): void
    {
        // The former SuperNative introduction, now the SuperNative page in Architecture.
        $this->get('/docs/mobile/4/architecture/super-native')
            ->assertOk()
            ->assertSee('SuperNative');

        // A flattened former sub-page and an original concept page.
        $this->get('/docs/mobile/4/digging-deeper/data-binding')
            ->assertOk();

        $this->get('/docs/mobile/4/digging-deeper/security')
            ->assertOk();
    }
}
