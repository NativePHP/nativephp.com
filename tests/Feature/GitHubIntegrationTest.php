<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class GitHubIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_user_with_active_max_license_sees_github_integration_card(): void
    {
        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)->get('/customer/licenses');

        $response->assertStatus(200);
        $response->assertSee('GitHub Repository Access');
        $response->assertSee('Connect GitHub');
    }

    public function test_user_without_max_license_does_not_see_github_integration_card(): void
    {
        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)->get('/customer/licenses');

        $response->assertStatus(200);
        $response->assertDontSee('GitHub Repository Access');
    }

    public function test_user_with_connected_github_sees_username(): void
    {
        $user = User::factory()->create([
            'github_username' => 'testuser',
            'github_id' => '123456',
        ]);
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)->get('/customer/licenses');

        $response->assertStatus(200);
        $response->assertSee('Connected as');
        $response->assertSee('@testuser');
        $response->assertSee('Request Repository Access');
    }

    public function test_user_can_request_repo_access_with_active_max_license(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile/collaborators/*' => Http::response([], 201),
        ]);

        $user = User::factory()->create([
            'github_username' => 'testuser',
            'github_id' => '123456',
        ]);
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)
            ->post('/customer/github/request-access');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $user->refresh();
        $this->assertNotNull($user->mobile_repo_access_granted_at);
    }

    public function test_user_cannot_request_repo_access_without_max_license(): void
    {
        $user = User::factory()->create([
            'github_username' => 'testuser',
            'github_id' => '123456',
        ]);
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)
            ->post('/customer/github/request-access');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_request_repo_access_without_connected_github(): void
    {
        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)
            ->post('/customer/github/request-access');

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Please connect your GitHub account first.');
    }

    public function test_user_can_disconnect_github_account(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile/collaborators/*' => Http::response([], 204),
        ]);

        $user = User::factory()->create([
            'github_username' => 'testuser',
            'github_id' => '123456',
            'mobile_repo_access_granted_at' => now(),
        ]);
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)
            ->delete('/customer/github/disconnect');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'github_username' => null,
            'github_id' => null,
            'mobile_repo_access_granted_at' => null,
        ]);
    }

    public function test_scheduled_command_removes_access_for_expired_max_license(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile/collaborators/*' => Http::response([], 204),
        ]);

        $user = User::factory()->create([
            'github_username' => 'testuser',
            'github_id' => '123456',
            'mobile_repo_access_granted_at' => now()->subDays(10),
        ]);
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->subDays(1), // Expired
            'is_suspended' => false,
        ]);

        $this->artisan('github:remove-expired-access')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'mobile_repo_access_granted_at' => null,
        ]);
    }

    public function test_scheduled_command_does_not_remove_access_for_active_max_license(): void
    {
        $user = User::factory()->create([
            'github_username' => 'testuser',
            'github_id' => '123456',
            'mobile_repo_access_granted_at' => now()->subDays(10),
        ]);
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30), // Active
            'is_suspended' => false,
        ]);

        $this->artisan('github:remove-expired-access')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'github_username' => 'testuser',
            'mobile_repo_access_granted_at' => $user->mobile_repo_access_granted_at,
        ]);
    }

    public function test_user_with_granted_access_sees_access_status(): void
    {
        $user = User::factory()->create([
            'github_username' => 'testuser',
            'github_id' => '123456',
            'mobile_repo_access_granted_at' => now()->subHours(2),
        ]);
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)->get('/customer/licenses');

        $response->assertStatus(200);
        $response->assertSee('Access Granted');
        $response->assertSee('@testuser');
        $response->assertDontSee('Request Repository Access');
    }
}
