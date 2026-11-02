<?php

namespace Tests\Feature;

use App\Models\GitHubInstallation;
use App\Models\User;
use App\Services\GitHubAppService;
use App\Services\GitHubUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GitHubTokenResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_installation_token_first_for_github_app_user(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'testowner',
            'selection_type' => 'all',
        ]);

        $appService = Mockery::mock(GitHubAppService::class);
        $appService->shouldReceive('findInstallationForRepo')
            ->with($user, 'testowner', 'testrepo')
            ->andReturn($installation);
        $appService->shouldReceive('getInstallationToken')
            ->with($installation)
            ->andReturn('ghs_installation_token');

        $this->app->instance(GitHubAppService::class, $appService);

        $service = GitHubUserService::for($user);
        $token = $service->resolveTokenForRepo('testowner', 'testrepo');

        $this->assertEquals('ghs_installation_token', $token);
    }

    public function test_falls_back_to_user_oauth_token_when_no_installation(): void
    {
        $user = User::factory()->withGitHubApp()->create();

        $appService = Mockery::mock(GitHubAppService::class);
        $appService->shouldReceive('findInstallationForRepo')
            ->andReturn(null);

        $this->app->instance(GitHubAppService::class, $appService);

        $service = GitHubUserService::for($user);
        $token = $service->resolveTokenForRepo('testowner', 'testrepo');

        // Should get the user's OAuth token
        $this->assertNotNull($token);
        $this->assertStringStartsWith('ghu_test_token_', $token);
    }

    public function test_falls_back_to_platform_token_when_no_user_token(): void
    {
        $user = User::factory()->create([
            'github_auth_type' => null,
            'github_token' => null,
        ]);

        config(['services.github.token' => 'ghp_platform_token']);

        $service = GitHubUserService::for($user);
        $token = $service->resolveTokenForRepo('testowner', 'testrepo');

        $this->assertEquals('ghp_platform_token', $token);
    }

    public function test_legacy_oauth_user_uses_oauth_token_directly(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();

        $service = GitHubUserService::for($user);
        $token = $service->resolveTokenForRepo('testowner', 'testrepo');

        // Should get the user's OAuth token directly (no installation lookup)
        $this->assertNotNull($token);
        $this->assertStringStartsWith('gho_test_token_', $token);
    }
}
