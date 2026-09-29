<?php

namespace Tests\Feature;

use App\Enums\PluginStatus;
use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Jobs\SyncPluginReleases;
use App\Livewire\Customer\Plugins\Show;
use App\Models\GitHubInstallation;
use App\Models\Plugin;
use App\Models\User;
use App\Services\PluginSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\TestCase;

class GitHubAppRepositoryEventsTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK_SECRET = 'test_webhook_secret';

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);

        config(['services.github_app.webhook_secret' => self::WEBHOOK_SECRET]);
    }

    private function sendAppWebhook(string $event, array $payload): TestResponse
    {
        $body = json_encode($payload);

        return $this->call('POST', '/webhooks/github-app', [], [], [], [
            'HTTP_X_GITHUB_EVENT' => $event,
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $body, self::WEBHOOK_SECRET),
            'CONTENT_TYPE' => 'application/json',
        ], $body);
    }

    private function coveredPlugin(array $attributes = []): Plugin
    {
        $user = User::factory()->withGitHubApp()->create();

        GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'acme',
            'selection_type' => 'all',
        ]);

        return Plugin::factory()->create([
            'user_id' => $user->id,
            'repository_url' => 'https://github.com/acme/camera-plugin',
            ...$attributes,
        ]);
    }

    public function test_push_event_from_the_app_syncs_the_matching_plugin(): void
    {
        Bus::fake([SyncPluginReleases::class]);
        $plugin = $this->coveredPlugin();

        $this->mock(PluginSyncService::class, function (MockInterface $mock) use ($plugin): void {
            $mock->shouldReceive('sync')->once()->withArgs(fn (Plugin $synced) => $synced->is($plugin))->andReturn(true);
        });

        $this->sendAppWebhook('push', ['repository' => ['full_name' => 'acme/camera-plugin']])
            ->assertOk()
            ->assertJson(['plugins' => 1]);

        Bus::assertNotDispatched(SyncPluginReleases::class);
    }

    public function test_release_event_from_the_app_syncs_releases(): void
    {
        Bus::fake([SyncPluginReleases::class]);
        $plugin = $this->coveredPlugin();

        $this->mock(PluginSyncService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sync')->once()->andReturn(true);
        });

        $this->sendAppWebhook('release', ['repository' => ['full_name' => 'acme/camera-plugin']])
            ->assertOk();

        Bus::assertDispatched(SyncPluginReleases::class, fn (SyncPluginReleases $job) => $job->plugin->is($plugin));
    }

    public function test_events_for_inactive_plugins_are_ignored(): void
    {
        $this->coveredPlugin(['is_active' => false]);

        $this->mock(PluginSyncService::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('sync');
        });

        $this->sendAppWebhook('push', ['repository' => ['full_name' => 'acme/camera-plugin']])
            ->assertOk()
            ->assertJson(['plugins' => 0]);
    }

    public function test_repository_events_need_a_valid_signature(): void
    {
        $this->coveredPlugin();

        $this->postJson('/webhooks/github-app', ['repository' => ['full_name' => 'acme/camera-plugin']], [
            'X-GitHub-Event' => 'push',
            'X-Hub-Signature-256' => 'sha256=invalid',
        ])->assertForbidden();
    }

    public function test_preflight_checks_skip_the_repo_webhook_when_the_app_covers_the_repo(): void
    {
        Http::fake(['*' => Http::response([], 404)]);

        $plugin = $this->coveredPlugin(['webhook_installed' => false]);
        $plugin->update(['status' => PluginStatus::Draft]);

        Livewire::actingAs($plugin->user)
            ->test(Show::class, $plugin->routeParams())
            ->call('runPreflightChecks');

        $this->assertTrue($plugin->fresh()->webhook_installed);
        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/hooks'));
    }

    public function test_retrying_the_webhook_does_not_create_one_when_the_app_covers_the_repo(): void
    {
        Http::fake(['*' => Http::response([], 404)]);

        $plugin = $this->coveredPlugin(['webhook_installed' => false]);

        Livewire::actingAs($plugin->user)
            ->test(Show::class, $plugin->routeParams())
            ->call('retryWebhook');

        $this->assertTrue($plugin->fresh()->webhook_installed);
        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/hooks'));
    }

    public function test_plugins_the_app_cannot_reach_still_get_a_repo_webhook(): void
    {
        Http::fake(['*' => Http::response([], 404)]);

        $user = User::factory()->withGitHubApp()->create();
        $plugin = Plugin::factory()->create([
            'user_id' => $user->id,
            'repository_url' => 'https://github.com/acme/camera-plugin',
            'webhook_installed' => false,
        ]);

        $this->assertFalse($plugin->isReachableViaGitHubApp());

        Livewire::actingAs($user)
            ->test(Show::class, $plugin->routeParams())
            ->call('retryWebhook');

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/repos/acme/camera-plugin/hooks'));
    }
}
