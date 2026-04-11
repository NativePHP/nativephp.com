<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Plugins\Create;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class GitHubMigrationBannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
    }

    public function test_legacy_oauth_user_sees_migration_banner_on_integrations(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->withLegacyGitHub()->create();

        $response = $this->actingAs($user)->get('/dashboard/integrations');

        $response->assertStatus(200);
        $response->assertSee('GitHub Connection Upgrade Required');
        $response->assertSee('Upgrade GitHub Connection');
    }

    public function test_github_app_user_does_not_see_migration_banner(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->withGitHubApp()->create();

        $response = $this->actingAs($user)->get('/dashboard/integrations');

        $response->assertStatus(200);
        $response->assertDontSee('GitHub Connection Upgrade Required');
    }

    public function test_user_without_github_does_not_see_migration_banner(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/integrations');

        $response->assertStatus(200);
        $response->assertDontSee('GitHub Connection Upgrade Required');
    }

    public function test_legacy_oauth_user_sees_banner_on_plugins_index(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->withLegacyGitHub()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard/developer/plugins');

        $response->assertStatus(200);
        $response->assertSee('GitHub Connection Upgrade Required');
    }

    public function test_legacy_oauth_user_is_blocked_from_plugin_creation_via_livewire(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', 'testuser/test-plugin')
            ->set('pluginType', 'free')
            ->call('createPlugin')
            ->assertHasNoErrors();

        // Verify no plugin was created
        $this->assertDatabaseCount('plugins', 0);
    }

    public function test_legacy_oauth_user_sees_blocking_banner_on_plugin_create(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->withLegacyGitHub()->create();

        $response = $this->actingAs($user)->get('/dashboard/developer/plugins/create');

        $response->assertStatus(200);
        $response->assertSee('GitHub Connection Upgrade Required');
        $response->assertSee('You must upgrade your GitHub connection before you can submit or manage plugins.');
    }
}
