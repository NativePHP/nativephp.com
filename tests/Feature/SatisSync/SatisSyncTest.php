<?php

namespace Tests\Feature\SatisSync;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Jobs\SyncPluginReleases;
use App\Models\Plugin;
use App\Models\User;
use App\Services\SatisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class SatisSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_approval_does_not_dispatch_sync_plugin_releases(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->paid()->pending()->create();

        $plugin->approve($this->admin->id);

        Bus::assertNotDispatched(SyncPluginReleases::class);
    }

    public function test_filament_sync_to_satis_action_dispatches_job(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->paid()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction('syncToSatis')
            ->assertNotified();

        Bus::assertDispatched(SyncPluginReleases::class, function ($job) use ($plugin) {
            return $job->plugin->is($plugin);
        });
    }

    public function test_sync_to_satis_action_hidden_for_free_plugins(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionHidden('syncToSatis');
    }

    public function test_sync_to_satis_action_visible_for_paid_plugins(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('syncToSatis');
    }

    public function test_satis_synced_at_is_stamped_after_successful_build(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();

        $this->assertNull($plugin->satis_synced_at);

        $satisService = $this->mock(SatisService::class);
        $satisService->shouldReceive('build')
            ->once()
            ->andReturn(['success' => true, 'job_id' => 'test-123']);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: true);
        $job->handle($satisService);

        $plugin->refresh();

        $this->assertNotNull($plugin->satis_synced_at);
    }

    public function test_satis_synced_at_is_not_stamped_after_failed_build(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();

        $this->assertNull($plugin->satis_synced_at);

        $satisService = $this->mock(SatisService::class);
        $satisService->shouldReceive('build')
            ->once()
            ->andReturn(['success' => false, 'error' => 'Build failed']);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: true);
        $job->handle($satisService);

        $plugin->refresh();

        $this->assertNull($plugin->satis_synced_at);
    }

    public function test_is_satis_synced_returns_false_when_never_synced(): void
    {
        $plugin = Plugin::factory()->paid()->create();

        $this->assertFalse($plugin->isSatisSynced());
    }

    public function test_is_satis_synced_returns_true_when_synced(): void
    {
        $plugin = Plugin::factory()->paid()->create([
            'satis_synced_at' => now(),
        ]);

        $this->assertTrue($plugin->isSatisSynced());
    }
}
