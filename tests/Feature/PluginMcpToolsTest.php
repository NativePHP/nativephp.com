<?php

namespace Tests\Feature;

use App\Models\Plugin;
use App\Models\PluginPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PluginMcpToolsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tools_list_includes_plugin_discovery_tools(): void
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
        ]);

        $response->assertOk();

        $tools = collect($response->json('result.tools'))->pluck('name')->all();

        $this->assertContains('search_plugins', $tools);
        $this->assertContains('get_plugin', $tools);
    }

    #[Test]
    public function search_plugins_finds_approved_plugin_by_name_fragment(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create([
            'name' => 'acme/unique-camera-plugin',
            'description' => 'Native camera helpers for mobile apps.',
        ]);

        Plugin::factory()->draft()->create([
            'name' => 'acme/draft-camera-plugin',
            'description' => 'Draft camera plugin should stay hidden.',
        ]);

        Plugin::factory()->pending()->create([
            'name' => 'acme/pending-camera-plugin',
            'description' => 'Pending camera plugin should stay hidden.',
        ]);

        Plugin::factory()->approved()->inactive()->create([
            'name' => 'acme/inactive-camera-plugin',
            'description' => 'Inactive camera plugin should stay hidden.',
        ]);

        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'search_plugins',
                'arguments' => ['query' => 'unique-camera'],
            ],
        ]);

        $response->assertOk();

        $text = $response->json('result.content.0.text');

        $this->assertStringContainsString('acme/unique-camera-plugin', $text);
        $this->assertStringContainsString('Marketplace:', $text);
        $this->assertStringContainsString(route('plugins.show', $plugin->routeParams()), $text);
        $this->assertStringNotContainsString('draft-camera-plugin', $text);
        $this->assertStringNotContainsString('pending-camera-plugin', $text);
        $this->assertStringNotContainsString('inactive-camera-plugin', $text);
        $this->assertStringNotContainsString('You already have access', $text);
    }

    #[Test]
    public function search_plugins_filters_by_type(): void
    {
        Plugin::factory()->approved()->free()->create([
            'name' => 'acme/free-biometrics',
            'description' => 'Free biometrics plugin.',
        ]);

        $paid = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/paid-biometrics',
            'description' => 'Paid biometrics plugin.',
        ]);

        PluginPrice::factory()->regular()->amount(4900)->create([
            'plugin_id' => $paid->id,
        ]);

        $freeResponse = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'search_plugins',
                'arguments' => ['query' => 'biometrics', 'type' => 'free'],
            ],
        ]);

        $freeText = $freeResponse->json('result.content.0.text');
        $this->assertStringContainsString('acme/free-biometrics', $freeText);
        $this->assertStringNotContainsString('acme/paid-biometrics', $freeText);

        $paidResponse = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/call',
            'params' => [
                'name' => 'search_plugins',
                'arguments' => ['query' => 'biometrics', 'type' => 'paid'],
            ],
        ]);

        $paidText = $paidResponse->json('result.content.0.text');
        $this->assertStringContainsString('acme/paid-biometrics', $paidText);
        $this->assertStringContainsString('Marketplace:', $paidText);
        $this->assertStringContainsString(route('plugins.show', $paid->routeParams()), $paidText);
        $this->assertStringContainsString('$49.00', $paidText);
        $this->assertStringNotContainsString('acme/free-biometrics', $paidText);
    }

    #[Test]
    public function search_plugins_always_includes_marketplace_url_for_paid_plugins_even_without_price(): void
    {
        $paid = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/paid-no-price',
            'description' => 'Paid plugin without a listed regular price.',
        ]);

        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'search_plugins',
                'arguments' => ['query' => 'paid-no-price'],
            ],
        ]);

        $response->assertOk();

        $text = $response->json('result.content.0.text');
        $marketplaceUrl = route('plugins.show', $paid->routeParams());

        $this->assertStringContainsString('acme/paid-no-price', $text);
        $this->assertStringContainsString('paid', $text);
        $this->assertStringContainsString('Marketplace:', $text);
        $this->assertStringContainsString($marketplaceUrl, $text);
    }

    #[Test]
    public function get_plugin_returns_detail_for_approved_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create([
            'name' => 'acme/detail-plugin',
            'description' => 'A detailed free plugin.',
            'repository_url' => 'https://github.com/acme/detail-plugin',
            'latest_version' => '1.2.3',
            'works_in_jump' => true,
        ]);

        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'get_plugin',
                'arguments' => ['name' => 'acme/detail-plugin'],
            ],
        ]);

        $response->assertOk();
        $this->assertArrayNotHasKey('isError', $response->json('result'));

        $text = $response->json('result.content.0.text');
        $this->assertStringContainsString('acme/detail-plugin', $text);
        $this->assertStringContainsString('A detailed free plugin.', $text);
        $this->assertStringContainsString('1.2.3', $text);
        $this->assertStringContainsString('https://github.com/acme/detail-plugin', $text);
        $this->assertStringContainsString('https://packagist.org/packages/acme/detail-plugin', $text);
        $this->assertStringContainsString('Marketplace:', $text);
        $this->assertStringContainsString(route('plugins.show', $plugin->routeParams()), $text);
        $this->assertStringContainsString('works in Jump', $text);
    }

    #[Test]
    public function get_plugin_returns_marketplace_url_for_paid_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/paid-detail',
            'description' => 'Paid detail plugin.',
        ]);

        PluginPrice::factory()->regular()->amount(2999)->create([
            'plugin_id' => $plugin->id,
        ]);

        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'get_plugin',
                'arguments' => [
                    'vendor' => 'acme',
                    'package' => 'paid-detail',
                ],
            ],
        ]);

        $response->assertOk();

        $text = $response->json('result.content.0.text');
        $marketplaceUrl = route('plugins.show', $plugin->routeParams());

        $this->assertStringContainsString('$29.99', $text);
        $this->assertStringContainsString('Marketplace:', $text);
        $this->assertStringContainsString($marketplaceUrl, $text);
        $this->assertStringNotContainsString('Packagist:', $text);
        $this->assertArrayNotHasKey(
            'has_access',
            $this->getJson('/api/mcp/plugins/acme/paid-detail')->json('plugin')
        );
    }

    #[Test]
    public function get_plugin_errors_for_unknown_or_unapproved_plugins(): void
    {
        Plugin::factory()->pending()->create([
            'name' => 'acme/pending-only',
        ]);

        $unknown = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'get_plugin',
                'arguments' => ['name' => 'acme/does-not-exist'],
            ],
        ]);

        $this->assertTrue($unknown->json('result.isError'));
        $this->assertStringContainsString('Plugin not found: acme/does-not-exist', $unknown->json('result.content.0.text'));

        $pending = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/call',
            'params' => [
                'name' => 'get_plugin',
                'arguments' => ['name' => 'acme/pending-only'],
            ],
        ]);

        $this->assertTrue($pending->json('result.isError'));
        $this->assertStringContainsString('Plugin not found: acme/pending-only', $pending->json('result.content.0.text'));
    }

    #[Test]
    public function public_results_include_marketplace_fields_without_access_claims(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/anon-plugin',
            'description' => 'Anonymous paid plugin.',
        ]);

        PluginPrice::factory()->regular()->amount(1500)->create([
            'plugin_id' => $plugin->id,
        ]);

        $response = $this->getJson('/api/mcp/plugins?q=anon-plugin')
            ->assertOk()
            ->assertJsonPath('plugins.0.name', 'acme/anon-plugin')
            ->assertJsonPath('plugins.0.price', '$15.00')
            ->assertJsonPath('plugins.0.marketplace_url', route('plugins.show', $plugin->routeParams()));

        $this->assertArrayNotHasKey('has_access', $response->json('plugins.0'));
        $this->assertArrayNotHasKey('your_price', $response->json('plugins.0'));
        $this->assertArrayNotHasKey('access_label', $response->json('plugins.0'));
    }

    #[Test]
    public function rest_plugins_search_returns_expected_json_shape(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/rest-paid',
            'description' => 'REST paid plugin.',
            'featured' => true,
            'latest_version' => '2.0.0',
        ]);

        PluginPrice::factory()->regular()->amount(9900)->create([
            'plugin_id' => $plugin->id,
        ]);

        $response = $this->getJson('/api/mcp/plugins?q=rest-paid&type=paid&limit=5');

        $response->assertOk()
            ->assertJsonPath('plugins.0.name', 'acme/rest-paid')
            ->assertJsonPath('plugins.0.type', 'paid')
            ->assertJsonPath('plugins.0.price', '$99.00')
            ->assertJsonPath('plugins.0.featured', true)
            ->assertJsonPath('plugins.0.latest_version', '2.0.0')
            ->assertJsonPath('plugins.0.marketplace_url', route('plugins.show', $plugin->routeParams()));

        $this->assertNotEmpty($response->json('plugins.0.marketplace_url'));
        $this->assertArrayNotHasKey('has_access', $response->json('plugins.0'));
        $this->assertArrayNotHasKey('your_price', $response->json('plugins.0'));
    }

    #[Test]
    public function rest_plugin_show_returns_detail_and_404_for_missing(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create([
            'name' => 'acme/rest-free',
            'description' => 'REST free plugin.',
            'repository_url' => 'https://github.com/acme/rest-free',
        ]);

        $this->getJson('/api/mcp/plugins/acme/rest-free')
            ->assertOk()
            ->assertJsonPath('plugin.name', 'acme/rest-free')
            ->assertJsonPath('plugin.type', 'free')
            ->assertJsonPath('plugin.packagist_url', 'https://packagist.org/packages/acme/rest-free')
            ->assertJsonPath('plugin.repository_url', 'https://github.com/acme/rest-free')
            ->assertJsonPath('plugin.marketplace_url', route('plugins.show', $plugin->routeParams()));

        $this->getJson('/api/mcp/plugins/acme/missing')
            ->assertNotFound()
            ->assertJsonPath('error', 'Plugin not found');
    }

}
