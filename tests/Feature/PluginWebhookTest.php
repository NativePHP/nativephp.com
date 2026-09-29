<?php

namespace Tests\Feature;

use App\Jobs\SyncPluginReleases;
use App\Models\Plugin;
use App\Services\PluginSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PluginWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ping_event_succeeds_for_unapproved_plugin(): void
    {
        $plugin = Plugin::factory()->create();

        $response = $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            [],
            ['X-GitHub-Event' => 'ping']
        );

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'pong']);
    }

    #[Test]
    public function ping_event_succeeds_for_approved_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        $response = $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            [],
            ['X-GitHub-Event' => 'ping']
        );

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'pong']);
    }

    #[Test]
    public function non_ping_event_returns_403_for_inactive_plugin(): void
    {
        $plugin = Plugin::factory()->inactive()->create();

        $response = $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            [],
            ['X-GitHub-Event' => 'push']
        );

        $response->assertForbidden()
            ->assertJson(['error' => 'Plugin is not active']);
    }

    #[Test]
    public function non_ping_event_succeeds_for_unapproved_but_active_plugin(): void
    {
        $plugin = Plugin::factory()->create([
            'is_active' => true,
            'last_synced_at' => now(),
        ]);

        $this->mock(PluginSyncService::class, function ($mock) {
            $mock->shouldReceive('sync')->once()->andReturn(true);
        });

        $response = $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            [],
            ['X-GitHub-Event' => 'push']
        );

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function published_release_queues_a_release_sync(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->create(['last_synced_at' => now()]);

        $this->mock(PluginSyncService::class, function ($mock) {
            $mock->shouldReceive('sync')->once()->andReturn(true);
        });

        $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            ['action' => 'published', 'release' => ['id' => 101]],
            ['X-GitHub-Event' => 'release']
        )->assertOk()->assertJson(['message' => 'Release sync queued']);

        Bus::assertDispatched(SyncPluginReleases::class, fn (SyncPluginReleases $job) => $job->plugin->is($plugin));
    }

    #[Test]
    public function release_events_other_than_published_are_ignored(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->create(['last_synced_at' => now()]);

        $this->mock(PluginSyncService::class, function ($mock) {
            $mock->shouldNotReceive('sync');
        });

        foreach (['created', 'released', 'prereleased', 'edited', 'deleted'] as $action) {
            $this->postJson(
                route('webhooks.plugins', $plugin->webhook_secret),
                ['action' => $action, 'release' => ['id' => 101]],
                ['X-GitHub-Event' => 'release']
            )->assertOk()->assertJson(['message' => 'Release event ignored']);
        }

        Bus::assertNotDispatched(SyncPluginReleases::class);
    }

    #[Test]
    public function the_same_release_delivered_twice_is_only_synced_once(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->create(['last_synced_at' => now()]);

        $this->mock(PluginSyncService::class, function ($mock) {
            $mock->shouldReceive('sync')->once()->andReturn(true);
        });

        $deliver = fn () => $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            ['action' => 'published', 'release' => ['id' => 101]],
            ['X-GitHub-Event' => 'release']
        );

        $deliver()->assertOk()->assertJson(['message' => 'Release sync queued']);
        $deliver()->assertOk()->assertJson(['message' => 'Release event ignored']);

        Bus::assertDispatchedTimes(SyncPluginReleases::class, 1);
    }

    #[Test]
    public function release_payload_sent_as_form_data_is_read(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->create(['last_synced_at' => now()]);

        $this->mock(PluginSyncService::class, function ($mock) {
            $mock->shouldReceive('sync')->once()->andReturn(true);
        });

        $this->post(
            route('webhooks.plugins', $plugin->webhook_secret),
            ['payload' => json_encode(['action' => 'published', 'release' => ['id' => 101]])],
            ['X-GitHub-Event' => 'release']
        )->assertOk()->assertJson(['message' => 'Release sync queued']);

        Bus::assertDispatchedTimes(SyncPluginReleases::class, 1);
    }

    #[Test]
    public function invalid_secret_returns_404(): void
    {
        $response = $this->postJson(
            route('webhooks.plugins', 'invalid-secret'),
            [],
            ['X-GitHub-Event' => 'ping']
        );

        $response->assertNotFound();
    }
}
