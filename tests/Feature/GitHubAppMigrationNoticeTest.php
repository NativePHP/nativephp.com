<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\GitHubAppMigrationRequired;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class GitHubAppMigrationNoticeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);

        Http::fake(['api.github.com/*' => Http::response([], 404)]);
    }

    public function test_dry_run_does_not_send_anything(): void
    {
        Notification::fake();

        $user = User::factory()->withLegacyGitHub()->create();

        $this->artisan('github:send-app-migration-notice --dry-run')
            ->expectsOutputToContain("Would send to: {$user->email}")
            ->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertNull($user->fresh()->github_app_migration_notified_at);
    }

    public function test_notice_goes_to_verified_legacy_users_once(): void
    {
        Notification::fake();
        config(['services.github.legacy_oauth_cutoff_date' => '2026-12-31']);

        $legacy = User::factory()->withLegacyGitHub()->create();
        $unverified = User::factory()->withLegacyGitHub()->unverified()->create();
        $appUser = User::factory()->withGitHubApp()->create();
        $notConnected = User::factory()->create();

        $this->artisan('github:send-app-migration-notice')->assertSuccessful();
        $this->artisan('github:send-app-migration-notice')->assertSuccessful();

        Notification::assertSentToTimes($legacy, GitHubAppMigrationRequired::class, 1);
        Notification::assertNotSentTo([$unverified, $appUser, $notConnected], GitHubAppMigrationRequired::class);
        $this->assertNotNull($legacy->fresh()->github_app_migration_notified_at);
    }

    public function test_real_send_refuses_to_run_without_a_cutoff_date(): void
    {
        Notification::fake();

        User::factory()->withLegacyGitHub()->create();

        $this->artisan('github:send-app-migration-notice')
            ->expectsOutputToContain('GITHUB_LEGACY_OAUTH_CUTOFF_DATE')
            ->assertFailed();

        Notification::assertNothingSent();
    }

    public function test_preview_sends_a_single_copy_to_the_given_address(): void
    {
        Notification::fake();

        $legacy = User::factory()->withLegacyGitHub()->create();

        $this->artisan('github:send-app-migration-notice --preview=me@example.com')
            ->expectsOutputToContain('Sent a preview to me@example.com')
            ->assertSuccessful();

        Notification::assertSentOnDemand(
            GitHubAppMigrationRequired::class,
            fn ($notification, array $channels, $notifiable) => $notifiable->routes['mail'] === 'me@example.com'
        );
        Notification::assertNotSentTo($legacy, GitHubAppMigrationRequired::class);
        $this->assertNull($legacy->fresh()->github_app_migration_notified_at);
    }

    public function test_email_explains_both_cases_and_gives_the_deadline(): void
    {
        config(['services.github.legacy_oauth_cutoff_date' => '2026-12-31']);

        $user = User::factory()->withLegacyGitHub()->create(['name' => 'Priya Patel']);

        $html = (string) (new GitHubAppMigrationRequired)->toMail($user)->render();

        $this->assertStringContainsString('Hi Priya,', $html);
        $this->assertStringContainsString('How does this affect you?', $html);
        $this->assertStringContainsString('Login with GitHub', $html);
        $this->assertStringContainsString('need to do anything', $html);
        $this->assertStringContainsString('If you are a plugin author', $html);
        $this->assertStringContainsString('before 31 December 2026', $html);
        $this->assertStringContainsString('may remove them from the Marketplace', $html);
        $this->assertStringContainsString(route('customer.integrations'), $html);
        $this->assertStringContainsString('moving away from GitHub OAuth on nativephp.com', $html);
        $this->assertStringContainsString('href="https://bifrost.nativephp.com"', $html);
        $this->assertStringContainsString('30% off annual plans', $html);
        $this->assertLessThan(strpos($html, 'This does not affect'), strpos($html, 'Connect the GitHub App'));
    }

    public function test_email_falls_back_to_generic_wording_without_a_cutoff_date(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();

        $html = (string) (new GitHubAppMigrationRequired)->toMail($user)->render();

        $this->assertStringContainsString('before we switch off the old connection', $html);
    }

    public function test_admins_can_filter_for_plugin_authors_still_on_the_oauth_app(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $legacyAuthor = User::factory()->withLegacyGitHub()->create();
        Plugin::factory()->create(['user_id' => $legacyAuthor->id]);

        $legacyWithoutPlugins = User::factory()->withLegacyGitHub()->create();

        $appAuthor = User::factory()->withGitHubApp()->create();
        Plugin::factory()->create(['user_id' => $appAuthor->id]);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->filterTable('plugin_authors_on_legacy_oauth')
            ->assertCanSeeTableRecords([$legacyAuthor])
            ->assertCanNotSeeTableRecords([$legacyWithoutPlugins, $appAuthor]);
    }
}
