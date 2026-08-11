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
}
