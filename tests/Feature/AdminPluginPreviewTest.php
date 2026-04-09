<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class AdminPluginPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_guest_cannot_view_pending_plugin(): void
    {
        $plugin = Plugin::factory()->pending()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_regular_user_cannot_view_pending_plugin(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_admin_can_view_pending_plugin(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $plugin = Plugin::factory()->pending()->create();

        $this->actingAs($admin)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200);
    }

    public function test_admin_sees_preview_banner_on_pending_plugin(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $plugin = Plugin::factory()->pending()->create();

        $this->actingAs($admin)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertSee('Preview')
            ->assertSee('Pending Review');
    }

    public function test_approved_plugin_does_not_show_preview_banner(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertDontSee('This plugin is not publicly visible');
    }

    public function test_admin_can_view_approved_plugin_without_preview_banner(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $plugin = Plugin::factory()->approved()->create();

        $this->actingAs($admin)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('This plugin is not publicly visible');
    }
}
