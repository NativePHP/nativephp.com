<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginShowSupportChannelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_long_support_url_is_truncated_in_display_text(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'support_channel' => 'https://github.com/SRWieZ/nativephp-mobile-packages',
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertSee('github.com/SRWieZ/nativep...', false);
    }

    public function test_support_url_display_strips_protocol(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'support_channel' => 'https://example.com/support',
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertSee('example.com/support', false);
    }

    public function test_email_support_channel_is_not_truncated(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'support_channel' => 'support@example.com',
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertSee('support@example.com', false);
    }
}
