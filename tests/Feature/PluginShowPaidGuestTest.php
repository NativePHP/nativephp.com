<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginShowPaidGuestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_paid_plugin_show_page_renders_for_guest(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create();

        PluginPrice::factory()->regular()->create([
            'plugin_id' => $plugin->id,
            'amount' => 2999,
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Installing this plugin')
            ->assertSee('Log in');
    }
}
