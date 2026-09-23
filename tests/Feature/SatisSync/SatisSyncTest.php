<?php

namespace Tests\Feature\SatisSync;

use App\Enums\PluginType;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Jobs\RemovePluginFromSatis;
use App\Jobs\SyncPluginReleases;
use App\Models\Plugin;
use App\Models\User;
use App\Services\SatisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
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

    public function test_submitting_a_paid_plugin_queues_a_satis_build(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->paid()->draft()->create();

        $plugin->submit();

        Bus::assertDispatched(SyncPluginReleases::class, function ($job) use ($plugin) {
            return $job->plugin->is($plugin);
        });
    }

    public function test_submitting_a_free_plugin_does_not_queue_a_satis_build(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->free()->draft()->create();

        $plugin->submit();

        Bus::assertNotDispatched(SyncPluginReleases::class);
    }

    public function test_approval_queues_a_satis_build_for_paid_plugins(): void
    {
        Http::fake();
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->paid()->pending()->create();

        $plugin->approve($this->admin->id);

        Bus::assertDispatched(SyncPluginReleases::class, function ($job) use ($plugin) {
            return $job->plugin->is($plugin);
        });
    }

    public function test_approval_does_not_queue_a_satis_build_for_free_plugins(): void
    {
        Http::fake();
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->free()->pending()->create();

        $plugin->approve($this->admin->id);

        Bus::assertNotDispatched(SyncPluginReleases::class);
    }

    public function test_switching_a_plugin_to_paid_queues_a_satis_build(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->free()->approved()->create();

        $plugin->update(['type' => PluginType::Paid]);

        Bus::assertDispatched(SyncPluginReleases::class, function ($job) use ($plugin) {
            return $job->plugin->is($plugin);
        });
    }

    public function test_switching_a_plugin_to_free_removes_it_from_satis(): void
    {
        Bus::fake([RemovePluginFromSatis::class]);

        $plugin = Plugin::factory()->paid()->approved()->create([
            'satis_synced_at' => now(),
        ]);

        $plugin->update(['type' => PluginType::Free]);

        Bus::assertDispatched(RemovePluginFromSatis::class, function ($job) use ($plugin) {
            return $job->packageName === $plugin->name;
        });

        $this->assertNull($plugin->fresh()->satis_synced_at);
    }

    public function test_editing_a_plugin_without_changing_its_type_leaves_satis_alone(): void
    {
        Bus::fake([SyncPluginReleases::class, RemovePluginFromSatis::class]);

        $plugin = Plugin::factory()->paid()->approved()->create();

        $plugin->update(['description' => 'A freshly worded description.']);

        Bus::assertNotDispatched(SyncPluginReleases::class);
        Bus::assertNotDispatched(RemovePluginFromSatis::class);
    }

    public function test_deleting_a_plugin_removes_it_from_satis(): void
    {
        Bus::fake([RemovePluginFromSatis::class]);

        $plugin = Plugin::factory()->paid()->approved()->create();
        $packageName = $plugin->name;

        $plugin->delete();

        Bus::assertDispatched(RemovePluginFromSatis::class, function ($job) use ($packageName) {
            return $job->packageName === $packageName;
        });
    }

    public function test_type_changes_survive_satis_being_unconfigured(): void
    {
        Http::fake();
        config(['services.satis.url' => null, 'services.satis.api_key' => null]);

        $plugin = Plugin::factory()->free()->approved()->create();

        $plugin->update(['type' => PluginType::Paid]);
        $plugin->update(['type' => PluginType::Free]);

        $this->assertTrue($plugin->fresh()->isFree());
    }

    public function test_satis_service_reports_missing_configuration_rather_than_failing(): void
    {
        config(['services.satis.url' => null, 'services.satis.api_key' => null]);

        $service = new SatisService;

        $this->assertFalse($service->removePackage('acme/widget')['success']);
        $this->assertFalse($service->build([Plugin::factory()->paid()->approved()->create()])['success']);
    }

    public function test_remove_plugin_from_satis_job_calls_the_satis_api(): void
    {
        $satisService = $this->mock(SatisService::class);
        $satisService->shouldReceive('removePackage')
            ->once()
            ->with('acme/widget')
            ->andReturn(['success' => true]);

        (new RemovePluginFromSatis('acme/widget'))->handle($satisService);
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

    public function test_building_a_single_plugin_stamps_satis_synced_at(): void
    {
        Http::fake(['*' => Http::response(['job_id' => 'test-123', 'message' => 'Build started'], 200)]);

        config(['services.satis.url' => 'https://satis.test', 'services.satis.api_key' => 'test-key']);

        $plugin = Plugin::factory()->paid()->approved()->create();

        (new SatisService)->buildForPlugin($plugin);

        $this->assertNotNull($plugin->fresh()->satis_synced_at);
    }

    public function test_building_a_single_plugin_does_not_stamp_satis_synced_at_on_failure(): void
    {
        Http::fake(['*' => Http::response(['error' => 'Boom'], 500)]);

        config(['services.satis.url' => 'https://satis.test', 'services.satis.api_key' => 'test-key']);

        $plugin = Plugin::factory()->paid()->approved()->create();

        (new SatisService)->buildForPlugin($plugin);

        $this->assertNull($plugin->fresh()->satis_synced_at);
    }

    public function test_build_all_only_includes_paid_plugins(): void
    {
        Http::fake(['*' => Http::response(['job_id' => 'test-123', 'message' => 'Build started'], 200)]);

        config(['services.satis.url' => 'https://satis.test', 'services.satis.api_key' => 'test-key']);

        $paidPlugin = Plugin::factory()->paid()->approved()->create();
        Plugin::factory()->free()->approved()->create();

        $service = new SatisService;
        $result = $service->buildAll();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['plugins_count']);

        // buildAll dispatches an individual partial build per paid plugin, each
        // authenticated with the owner's token, so a single failure can never
        // authoritatively overwrite the published index.
        Http::assertSentCount(1);
        Http::assertSent(function ($request) use ($paidPlugin) {
            $data = $request->data();
            $plugins = $data['plugins'] ?? [];

            return count($plugins) === 1
                && $plugins[0]['name'] === $paidPlugin->name
                && $data['full_build'] === false;
        });
    }
}
