<?php

namespace Tests\Feature\Docs;

use App\Features\ShowPlugins;
use App\Support\JumpApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class JumpPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_edge_component_page_shows_the_jump_qr_and_store_overlay(): void
    {
        $response = $this->get('/docs/mobile/4/edge-components/stack');

        $response->assertStatus(200)
            ->assertSee('Preview in Jump')
            ->assertSee('Get the Jump app')
            ->assertSee(JumpApp::IOS_APP_STORE_URL, false)
            ->assertSee(JumpApp::ANDROID_PLAY_STORE_URL, false);
    }

    public function test_qr_deep_link_targets_this_page_with_the_marker_param(): void
    {
        $this->get('/docs/mobile/4/edge-components/badge')
            ->assertStatus(200)
            ->assertSee('edge-components/badge?jump=qr', false);
    }

    public function test_qr_deep_link_is_pinned_to_the_canonical_domain(): void
    {
        // Jump only claims deep links for nativephp.com, so the QR must encode
        // that domain regardless of the host the docs are being served from.
        $this->get('/docs/mobile/4/edge-components/badge')
            ->assertStatus(200)
            ->assertSee('https://nativephp.com/docs/mobile/4/edge-components/badge?jump=qr', false);
    }

    public function test_qr_card_renders_in_the_desktop_only_right_sidebar(): void
    {
        $html = $this->get('/docs/mobile/4/edge-components/stack')
            ->assertStatus(200)
            ->getContent();

        // The right sidebar is the desktop-only region and follows </article>.
        $this->assertGreaterThan(
            strrpos($html, '</article>'),
            strpos($html, 'Preview in Jump'),
            'The QR card should render inside the right sidebar.'
        );
    }

    public function test_qr_card_sits_between_the_copy_button_and_the_ads(): void
    {
        $html = $this->get('/docs/mobile/4/edge-components/stack')
            ->assertStatus(200)
            ->getContent();

        $copyButton = strrpos($html, 'Copy as Markdown');
        $qrCard = strpos($html, 'Preview in Jump');
        $ads = strpos($html, 'Become a Partner', $qrCard);

        $this->assertGreaterThan($copyButton, $qrCard, 'The QR card should follow the Copy as Markdown button.');
        $this->assertGreaterThan($qrCard, $ads, 'The QR card should come before the ads.');
    }

    public function test_qr_card_is_collapsed_to_its_title_by_default(): void
    {
        $html = $this->get('/docs/mobile/4/edge-components/stack')
            ->assertStatus(200)
            ->getContent();

        // The card occupies the sidebar slot between the copy button and the ads.
        $start = strrpos($html, 'Copy as Markdown');
        $end = strpos($html, 'Become a Partner', $start);
        $cardMarkup = substr($html, $start, $end - $start);

        $this->assertStringContainsString('Preview in Jump', $cardMarkup);
        $this->assertStringContainsString('x-data="{ open: false }"', $cardMarkup);
        $this->assertStringContainsString('open = !open', $cardMarkup);
        $this->assertStringContainsString('x-collapse', $cardMarkup);
    }

    public function test_store_overlay_stays_outside_the_desktop_only_sidebar(): void
    {
        $html = $this->get('/docs/mobile/4/edge-components/stack')
            ->assertStatus(200)
            ->getContent();

        // Phones render the overlay, so it must not be hidden with the sidebar.
        $this->assertLessThan(
            strrpos($html, '</article>'),
            strpos($html, 'Get the Jump app'),
            'The overlay should render in the always-visible article body.'
        );
    }

    public function test_non_edge_docs_page_has_no_jump_preview(): void
    {
        $this->get('/docs/mobile/4/getting-started/introduction')
            ->assertStatus(200)
            ->assertDontSee('Preview in Jump')
            ->assertDontSee('Get the Jump app')
            ->assertDontSee('jump=qr', false);
    }

    /**
     * EDGE sections also exist in Mobile v2 and v3, but Jump only previews v4.
     *
     * @return array<string, array{string}>
     */
    public static function nonV4EdgePagesProvider(): array
    {
        return [
            'mobile v2 edge components' => ['/docs/mobile/2/edge-components/bottom-nav'],
            'mobile v3 edge components' => ['/docs/mobile/3/edge-components/bottom-nav'],
        ];
    }

    #[DataProvider('nonV4EdgePagesProvider')]
    public function test_older_mobile_edge_pages_have_no_jump_preview(string $url): void
    {
        $this->get($url)
            ->assertStatus(200)
            ->assertDontSee('Preview in Jump')
            ->assertDontSee('Get the Jump app')
            ->assertDontSee('jump=qr', false);
    }

    public function test_desktop_docs_have_no_jump_preview(): void
    {
        $this->get('/docs/desktop/2/the-basics/windows')
            ->assertStatus(200)
            ->assertDontSee('Preview in Jump')
            ->assertDontSee('Get the Jump app')
            ->assertDontSee('jump=qr', false);
    }
}
