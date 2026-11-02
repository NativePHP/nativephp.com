<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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

    public function test_staff_with_plugin_view_permission_can_preview_pending_plugin(): void
    {
        $plugin = Plugin::factory()->pending()->create();

        $this->actingAs($this->staffWithPermissions(['ViewAny:Plugin', 'View:Plugin']))
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk()
            ->assertSee('This plugin is not publicly visible.');
    }

    public function test_staff_with_plugin_view_permission_can_preview_paid_pending_plugin_and_its_license(): void
    {
        $plugin = Plugin::factory()->pending()->paid()->create(['license_html' => '<p>License terms</p>']);
        $staff = $this->staffWithPermissions(['ViewAny:Plugin', 'View:Plugin']);

        $this->actingAs($staff)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk();

        $this->actingAs($staff)
            ->get(route('plugins.license', $plugin->routeParams()))
            ->assertOk()
            ->assertSee('License terms', false);
    }

    public function test_staff_without_plugin_view_permission_cannot_preview_pending_plugin(): void
    {
        $plugin = Plugin::factory()->pending()->create();

        $this->actingAs($this->staffWithPermissions(['ViewAny:Lead']))
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertNotFound();
    }

    public function test_super_admin_role_can_preview_pending_plugin(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Role::findOrCreate(config('filament-shield.super_admin.name')));

        $plugin = Plugin::factory()->pending()->create();

        $this->actingAs($superAdmin)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk();
    }

    /**
     * @param  list<string>  $permissions
     */
    private function staffWithPermissions(array $permissions): User
    {
        $role = Role::findOrCreate('test_role');

        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::findOrCreate($permission));
        }

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
