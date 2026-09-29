<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginLicensePreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    private function createPaidPluginWithLicense(array $pluginAttributes = []): Plugin
    {
        $plugin = Plugin::factory()
            ->paid()
            ->create(array_merge([
                'license_html' => '<p>License agreement content</p>',
            ], $pluginAttributes));

        PluginPrice::factory()->regular()->create([
            'plugin_id' => $plugin->id,
            'amount' => 2999,
        ]);

        return $plugin;
    }

    public function test_guest_cannot_view_pending_paid_plugin_license(): void
    {
        $plugin = $this->createPaidPluginWithLicense();

        $this->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_regular_user_cannot_view_pending_paid_plugin_license(): void
    {
        $user = User::factory()->create();
        $plugin = $this->createPaidPluginWithLicense();

        $this->actingAs($user)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_admin_can_view_pending_paid_plugin_license(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $plugin = $this->createPaidPluginWithLicense();

        $this->actingAs($admin)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(200);
    }

    public function test_owner_can_view_pending_paid_plugin_license(): void
    {
        $owner = User::factory()->create();
        $plugin = $this->createPaidPluginWithLicense(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(200);
    }

    public function test_admin_sees_preview_banner_on_pending_paid_plugin_license(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $plugin = $this->createPaidPluginWithLicense();

        $this->actingAs($admin)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertSee('Preview')
            ->assertSee('This plugin is not publicly visible')
            ->assertSee('Pending Review');
    }

    public function test_owner_sees_preview_banner_on_pending_paid_plugin_license(): void
    {
        $owner = User::factory()->create();
        $plugin = $this->createPaidPluginWithLicense(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertSee('Preview')
            ->assertSee('This plugin is not publicly visible')
            ->assertSee('Pending Review');
    }

    public function test_approved_paid_plugin_license_does_not_show_preview_banner(): void
    {
        $plugin = $this->createPaidPluginWithLicense(['status' => 'approved', 'approved_at' => now()]);

        $this->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('This plugin is not publicly visible');
    }

    public function test_admin_sees_preview_banner_on_delisted_paid_plugin_license(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $plugin = $this->createPaidPluginWithLicense([
            'status' => 'approved',
            'approved_at' => now(),
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertSee('Preview')
            ->assertSee('It has been de-listed');
    }

    public function test_guest_cannot_view_delisted_paid_plugin_license(): void
    {
        $plugin = $this->createPaidPluginWithLicense([
            'status' => 'approved',
            'approved_at' => now(),
            'is_active' => false,
        ]);

        $this->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_free_plugin_license_returns_404(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create([
            'license_html' => '<p>License content</p>',
        ]);

        $this->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(404);
    }

    public function test_paid_plugin_without_license_html_returns_404(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $plugin = Plugin::factory()->pending()->paid()->create([
            'license_html' => null,
        ]);

        PluginPrice::factory()->regular()->create([
            'plugin_id' => $plugin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(404);
    }
}
