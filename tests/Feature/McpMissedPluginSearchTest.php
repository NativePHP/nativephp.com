<?php

namespace Tests\Feature;

use App\Enums\PluginType;
use App\Jobs\ClassifyMissedPluginSearch;
use App\Models\MissedPluginSearch;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Tests\TestCase;

class McpMissedPluginSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function test_a_search_that_finds_nothing_is_recorded_and_queued_for_classification(): void
    {
        $this->searchPlugins(['query' => 'bluetooth', 'use_case' => 'Read heart rate from a BLE chest strap'])
            ->assertOk()
            ->assertJsonPath('result.content.0.text', 'No marketplace plugins found for "bluetooth"');

        $search = MissedPluginSearch::sole();

        $this->assertSame('bluetooth', $search->term);
        $this->assertSame('Read heart rate from a BLE chest strap', $search->use_case);
        $this->assertNull($search->type);
        $this->assertNull($search->plugin_idea_id);

        Queue::assertPushed(
            ClassifyMissedPluginSearch::class,
            fn (ClassifyMissedPluginSearch $job): bool => $job->search->is($search),
        );
    }

    public function test_a_search_that_finds_a_plugin_is_not_recorded(): void
    {
        Plugin::factory()->approved()->free()->create([
            'name' => 'acme/bluetooth',
            'description' => 'Bluetooth Low Energy for mobile apps.',
        ]);

        $this->searchPlugins(['query' => 'bluetooth'])->assertOk();

        $this->assertDatabaseEmpty('missed_plugin_searches');
        Queue::assertNothingPushed();
    }

    public function test_a_search_narrowed_to_one_type_is_not_recorded_when_a_plugin_of_the_other_type_covers_it(): void
    {
        Plugin::factory()->approved()->free()->create([
            'name' => 'acme/camera',
            'description' => 'Camera helpers for mobile apps.',
        ]);

        $this->searchPlugins(['query' => 'camera', 'type' => 'paid'])
            ->assertJsonPath('result.content.0.text', 'No marketplace plugins found for "camera" (type: paid)');

        $this->assertDatabaseEmpty('missed_plugin_searches');
    }

    public function test_a_search_narrowed_to_one_type_is_recorded_with_that_type_when_nothing_covers_it(): void
    {
        $this->searchPlugins(['query' => 'nfc', 'type' => 'paid']);

        $this->assertSame(PluginType::Paid, MissedPluginSearch::sole()->type);
    }

    public function test_blank_searches_are_not_recorded(): void
    {
        $this->searchPlugins(['query' => '   ']);

        $this->assertDatabaseEmpty('missed_plugin_searches');
    }

    public function test_search_terms_are_tidied_before_they_are_stored(): void
    {
        $this->searchPlugins(['query' => "  apple \n  pay "]);

        $this->assertSame('apple pay', MissedPluginSearch::sole()->term);
    }

    public function test_oversized_input_is_cut_down_before_it_is_stored(): void
    {
        $this->searchPlugins([
            'query' => str_repeat('a', 600),
            'use_case' => str_repeat('b', 1200),
        ]);

        $search = MissedPluginSearch::sole();

        $this->assertSame(500, mb_strlen($search->term));
        $this->assertSame(1000, mb_strlen($search->use_case));
    }

    public function test_a_use_case_that_is_not_text_is_ignored(): void
    {
        $this->searchPlugins(['query' => 'nfc', 'use_case' => ['scan', 'tags']]);

        $this->assertNull(MissedPluginSearch::sole()->use_case);
    }

    public function test_the_search_still_answers_when_recording_it_fails(): void
    {
        Exceptions::fake();

        MissedPluginSearch::creating(function (): void {
            throw new RuntimeException('The database is down');
        });

        $this->searchPlugins(['query' => 'bluetooth'])
            ->assertOk()
            ->assertJsonPath('result.content.0.text', 'No marketplace plugins found for "bluetooth"');

        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_rest_api_searches_are_not_recorded(): void
    {
        $this->getJson('/api/mcp/plugins?q=bluetooth')
            ->assertOk()
            ->assertJsonPath('plugins', []);

        $this->assertDatabaseEmpty('missed_plugin_searches');
    }

    public function test_the_tool_offers_an_optional_use_case_argument(): void
    {
        $tool = collect($this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
        ])->json('result.tools'))->firstWhere('name', 'search_plugins');

        $this->assertSame('string', $tool['inputSchema']['properties']['use_case']['type']);
        $this->assertSame(['query'], $tool['inputSchema']['required']);
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    private function searchPlugins(array $arguments): TestResponse
    {
        return $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'search_plugins',
                'arguments' => $arguments,
            ],
        ]);
    }
}
