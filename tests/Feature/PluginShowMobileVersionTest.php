<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
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

    public function test_plugin_header_shows_a_pill_for_each_supported_mobile_version(): void
    {
        $plugin = Plugin::factory()->approved()->mobileVersions('3.0', '4.5.2')->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Works with NativePHP Mobile 3.x from 3.0',
                'Works with NativePHP Mobile 4.x from 4.5.2',
            ]);
    }

    public function test_plugin_header_marks_mobile_versions_with_a_phone_icon(): void
    {
        $plugin = Plugin::factory()->approved()->mobileVersions('4.0')->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee(Blade::render('<x-icons.device-mobile-phone class="h-3.5 shrink-0" aria-hidden="true" />'), false);
    }

    public function test_plugin_header_has_no_mobile_version_pills_without_versions(): void
    {
        $plugin = Plugin::factory()->approved()->create(['mobile_versions' => null]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('Works with NativePHP Mobile');
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
