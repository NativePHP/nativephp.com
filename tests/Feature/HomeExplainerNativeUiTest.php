<?php

namespace Tests\Feature;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Torchlight\Blade\BladeManager;

class HomeExplainerNativeUiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Torchlight's block registry is a static array. If a request that
     * registered blocks throws before the RenderTorchlight middleware clears
     * them, they leak into every later HTML response in the same PHPUnit
     * process — 500ing unrelated tests. Clear them unconditionally.
     */
    protected function tearDown(): void
    {
        BladeManager::clearBlocks();

        parent::tearDown();
    }

    #[Test]
    public function the_mobile_path_leads_with_native_rendering_not_a_web_view()
    {
        $this->blade('<x-home.explainer />')
            ->assertSee('SwiftUI')
            ->assertSee('Jetpack Compose')
            ->assertSee('shared memory.')
            ->assertSee('No web view. No JSON bridge.')
            ->assertDontSee('Native WebView')
            ->assertDontSee('native web view');
    }

    #[Test]
    public function the_mobile_diagram_shows_native_views_with_the_web_view_as_optional()
    {
        $this->blade('<x-illustrations.mobile-stack />')
            ->assertSee('SwiftUI &amp; Compose', escape: false)
            ->assertSee('Real native views')
            ->assertSee('Web view (optional)')
            ->assertDontSee('HTML/CSS + JavaScript');
    }

    #[Test]
    public function the_desktop_diagram_shows_the_electron_and_chromium_stack()
    {
        $this->blade('<x-illustrations.desktop-stack />')
            ->assertSee('Electron')
            ->assertSee('Static PHP Runtime')
            ->assertSee('Chromium Window')
            ->assertSee('HTML/CSS + JavaScript')
            ->assertDontSee('SwiftUI');
    }

    #[Test]
    public function the_explainer_renders_both_platform_paths_for_the_toggle()
    {
        $this->blade('<x-home.explainer />')
            // Mobile path
            ->assertSee('Real native views')
            ->assertSee('Tiny apps')
            ->assertSee('nativephp/mobile')
            // Desktop path
            ->assertSee('Chromium Window')
            ->assertSee('statically-compiled PHP binary')
            ->assertSee('authenticated HTTP')
            ->assertSee('One file')
            ->assertSee('nativephp/desktop');
    }

    #[Test]
    public function the_mobile_tools_card_reads_as_written()
    {
        $this->blade('<x-home.explainer />')
            ->assertSee('Use (almost) any Composer package!')
            ->assertSeeInOrder([
                'Blade templates feel like HTML and render to real native',
                'views.',
                'No web view required',
            ]);
    }

    #[Test]
    public function the_explainer_blocks_are_bound_to_the_platform_store()
    {
        $this->blade('<x-home.explainer />')
            ->assertSee("\$store.platform.is('mobile')", escape: false)
            ->assertSee("\$store.platform.is('desktop')", escape: false);
    }

    /**
     * The mobile path renders native UI, so anything that renders HTML would
     * imply a web view. Those tools belong to the desktop path only.
     */
    #[Test]
    public function the_html_rendering_tools_are_scoped_to_the_desktop_path()
    {
        $scopes = $this->toolPillScopes();

        $webOnly = [
            'Livewire', 'FilamentPHP', 'TailwindCSS', 'Alpine.js', 'Inertia.js',
            'React', 'Vue.js', 'Nuxt', 'Next.js', 'TypeScript', 'JavaScript',
        ];

        foreach ($webOnly as $name) {
            $this->assertSame('desktop', $scopes[$name] ?? null, "{$name} should be desktop-only.");
        }
    }

    #[Test]
    public function the_platform_agnostic_php_tools_show_on_both_paths()
    {
        $scopes = $this->toolPillScopes();

        foreach (['Laravel', 'Pest', 'PHPUnit'] as $name) {
            $this->assertSame('all', $scopes[$name] ?? null, "{$name} should show on both paths.");
        }
    }

    /**
     * Several skill SVGs use document-wide ids (Pest's gradient is a bare
     * id="a"), so a pill rendered twice loses its fill in the second copy.
     */
    #[Test]
    public function each_tool_pill_is_rendered_exactly_once()
    {
        $names = array_map(
            fn (DOMElement $pill) => $this->pillName($pill),
            iterator_to_array($this->toolPills()),
        );

        $this->assertSame(
            array_unique($names),
            $names,
            'A tool pill is rendered more than once, which breaks SVGs with shared ids.',
        );
    }

    /**
     * @return array<string, string> pill name => the path it appears on
     */
    private function toolPillScopes(): array
    {
        $scopes = [];

        foreach ($this->toolPills() as $pill) {
            $scopes[$this->pillName($pill)] = $pill->getAttribute('data-tools');
        }

        return $scopes;
    }

    private function toolPills(): DOMNodeList
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$this->blade('<x-home.explainer />'));
        libxml_clear_errors();

        return (new DOMXPath($dom))->query('//a[@data-tools]');
    }

    private function pillName(DOMElement $pill): string
    {
        return trim(preg_replace('/\s+/', ' ', $pill->textContent));
    }

    #[Test]
    public function the_hero_offers_a_platform_chooser_defaulting_to_mobile()
    {
        $this->blade('<x-home.hero />')
            ->assertSee('I want to build a', escape: false)
            ->assertSee('role="tablist"', escape: false)
            ->assertSee("\$store.platform.select('mobile')", escape: false)
            ->assertSee("\$store.platform.select('desktop')", escape: false)
            ->assertSee('Mobile app')
            ->assertSee('Desktop app')
            // No-JS fallback points at the Mobile docs
            ->assertSee('href="/docs/mobile/getting-started/introduction"', escape: false)
            ->assertSee('Read the Mobile docs');
    }

    #[Test]
    public function the_hero_no_longer_promotes_the_demo_app_downloads()
    {
        $this->blade('<x-home.hero />')
            ->assertDontSee('Try our')
            ->assertDontSee('TestFlight')
            ->assertDontSee('Play Store')
            ->assertDontSee('testflight.apple.com', escape: false);
    }

    /**
     * The version is pinned deliberately — SuperNative is a v4 story, so the
     * link should keep working even after a future major becomes the latest.
     */
    #[Test]
    public function the_under_the_hood_section_links_to_the_supernative_intro()
    {
        $this->blade('<x-home.explainer />')
            ->assertSee('Learn more about SuperNative')
            ->assertSee('/docs/mobile/4/architecture/super-native', escape: false);
    }

    #[Test]
    public function the_supernative_page_the_homepage_links_to_exists()
    {
        // The page contains fenced code blocks. Torchlight throws outside
        // production when no token is configured (as in CI), so give it a
        // token and fake the API to force its offline fallback.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);

        $this->get('/docs/mobile/4/architecture/super-native')
            ->assertOk();
    }

    #[Test]
    public function the_mobile_path_names_supernative()
    {
        $this->blade('<x-home.explainer />')
            ->assertSeeInOrder([
                'No web view. No JSON bridge.',
                'We call this',
                'SuperNative.',
            ]);
    }

    #[Test]
    public function the_desktop_render_target_note_drops_the_gui_framework_line()
    {
        $this->blade('<x-home.explainer />')
            ->assertSee('Chromium window.')
            ->assertDontSee("isn't a GUI framework", escape: false);
    }

    #[Test]
    public function the_bifrost_diagram_no_longer_advertises_a_windows_track()
    {
        $this->blade('<x-illustrations.bifrost-diagram />')
            ->assertSee('Apple (macOS)')
            ->assertSee('Android')
            ->assertDontSee('Windows');
    }

    #[Test]
    public function the_homepage_renders_both_paths()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Real native views')
            ->assertSee('Jetpack Compose')
            ->assertSee('Chromium Window')
            ->assertDontSee('Native WebView');
    }
}
