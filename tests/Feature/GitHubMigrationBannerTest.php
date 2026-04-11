<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
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

        $response = $this->actingAs($user)->get('/customer/integrations');

        $response->assertStatus(200);
        $response->assertSee('GitHub Connection Upgrade Required');
        $response->assertSee('Upgrade GitHub Connection');
    }

    public function test_github_app_user_does_not_see_migration_banner(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->withGitHubApp()->create();

        $response = $this->actingAs($user)->get('/customer/integrations');

        $response->assertStatus(200);
        $response->assertDontSee('GitHub Connection Upgrade Required');
    }

    public function test_user_without_github_does_not_see_migration_banner(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/customer/integrations');

        $response->assertStatus(200);
        $response->assertDontSee('GitHub Connection Upgrade Required');
    }

    public function test_legacy_oauth_user_sees_banner_on_plugins_index(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->withLegacyGitHub()->create();

        $response = $this->actingAs($user)->get('/customer/plugins');

        $response->assertStatus(200);
        $response->assertSee('GitHub Connection Upgrade Required');
    }

    public function test_legacy_oauth_user_is_blocked_from_plugin_submission(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();

        $response = $this->actingAs($user)
            ->post('/customer/plugins', [
                'repository' => 'testuser/test-plugin',
                'type' => 'free',
            ]);

        $response->assertRedirect(route('customer.integrations'));
        $response->assertSessionHas('error', 'Please upgrade your GitHub connection before submitting plugins.');
    }

    public function test_legacy_oauth_user_sees_blocking_banner_on_plugin_create(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->withLegacyGitHub()->create();

        $response = $this->actingAs($user)->get('/customer/plugins/submit');

        $response->assertStatus(200);
        $response->assertSee('GitHub Connection Upgrade Required');
        $response->assertSee('You must upgrade your GitHub connection before you can submit or manage plugins.');
    }
}
