<?php

namespace Tests\Feature;

use App\Enums\GitHubAuthType;
use App\Features\ShowAuthButtons;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\Concerns\InteractsWithGitHubApp;
use Tests\TestCase;

class GitHubAppUserTokenTest extends TestCase
{
    use InteractsWithGitHubApp;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);

        $this->configureGitHubApp();

        config([
            'services.github.client_id' => 'legacy-client',
            'services.github.client_secret' => 'legacy-secret',
        ]);
    }

    private function fakeSocialiteUser(User $user, string $intent): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->id = $user->github_id;
        $socialiteUser->nickname = $user->github_username;
        $socialiteUser->name = $user->name;
        $socialiteUser->email = $user->email;
        $socialiteUser->token = 'ghu_app_token';
        $socialiteUser->refreshToken = 'ghr_app_refresh';
        $socialiteUser->expiresIn = 28800;

        $provider = Mockery::mock(GithubProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github-app')->andReturn($provider);

        session([
            'github_auth_intent' => $intent,
            'github_auth_driver' => 'github-app',
        ]);
    }

    public function test_login_stores_the_refresh_token_and_expiry(): void
    {
        Http::fake();

        $user = User::factory()->withGitHubApp()->create();
        $this->fakeSocialiteUser($user, 'login');

        $this->get('/auth/github/callback');

        $user->refresh();

        $this->assertSame('ghr_app_refresh', $user->getGitHubRefreshToken());
        $this->assertTrue($user->github_token_expires_at->between(now()->addHours(7), now()->addHours(9)));
    }

    public function test_an_expired_token_is_refreshed_before_use(): void
    {
        Http::fake([
            'github.com/login/oauth/access_token' => Http::response([
                'access_token' => 'ghu_fresh',
                'refresh_token' => 'ghr_fresh',
                'expires_in' => 28800,
            ]),
        ]);

        $user = User::factory()->withGitHubApp()->create([
            'github_refresh_token' => encrypt('ghr_old'),
            'github_token_expires_at' => now()->subMinute(),
        ]);

        $this->assertSame('ghu_fresh', $user->getGitHubToken());

        Http::assertSent(fn (Request $request) => $request['grant_type'] === 'refresh_token'
            && $request['refresh_token'] === 'ghr_old'
            && $request['client_id'] === 'Iv1.testclient');

        $user->refresh();

        $this->assertSame('ghr_fresh', $user->getGitHubRefreshToken());
        $this->assertTrue($user->github_token_expires_at->isFuture());
    }

    public function test_a_token_that_has_not_expired_is_used_as_is(): void
    {
        Http::fake();

        $user = User::factory()->withGitHubApp()->create([
            'github_token' => encrypt('ghu_current'),
            'github_refresh_token' => encrypt('ghr_current'),
            'github_token_expires_at' => now()->addHours(4),
        ]);

        $this->assertSame('ghu_current', $user->getGitHubToken());

        Http::assertNothingSent();
    }

    public function test_a_rejected_refresh_token_clears_the_stored_tokens(): void
    {
        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['error' => 'bad_refresh_token']),
        ]);

        $user = User::factory()->withGitHubApp()->create([
            'github_refresh_token' => encrypt('ghr_revoked'),
            'github_token_expires_at' => now()->subMinute(),
        ]);

        $this->assertNull($user->getGitHubToken());

        $user->refresh();

        $this->assertNull($user->github_token);
        $this->assertNull($user->github_refresh_token);
        $this->assertNotNull($user->github_id);
    }

    public function test_upgrading_by_linking_revokes_the_legacy_oauth_grant(): void
    {
        Http::fake();

        $user = User::factory()->withLegacyGitHub()->create(['github_token' => encrypt('gho_legacy')]);
        $this->fakeSocialiteUser($user, 'link');

        $this->actingAs($user)->get('/auth/github/callback');

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
            && $request->url() === 'https://api.github.com/applications/legacy-client/grant'
            && $request['access_token'] === 'gho_legacy'
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('legacy-client:legacy-secret')));

        $this->assertEquals(GitHubAuthType::App, $user->fresh()->github_auth_type);
    }

    public function test_upgrading_by_logging_in_revokes_the_legacy_oauth_grant(): void
    {
        Http::fake();

        $user = User::factory()->withLegacyGitHub()->create(['github_token' => encrypt('gho_legacy')]);
        $this->fakeSocialiteUser($user, 'login');

        $this->get('/auth/github/callback');

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
            && str_ends_with($request->url(), '/applications/legacy-client/grant')
            && $request['access_token'] === 'gho_legacy');
    }

    public function test_github_app_users_logging_in_again_do_not_trigger_a_revoke(): void
    {
        Http::fake();

        $user = User::factory()->withGitHubApp()->create();
        $this->fakeSocialiteUser($user, 'login');

        $this->get('/auth/github/callback');

        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/grant'));
    }

    public function test_disconnecting_clears_the_refresh_token(): void
    {
        Http::fake();

        $user = User::factory()->withGitHubApp()->create([
            'github_refresh_token' => encrypt('ghr_current'),
            'github_token_expires_at' => now()->addHours(4),
        ]);

        $this->actingAs($user)->delete(route('github.disconnect'));

        $user->refresh();

        $this->assertNull($user->github_refresh_token);
        $this->assertNull($user->github_token_expires_at);
    }
}
