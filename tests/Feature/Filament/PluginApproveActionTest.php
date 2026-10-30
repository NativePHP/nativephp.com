<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginStatus;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Models\Plugin;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PluginApproveActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Queue::fake();
        config(['filament.users' => ['env-admin@test.com']]);
        Filament::setCurrentPanel('admin');
    }

    public function test_super_admin_role_can_approve_a_pending_plugin_that_passes_review(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Role::findOrCreate(config('filament-shield.super_admin.name')));

        $this->assertApproves($superAdmin, $this->reviewablePlugin());
    }

    public function test_filament_users_admin_can_approve_a_pending_plugin_that_passes_review(): void
    {
        $this->assertApproves(User::factory()->create(['email' => 'env-admin@test.com']), $this->reviewablePlugin());
    }

    public function test_approve_stays_clickable_and_explains_failing_checks(): void
    {
        $superAdmin = User::factory()->create(['email' => 'env-admin@test.com']);
        $plugin = $this->reviewablePlugin();
        $plugin->update(['demo_video_url' => null, 'demo_video_attested_at' => null]);

        Livewire::actingAs($superAdmin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('approve')
            ->assertActionEnabled('approve')
            ->mountAction('approve')
            ->assertMountedActionModalSee('required checks are failing')
            ->assertMountedActionModalSee('Demo video showing the plugin working')
            ->callMountedAction();

        $this->assertSame(PluginStatus::Pending, $plugin->fresh()->status);
    }

    private function assertApproves(User $user, Plugin $plugin): void
    {
        Livewire::actingAs($user)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('approve')
            ->assertActionEnabled('approve')
            ->callAction('approve')
            ->assertHasNoActionErrors();

        $plugin->refresh();

        $this->assertSame(PluginStatus::Approved, $plugin->status);
        $this->assertSame($user->id, $plugin->approved_by);
    }

    private function reviewablePlugin(): Plugin
    {
        return Plugin::factory()->pending()->free()->create([
            'webhook_installed' => true,
            'review_checks' => ['has_license_file' => true, 'has_release_version' => true, 'release_version' => '1.0.0'],
        ]);
    }
}
