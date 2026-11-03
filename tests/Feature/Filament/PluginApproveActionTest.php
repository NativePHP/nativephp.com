<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Models\Plugin;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
        $plugin = $this->pluginWithoutDemoVideo();
        $this->recordSubmission($plugin, $this->afterRequirement());

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

    public function test_plugin_submitted_before_demo_videos_were_required_can_be_approved_without_one(): void
    {
        $plugin = $this->pluginWithoutDemoVideo();
        $this->recordSubmission($plugin, $this->beforeRequirement());

        $this->assertFalse($plugin->requiresDemoVideo());
        $this->assertTrue($plugin->passesRequiredReviewChecks());
        $this->assertNotContains('Demo video showing the plugin working', $plugin->getFailingRequiredChecks());

        $this->assertApproves(User::factory()->create(['email' => 'env-admin@test.com']), $plugin);
    }

    public function test_plugin_submitted_after_demo_videos_were_required_still_needs_one(): void
    {
        $plugin = $this->pluginWithoutDemoVideo();
        $this->recordSubmission($plugin, $this->afterRequirement());

        $this->assertTrue($plugin->requiresDemoVideo());
        $this->assertFalse($plugin->passesRequiredReviewChecks());
        $this->assertContains('Demo video showing the plugin working', $plugin->getFailingRequiredChecks());
    }

    public function test_resubmitting_after_the_cutoff_keeps_the_original_submission_date(): void
    {
        $plugin = $this->pluginWithoutDemoVideo();
        $this->recordSubmission($plugin, $this->beforeRequirement());
        $this->recordSubmission($plugin, $this->afterRequirement(), PluginActivityType::Resubmitted);

        $this->assertFalse($plugin->requiresDemoVideo());
    }

    public function test_old_pending_plugin_without_a_logged_submission_falls_back_to_its_creation_date(): void
    {
        $plugin = $this->pluginWithoutDemoVideo(['created_at' => $this->beforeRequirement()]);

        $this->assertFalse($plugin->requiresDemoVideo());
    }

    public function test_old_draft_that_was_never_submitted_needs_a_demo_video(): void
    {
        $plugin = $this->pluginWithoutDemoVideo(['status' => PluginStatus::Draft, 'created_at' => $this->beforeRequirement()]);

        $this->assertTrue($plugin->requiresDemoVideo());
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function pluginWithoutDemoVideo(array $attributes = []): Plugin
    {
        $plugin = $this->reviewablePlugin();
        $plugin->forceFill(['demo_video_url' => null, 'demo_video_attested_at' => null, ...$attributes])->save();

        return $plugin->fresh();
    }

    private function recordSubmission(Plugin $plugin, Carbon $at, PluginActivityType $type = PluginActivityType::Submitted): void
    {
        $plugin->activities()->create([
            'type' => $type,
            'from_status' => PluginStatus::Draft,
            'to_status' => PluginStatus::Pending,
            'causer_id' => $plugin->user_id,
            'created_at' => $at,
        ]);
    }

    private function beforeRequirement(): Carbon
    {
        return Carbon::parse(Plugin::DEMO_VIDEO_REQUIRED_FROM, 'UTC')->subDay();
    }

    private function afterRequirement(): Carbon
    {
        return Carbon::parse(Plugin::DEMO_VIDEO_REQUIRED_FROM, 'UTC')->addDay();
    }
}
