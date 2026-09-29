<?php

namespace Tests\Feature;

use App\Enums\GitHubAuthType;
use App\Models\Plugin;
use App\Models\User;
use App\Services\GitHubUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubLegacyOAuthRetirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_tokens_are_used_until_the_cutoff_date(): void
    {
        config(['services.github.legacy_oauth_cutoff_date' => now()->addDay()->toDateString()]);

        $user = User::factory()->withLegacyGitHub()->create(['github_token' => encrypt('gho_legacy')]);

        $this->assertSame('gho_legacy', $user->getGitHubToken());
    }

    public function test_legacy_tokens_are_ignored_once_the_cutoff_date_has_passed(): void
    {
        config([
            'services.github.legacy_oauth_cutoff_date' => now()->subDay()->toDateString(),
            'services.github.token' => 'ghp_platform',
        ]);

        $user = User::factory()->withLegacyGitHub()->create(['github_token' => encrypt('gho_legacy')]);

        $this->assertNull($user->getGitHubToken());
        $this->assertFalse($user->hasGitHubToken());
        $this->assertSame('ghp_platform', GitHubUserService::for($user)->resolveTokenForRepo('acme', 'public-plugin'));
    }

    public function test_github_app_tokens_are_unaffected_by_the_cutoff_date(): void
    {
        config(['services.github.legacy_oauth_cutoff_date' => now()->subDay()->toDateString()]);

        $user = User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_app')]);

        $this->assertSame('ghu_app', $user->getGitHubToken());
    }

    public function test_command_refuses_to_run_before_the_cutoff_date(): void
    {
        config(['services.github.legacy_oauth_cutoff_date' => now()->addWeek()->toDateString()]);

        $user = User::factory()->withLegacyGitHub()->create();

        $this->artisan('github:retire-legacy-oauth')
            ->expectsOutputToContain("hasn't passed yet")
            ->assertFailed();

        $this->assertNotNull($user->fresh()->github_token);
    }

    public function test_command_refuses_to_run_without_a_cutoff_date(): void
    {
        $this->artisan('github:retire-legacy-oauth')
            ->expectsOutputToContain('GITHUB_LEGACY_OAUTH_CUTOFF_DATE')
            ->assertFailed();
    }

    public function test_command_clears_legacy_tokens_but_keeps_github_identity(): void
    {
        config(['services.github.legacy_oauth_cutoff_date' => now()->subDay()->toDateString()]);

        $legacy = User::factory()->withLegacyGitHub()->create();
        $appUser = User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_app')]);

        $this->artisan('github:retire-legacy-oauth')
            ->expectsConfirmation('Clear 1 legacy token(s)? GitHub IDs and usernames are kept, so sign-in and repo invites keep working.', 'yes')
            ->expectsOutputToContain('Cleared 1 legacy token(s).')
            ->assertSuccessful();

        $legacy->refresh();

        $this->assertNull($legacy->github_token);
        $this->assertNotNull($legacy->github_id);
        $this->assertNotNull($legacy->github_username);
        $this->assertEquals(GitHubAuthType::OAuth, $legacy->github_auth_type);
        $this->assertSame('ghu_app', $appUser->fresh()->getGitHubToken());
    }

    public function test_command_lists_plugin_authors_who_never_moved_over(): void
    {
        config(['services.github.legacy_oauth_cutoff_date' => now()->subDay()->toDateString()]);

        $author = User::factory()->withLegacyGitHub()->create();
        Plugin::factory()->create(['user_id' => $author->id, 'name' => 'acme/camera-plugin']);

        $this->artisan('github:retire-legacy-oauth --dry-run')
            ->expectsOutputToContain('1 of them plugin authors')
            ->expectsTable(['User', 'Email', 'Plugins'], [[$author->id, $author->email, 'acme/camera-plugin']])
            ->expectsOutputToContain('DRY RUN')
            ->assertSuccessful();

        $this->assertNotNull($author->fresh()->github_token);
    }

    public function test_declining_the_confirmation_changes_nothing(): void
    {
        config(['services.github.legacy_oauth_cutoff_date' => now()->subDay()->toDateString()]);

        $legacy = User::factory()->withLegacyGitHub()->create();

        $this->artisan('github:retire-legacy-oauth')
            ->expectsConfirmation('Clear 1 legacy token(s)? GitHub IDs and usernames are kept, so sign-in and repo invites keep working.', 'no')
            ->expectsOutputToContain('Nothing changed.')
            ->assertSuccessful();

        $this->assertNotNull($legacy->fresh()->github_token);
    }
}
