<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\User;
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

    public function test_owner_can_preview_draft_plugin_listing(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->draft()->for($user)->create();

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Preview');
    }

    public function test_non_owner_cannot_view_draft_plugin_listing(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $plugin = Plugin::factory()->draft()->for($owner)->create();

        $this->actingAs($otherUser)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_guest_cannot_view_draft_plugin_listing(): void
    {
        $plugin = Plugin::factory()->draft()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_delisted_plugin_is_not_visible_to_public(): void
    {
        $plugin = Plugin::factory()->approved()->create(['is_active' => false]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_owner_can_preview_delisted_plugin(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->approved()->for($user)->create(['is_active' => false]);

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('de-listed');
    }
}
