<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginShowMobileVersionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_plugin_show_displays_mobile_min_version(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'mobile_min_version' => '^3.0.0',
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('NativePHP Mobile')
            ->assertSee('^3.0.0');
    }

    public function test_plugin_show_displays_dash_when_mobile_min_version_is_null(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'mobile_min_version' => null,
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('NativePHP Mobile');
    }
}
