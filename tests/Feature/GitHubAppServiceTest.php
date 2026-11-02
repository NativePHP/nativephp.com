<?php

namespace Tests\Feature;

use App\Models\GitHubInstallation;
use App\Models\User;
use App\Services\GitHubAppService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithGitHubApp;
use Tests\TestCase;

class GitHubAppServiceTest extends TestCase
{
    use InteractsWithGitHubApp;
    use RefreshDatabase;

    public function test_installation_url_points_at_the_nativephp_app_by_default(): void
    {
        $this->assertSame(
            'https://github.com/apps/nativephp-plugin-marketplace/installations/new',
            (new GitHubAppService)->installationUrl()
        );
    }

    public function test_jwt_is_signed_with_the_app_private_key(): void
    {
        $publicKey = $this->configureGitHubApp();

        $jwt = (new GitHubAppService)->generateJwt();
        $claims = JWT::decode($jwt, new Key($publicKey, 'RS256'));

        $this->assertSame('12345', $claims->iss);
        $this->assertLessThanOrEqual(time(), $claims->iat);
        $this->assertGreaterThan(time(), $claims->exp);
        $this->assertLessThanOrEqual(10 * 60, $claims->exp - $claims->iat);
    }

    public function test_jwt_accepts_a_private_key_stored_on_one_line(): void
    {
        $publicKey = $this->configureGitHubApp();

        config(['services.github_app.private_key' => str_replace("\n", '\n', config('services.github_app.private_key'))]);

        $this->assertStringNotContainsString("\n", config('services.github_app.private_key'));

        $claims = JWT::decode((new GitHubAppService)->generateJwt(), new Key($publicKey, 'RS256'));

        $this->assertSame('12345', $claims->iss);
    }

    public function test_jwt_fails_clearly_when_the_private_key_is_missing(): void
    {
        config(['services.github_app.private_key' => '']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('GITHUB_APP_PRIVATE_KEY');

        (new GitHubAppService)->generateJwt();
    }

    public function test_a_missing_private_key_does_not_break_installation_token_lookups(): void
    {
        config(['services.github_app.private_key' => '']);

        $installation = GitHubInstallation::factory()->create();

        $this->assertNull((new GitHubAppService)->refreshInstallationToken($installation));
    }

    public function test_find_installation_for_repo_with_all_repos_selected(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'testuser',
            'selection_type' => 'all',
        ]);

        $service = new GitHubAppService;
        $found = $service->findInstallationForRepo($user, 'testuser', 'my-plugin');

        $this->assertNotNull($found);
        $this->assertEquals($installation->id, $found->id);
    }

    public function test_find_installation_for_repo_with_selected_repos(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        GitHubInstallation::factory()->selectedRepos(['testuser/my-plugin'])->create([
            'user_id' => $user->id,
            'account_login' => 'testuser',
        ]);

        $service = new GitHubAppService;

        $found = $service->findInstallationForRepo($user, 'testuser', 'my-plugin');
        $this->assertNotNull($found);

        $notFound = $service->findInstallationForRepo($user, 'testuser', 'other-repo');
        $this->assertNull($notFound);
    }

    public function test_find_installation_for_repo_ignores_suspended(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        GitHubInstallation::factory()->suspended()->create([
            'user_id' => $user->id,
            'account_login' => 'testuser',
            'selection_type' => 'all',
        ]);

        $service = new GitHubAppService;
        $found = $service->findInstallationForRepo($user, 'testuser', 'my-plugin');

        $this->assertNull($found);
    }

    public function test_find_installation_for_repo_returns_null_for_wrong_owner(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'testuser',
            'selection_type' => 'all',
        ]);

        $service = new GitHubAppService;
        $found = $service->findInstallationForRepo($user, 'otheruser', 'my-plugin');

        $this->assertNull($found);
    }

    public function test_find_installation_for_repo_case_insensitive_owner(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'TestUser',
            'selection_type' => 'all',
        ]);

        $service = new GitHubAppService;
        $found = $service->findInstallationForRepo($user, 'testuser', 'my-plugin');

        $this->assertNotNull($found);
    }
}
