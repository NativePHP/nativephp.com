<?php

namespace Tests\Feature;

use App\Services\DocsSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsMcpServerPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The page renders fenced code blocks. Torchlight throws outside
        // production when no token is configured (as in CI), so give it a token
        // and fake the API to force its offline fallback.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);
    }

    #[Test]
    public function it_documents_the_endpoint_clients_should_connect_to(): void
    {
        $this->withoutVite()
            ->get(route('mcp'))
            ->assertOk()
            ->assertSee('Docs MCP Server')
            ->assertSee('https://nativephp.com/api/mcp/message');
    }

    #[Test]
    public function it_shows_the_config_snippets_without_evaluating_them_as_blade(): void
    {
        $this->withoutVite()
            ->get(route('mcp'))
            ->assertOk()
            ->assertSee('mcpServers')
            ->assertSee('"servers"')
            ->assertSee('mcp-remote');
    }

    #[Test]
    public function it_is_reachable_from_the_footer_on_every_page(): void
    {
        $content = $this->withoutVite()
            ->get(route('welcome'))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<a\s+href="'.preg_quote(route('mcp'), '/').'"[^>]*>\s*MCP\s*<\/a>/',
            $content,
            'The footer should link to the MCP page, labelled "MCP".',
        );
    }

    #[Test]
    public function the_documented_message_endpoint_lists_the_documented_tools(): void
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
        ]);

        $response->assertOk();

        $tools = collect($response->json('result.tools'))->pluck('name')->all();

        $this->assertEqualsCanonicalizing(
            ['search_docs', 'get_page', 'list_apis', 'get_navigation'],
            $tools,
        );
    }

    /**
     * The documented workflow is search, then fetch. A path that search hands
     * back has to be one get_page can actually resolve, including for pages
     * nested in a subsection.
     */
    #[Test]
    public function every_search_result_path_can_be_fetched_by_get_page(): void
    {
        $paths = collect(app(DocsSearchService::class)->search('camera', 'mobile', '4', 10))
            ->pluck('id');

        $this->assertTrue($paths->contains('mobile/4/plugins/core/camera'));

        foreach ($paths as $path) {
            $response = $this->postJson('/api/mcp/message', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'tools/call',
                'params' => ['name' => 'get_page', 'arguments' => ['path' => $path]],
            ]);

            $this->assertStringNotContainsString(
                'Page not found',
                $response->json('result.content.0.text'),
                "search_docs returned {$path}, which get_page could not resolve.",
            );
        }
    }

    #[Test]
    public function the_sse_route_is_gone_so_clients_cannot_hang_on_it(): void
    {
        $this->getJson('/api/mcp/sse')->assertNotFound();

        $this->assertNull(
            app('router')->getRoutes()->getByName('mcp.sse'),
            'The SSE route never sent the endpoint event its transport requires.',
        );
    }

    #[Test]
    public function the_documented_raw_markdown_url_serves_a_page(): void
    {
        $this->get('/docs/mobile/4/plugins/core/camera.md')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=utf-8');
    }

    #[Test]
    public function the_docs_no_longer_carry_a_duplicate_copy_of_this_page(): void
    {
        $this->assertSame(
            [],
            glob(resource_path('views/docs/*/*/**/mcp-server.md')) ?: [],
            'The MCP server is documented once, at '.route('mcp').'.',
        );
    }

    /**
     * Jump and MCP clients read page content with Blade components stripped.
     * A tag that survives shows up as a stray paragraph of attributes.
     */
    #[Test]
    public function a_multi_line_self_closing_component_is_stripped_whole(): void
    {
        $markdown = <<<'MD'
            Before.

            <x-docs.edge-preview
                ios="edge-slider-ios.png"
                android="edge-slider-android.png"
                source="resources/views/native/explore/forms.blade.php"
                alt="Slider"
                edge="both"
            />

            After.
            MD;

        $this->assertSame("Before.\n\n\n\nAfter.", $this->stripBladeComponents($markdown));
    }

    #[Test]
    public function a_self_closing_component_with_bound_attributes_is_stripped(): void
    {
        $markdown = "Before.\n\n<x-docs.edge-preview ios=\"a.png\" :sidebar-width-ios=\"85\" :show=\"\$count > 0\" />\n\nAfter.";

        $this->assertSame("Before.\n\n\n\nAfter.", $this->stripBladeComponents($markdown));
    }

    #[Test]
    public function a_self_closing_component_does_not_swallow_prose_up_to_a_later_closing_tag(): void
    {
        $markdown = <<<'MD'
            Intro.

            <x-docs.version-badge since="4.2" />

            This paragraph must survive.

            <x-foo title="Bar">Hidden body</x-foo>

            Outro.
            MD;

        $content = $this->stripBladeComponents($markdown);

        $this->assertStringContainsString('Intro.', $content);
        $this->assertStringContainsString('This paragraph must survive.', $content);
        $this->assertStringContainsString('Outro.', $content);
        $this->assertStringNotContainsString('<x-', $content);
        $this->assertStringNotContainsString('Hidden body', $content);
        $this->assertStringNotContainsString('</x-foo>', $content);
    }

    #[Test]
    public function fenced_blade_examples_are_left_byte_for_byte(): void
    {
        $code = <<<'MD'
            ```blade
            <native:column class="gap-4">
                <native:text>{{ $count }}</native:text>
                <native:button label="Save" @press="save" />
                <x-docs.edge-preview ios="a.png" source="resources/views/a.blade.php" />
            </native:column>
            ```
            MD;

        $content = $this->stripBladeComponents("<x-docs.version-badge since=\"4.2\" />\n\n{$code}\n\nAfter.");

        $this->assertStringContainsString($code, $content);
        $this->assertStringStartsWith("\n\n```blade", $content);
    }

    #[Test]
    public function edge_component_pages_serve_no_screenshot_markup(): void
    {
        $pages = collect($this->getJson('/api/mcp/navigation/mobile/4')->assertOk()->json('navigation.edge-components'));

        $this->assertNotEmpty($pages);

        foreach ($pages as $page) {
            $prose = preg_replace('/```[\s\S]*?```/', '', $page['content']);

            $this->assertStringNotContainsString('edge-preview', $prose, "{$page['id']} leaks its screenshot tag.");
            $this->assertStringNotContainsString('source="resources/', $prose, "{$page['id']} leaks its screenshot tag.");
        }
    }

    private function stripBladeComponents(string $markdown): string
    {
        $service = app(DocsSearchService::class);

        return (new \ReflectionMethod($service, 'stripBladeComponents'))->invoke($service, $markdown);
    }
}
