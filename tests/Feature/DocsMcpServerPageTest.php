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
            ['search_docs', 'get_page', 'list_edge_components', 'get_edge_component', 'get_navigation', 'search_plugins', 'get_plugin'],
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

    #[Test]
    public function mobile_v4_getting_started_documents_the_public_mcp_endpoint(): void
    {
        $this->withoutVite()
            ->get('/docs/mobile/4/getting-started/mcp')
            ->assertOk()
            ->assertSee('MCP Docs Server')
            ->assertSee('https://nativephp.com/api/mcp/message');
    }

    #[Test]
    public function list_edge_components_returns_mobile_v4_components(): void
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'list_edge_components',
                'arguments' => ['platform' => 'mobile', 'version' => '4'],
            ],
        ]);

        $response->assertOk();

        $text = $response->json('result.content.0.text');

        $this->assertStringContainsString('mobile v4 EDGE components', $text);
        $this->assertStringContainsString('button', $text);
        $this->assertStringContainsString('text', $text);
        $this->assertStringContainsString('Path: mobile/4/edge-components/button', $text);
        $this->assertStringNotContainsString('Unknown tool', $text);
    }

    #[Test]
    public function list_edge_components_defaults_version_to_latest_mobile(): void
    {
        $latest = app(DocsSearchService::class)->getLatestVersions()['mobile'];

        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'list_edge_components',
                'arguments' => ['platform' => 'mobile'],
            ],
        ]);

        $response->assertOk();

        $text = $response->json('result.content.0.text');

        $this->assertStringContainsString("mobile v{$latest} EDGE components", $text);
        $this->assertStringContainsString("Path: mobile/{$latest}/edge-components/", $text);
    }

    #[Test]
    public function list_apis_is_no_longer_registered(): void
    {
        $list = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
        ]);

        $tools = collect($list->json('result.tools'))->pluck('name')->all();
        $this->assertNotContains('list_apis', $tools);

        $call = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/call',
            'params' => [
                'name' => 'list_apis',
                'arguments' => ['platform' => 'mobile', 'version' => '2'],
            ],
        ]);

        $this->assertTrue($call->json('result.isError'));
        $this->assertStringContainsString('Unknown tool: list_apis', $call->json('result.content.0.text'));
    }

    #[Test]
    public function the_edge_components_rest_endpoint_lists_mobile_v4_components(): void
    {
        $response = $this->getJson('/api/mcp/edge-components/mobile/4');

        $response->assertOk()
            ->assertJsonStructure(['edge_components' => [['title', 'slug', 'description', 'section', 'id']]]);

        $slugs = collect($response->json('edge_components'))->pluck('slug');

        $this->assertTrue($slugs->contains('button'));
        $this->assertTrue($slugs->contains('text'));
        $this->assertTrue(
            collect($response->json('edge_components'))->every(fn ($c) => $c['section'] === 'edge-components'
                || str_starts_with($c['section'], 'edge-components/')),
        );
    }

    #[Test]
    public function the_legacy_apis_rest_endpoint_is_gone(): void
    {
        $this->getJson('/api/mcp/apis/mobile/2')->assertNotFound();
    }

    #[Test]
    public function list_edge_components_names_the_tags_each_page_documents(): void
    {
        $text = $this->callTool('list_edge_components', ['platform' => 'mobile', 'version' => '4']);

        $this->assertStringContainsString('Tags: <native:list>, <native:list-item>, <native:list-section>', $text);
        $this->assertStringContainsString('<native:outlined-text-input>', $text);
        $this->assertStringContainsString('get_edge_component', $text);
    }

    #[Test]
    public function get_edge_component_returns_the_props_and_events_for_a_sub_element(): void
    {
        $text = $this->callTool('get_edge_component', ['tag' => 'list-item', 'version' => '4']);

        $this->assertStringContainsString('# <native:list-item>', $text);
        $this->assertStringContainsString('Documented at: mobile/4/edge-components/list (section "List Item")', $text);
        $this->assertStringContainsString('Native\\Mobile\\UI\\Elements\\ListItem', $text);
        $this->assertStringContainsString('`leadingCheckbox`', $text);
        $this->assertStringContainsString('`on-leading-change`', $text);
        $this->assertStringContainsString('`on-swipe-delete`', $text);
        $this->assertStringContainsString('`@press`', $text);
        $this->assertStringContainsString('onTrailingPress(string $method)', $text, 'The fluent API from the Element section belongs with the element.');
        $this->assertStringContainsString('nativephp/mobile-ui', $text);

        $this->assertStringNotContainsString('`on-refresh`', $text, 'Props of the parent <native:list> should not leak in.');
        $this->assertStringNotContainsString('```', $text, 'Examples are left to get_page.');
    }

    #[Test]
    public function get_edge_component_returns_the_whole_page_reference_for_a_page_level_element(): void
    {
        $text = $this->callTool('get_edge_component', ['tag' => 'button']);

        $this->assertStringContainsString('Documented at: mobile/4/edge-components/button', $text);
        $this->assertStringContainsString('## Props', $text);
        $this->assertStringContainsString('## Events', $text);
        $this->assertStringContainsString('- `@press` - Component method to call when tapped', $text);
        $this->assertStringContainsString('## Element', $text);
        $this->assertStringNotContainsString('## Examples', $text);
    }

    #[Test]
    public function get_edge_component_leaves_sub_element_sections_out_of_the_parent(): void
    {
        $text = $this->callTool('get_edge_component', ['tag' => 'list']);

        $this->assertStringContainsString('`on-end-reached`', $text);
        $this->assertStringNotContainsString('## List Item', $text);
        $this->assertStringNotContainsString('## List Section', $text);
        $this->assertStringContainsString('Also on this page: <native:list-item>, <native:list-section>', $text);
    }

    #[Test]
    public function get_edge_component_accepts_the_spellings_agents_use(): void
    {
        foreach (['<native:list-item>', 'native:list-item', 'list_item', 'ListItem', '<native:list-item />'] as $spelling) {
            $this->assertStringContainsString(
                '# <native:list-item>',
                $this->callTool('get_edge_component', ['tag' => $spelling]),
                "The tag should resolve when spelled {$spelling}.",
            );
        }

        $this->assertStringContainsString(
            'Documented at: mobile/4/edge-components/text-input',
            $this->callTool('get_edge_component', ['tag' => 'outlined-text-input']),
        );
    }

    #[Test]
    public function get_edge_component_lists_the_known_tags_when_it_cannot_find_one(): void
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => ['name' => 'get_edge_component', 'arguments' => ['tag' => 'text-input']],
        ]);

        $this->assertTrue($response->json('result.isError'));
        $this->assertStringContainsString('No EDGE component "text-input"', $response->json('result.content.0.text'));
        $this->assertStringContainsString('outlined-text-input', $response->json('result.content.0.text'));
    }

    /**
     * The catalogue is derived from the docs pages, so every tag it hands out
     * must resolve to a non-empty reference that get_page can also open.
     */
    #[Test]
    public function every_catalogued_edge_component_resolves_to_a_reference(): void
    {
        $service = app(DocsSearchService::class);
        $elements = $service->edgeElements('mobile', '4');

        $this->assertGreaterThan(40, count($elements));

        foreach (array_keys($elements) as $tag) {
            $element = $service->getEdgeElement('mobile', '4', $tag);

            $this->assertNotNull($element, "{$tag} is catalogued but does not resolve.");
            $this->assertNotSame('', trim($element['reference']), "{$tag} resolved to an empty reference.");
            $this->assertNotNull($service->getPageByPath($element['path']), "{$tag} points at a page get_page can't open.");
        }
    }

    #[Test]
    public function get_page_keeps_event_names_written_in_inline_code(): void
    {
        $text = $this->callTool('get_page', ['path' => 'mobile/4/edge-components/button']);

        $this->assertStringContainsString('- `@press` - Component method to call when tapped', $text);
        $this->assertStringNotContainsString('- `` -', $text);
    }

    #[Test]
    public function the_edge_components_rest_endpoints_expose_tags_and_references(): void
    {
        $list = $this->getJson('/api/mcp/edge-components/mobile/4')->assertOk();

        $listPage = collect($list->json('edge_components'))->firstWhere('slug', 'list');
        $this->assertSame(['list', 'list-item', 'list-section'], $listPage['tags']);

        $this->getJson('/api/mcp/edge-components/mobile/4/list-item')
            ->assertOk()
            ->assertJsonPath('edge_component.tag', 'list-item')
            ->assertJsonPath('edge_component.path', 'mobile/4/edge-components/list')
            ->assertJsonPath('edge_component.section', 'List Item');

        $this->getJson('/api/mcp/edge-components/mobile/4/not-a-component')->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function callTool(string $name, array $arguments): string
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => ['name' => $name, 'arguments' => $arguments],
        ]);

        $response->assertOk();

        return (string) $response->json('result.content.0.text');
    }
}
