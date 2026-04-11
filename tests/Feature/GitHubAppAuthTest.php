<?php

namespace Tests\Feature;

use App\Enums\GitHubAuthType;
use App\Features\ShowAuthButtons;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GitHubAppAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_login_via_github_app_stores_app_auth_type(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->id = 12345;
        $socialiteUser->nickname = 'testuser';
        $socialiteUser->name = 'Test User';
        $socialiteUser->email = 'test@example.com';
        $socialiteUser->token = 'ghu_test_token';

        $provider = Mockery::mock(\Laravel\Socialite\Two\GithubProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('github-app')
            ->andReturn($provider);

        session([
            'github_auth_intent' => 'login',
            'github_auth_driver' => 'github-app',
        ]);

        $response = $this->get('/auth/github/callback');

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'github_id' => '12345',
            'github_username' => 'testuser',
            'github_auth_type' => 'app',
        ]);
    }

    public function test_login_via_legacy_oauth_stores_oauth_auth_type(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->id = 12345;
        $socialiteUser->nickname = 'testuser';
        $socialiteUser->name = 'Test User';
        $socialiteUser->email = 'test@example.com';
        $socialiteUser->token = 'gho_test_token';

        $provider = Mockery::mock(\Laravel\Socialite\Two\GithubProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($provider);

        session([
            'github_auth_intent' => 'login',
            'github_auth_driver' => 'github',
        ]);

        $response = $this->get('/auth/github/callback');

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'github_id' => '12345',
            'github_username' => 'testuser',
            'github_auth_type' => 'oauth',
        ]);
    }

    public function test_linking_github_app_updates_existing_user_auth_type(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->id = $user->github_id;
        $socialiteUser->nickname = 'newusername';
        $socialiteUser->token = 'ghu_new_token';

        $provider = Mockery::mock(\Laravel\Socialite\Two\GithubProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('github-app')
            ->andReturn($provider);

        session([
            'github_auth_intent' => 'link',
            'github_auth_driver' => 'github-app',
        ]);

        $this->actingAs($user)
            ->get('/auth/github/callback');

        $user->refresh();
        $this->assertEquals(GitHubAuthType::App, $user->github_auth_type);
        $this->assertEquals('newusername', $user->github_username);
    }

    public function test_user_model_auth_type_helpers(): void
    {
        $appUser = User::factory()->withGitHubApp()->create();
        $oauthUser = User::factory()->withLegacyGitHub()->create();
        $noGithubUser = User::factory()->create();

        $this->assertTrue($appUser->isUsingGitHubApp());
        $this->assertFalse($appUser->isUsingLegacyOAuth());
        $this->assertFalse($appUser->needsGitHubAppMigration());

        $this->assertFalse($oauthUser->isUsingGitHubApp());
        $this->assertTrue($oauthUser->isUsingLegacyOAuth());
        $this->assertTrue($oauthUser->needsGitHubAppMigration());

        $this->assertFalse($noGithubUser->isUsingGitHubApp());
        $this->assertFalse($noGithubUser->isUsingLegacyOAuth());
        $this->assertFalse($noGithubUser->needsGitHubAppMigration());
    }

    public function test_github_app_redirect_uses_app_driver_when_configured(): void
    {
        config(['services.github_app.client_id' => 'test_client_id']);

        $user = User::factory()->create();

        $provider = Mockery::mock(\Laravel\Socialite\Two\GithubProvider::class);
        $provider->shouldReceive('scopes')
            ->with(['read:user', 'user:email'])
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->andReturn(redirect('https://github.com/login/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->with('github-app')
            ->andReturn($provider);

        $response = $this->actingAs($user)
            ->get('/auth/github');

        $response->assertRedirect();
    }
}
