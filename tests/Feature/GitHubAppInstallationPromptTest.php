<?php

namespace Tests\Feature;

use App\Enums\GitHubAuthType;
use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\GitHubAppStatus;
use App\Models\GitHubInstallation;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class GitHubAppInstallationPromptTest extends TestCase
{
    use RefreshDatabase;

    private const INSTALL_URL = 'https://github.com/apps/nativephp-test/installations/new';

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);

        config(['services.github_app.slug' => 'nativephp-test']);

        Http::fake(['api.github.com/*' => Http::response([], 404)]);
    }

    private function fakeGitHubAppLogin(User $user): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->id = $user->github_id;
        $socialiteUser->nickname = $user->github_username;
        $socialiteUser->name = $user->name;
        $socialiteUser->email = $user->email;
        $socialiteUser->token = 'ghu_new_token';

        $provider = Mockery::mock(GithubProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github-app')->andReturn($provider);

        session([
            'github_auth_intent' => 'login',
            'github_auth_driver' => 'github-app',
        ]);
    }

    private function pluginFor(User $user, string $repository): Plugin
    {
        return Plugin::factory()->create([
            'user_id' => $user->id,
            'repository_url' => "https://github.com/{$repository}",
        ]);
    }

    public function test_plugins_missing_access_lists_repos_without_a_covering_installation(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $covered = $this->pluginFor($user, 'acme/covered-plugin');
        $uncovered = $this->pluginFor($user, 'other-org/uncovered-plugin');

        GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'acme',
            'selection_type' => 'all',
        ]);

        $missing = $user->pluginsMissingGitHubAppAccess();

        $this->assertTrue($missing->contains($uncovered));
        $this->assertFalse($missing->contains($covered));
    }

    public function test_plugins_missing_access_respects_selected_repositories(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $selected = $this->pluginFor($user, 'acme/selected-plugin');
        $notSelected = $this->pluginFor($user, 'acme/not-selected-plugin');

        GitHubInstallation::factory()->selectedRepos(['acme/selected-plugin'])->create([
            'user_id' => $user->id,
            'account_login' => 'acme',
        ]);

        $missing = $user->pluginsMissingGitHubAppAccess();

        $this->assertFalse($missing->contains($selected));
        $this->assertTrue($missing->contains($notSelected));
    }

    public function test_suspended_installations_do_not_count_as_access(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $plugin = $this->pluginFor($user, 'acme/some-plugin');

        GitHubInstallation::factory()->suspended()->create([
            'user_id' => $user->id,
            'account_login' => 'acme',
        ]);

        $this->assertTrue($user->pluginsMissingGitHubAppAccess()->contains($plugin));
        $this->assertTrue($user->needsGitHubAppInstallation());
    }

    public function test_plugins_missing_access_is_empty_for_legacy_oauth_users(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();
        $this->pluginFor($user, 'acme/some-plugin');

        $this->assertTrue($user->pluginsMissingGitHubAppAccess()->isEmpty());
        $this->assertFalse($user->needsGitHubAppInstallation());
    }

    public function test_legacy_plugin_author_logging_in_with_github_app_is_sent_to_install_the_app(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();
        $this->pluginFor($user, 'acme/some-plugin');

        $this->fakeGitHubAppLogin($user);

        $response = $this->get('/auth/github/callback');

        $response->assertRedirect(self::INSTALL_URL);
        $response->assertSessionHas('github_return_url', route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertEquals(GitHubAuthType::App, $user->fresh()->github_auth_type);
    }

    public function test_install_redirect_preserves_the_intended_url(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();
        $this->pluginFor($user, 'acme/some-plugin');

        $this->fakeGitHubAppLogin($user);
        session(['url.intended' => route('customer.plugins.index')]);

        $response = $this->get('/auth/github/callback');

        $response->assertRedirect(self::INSTALL_URL);
        $response->assertSessionHas('github_return_url', route('customer.plugins.index'));
    }

    public function test_user_without_plugins_logging_in_with_github_app_goes_to_the_dashboard(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();

        $this->fakeGitHubAppLogin($user);

        $this->get('/auth/github/callback')->assertRedirect(route('dashboard'));
    }

    public function test_plugin_author_with_covered_repos_logging_in_goes_to_the_dashboard(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $this->pluginFor($user, 'acme/some-plugin');

        GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'acme',
            'selection_type' => 'all',
        ]);

        $this->fakeGitHubAppLogin($user);

        $this->get('/auth/github/callback')->assertRedirect(route('dashboard'));
    }

    public function test_github_app_user_sees_which_plugin_repos_need_access(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $this->pluginFor($user, 'acme/some-plugin');

        $response = $this->actingAs($user)->get(route('customer.integrations'));

        $response->assertOk();
        $response->assertSee('GitHub App Needs Access to Your Plugins');
        $response->assertSee('acme/some-plugin');
        $response->assertSee(self::INSTALL_URL);
        $response->assertDontSee('GitHub Connection Upgrade Required');
    }

    public function test_github_app_user_with_covered_repos_sees_no_access_banner(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $this->pluginFor($user, 'acme/some-plugin');

        GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'account_login' => 'acme',
            'selection_type' => 'all',
        ]);

        $response = $this->actingAs($user)->get(route('customer.plugins.index'));

        $response->assertOk();
        $response->assertDontSee('GitHub App Needs Access to Your Plugins');
    }

    public function test_legacy_upgrade_banner_names_the_repos_to_grant(): void
    {
        $user = User::factory()->withLegacyGitHub()->create();
        $this->pluginFor($user, 'acme/paid-plugin');

        $response = $this->actingAs($user)->get(route('customer.integrations'));

        $response->assertOk();
        $response->assertSee('GitHub Connection Upgrade Required');
        $response->assertSee('acme/paid-plugin');
    }

    public function test_create_page_prompts_github_app_user_without_installation_to_install(): void
    {
        $user = User::factory()->withGitHubApp()->create();

        $response = $this->actingAs($user)->get(route('customer.plugins.create'));

        $response->assertOk();
        $response->assertSee('Install the GitHub App');
        $response->assertSee(self::INSTALL_URL);
    }

    public function test_create_page_shows_form_once_the_app_is_installed(): void
    {
        $user = User::factory()->withGitHubApp()->create();

        GitHubInstallation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('customer.plugins.create'));

        $response->assertOk();
        $response->assertDontSee('Install the GitHub App');
        $response->assertSee('Select Repository');
    }

    public function test_setup_callback_clears_the_cached_repository_list(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->create(['user_id' => $user->id]);

        Cache::put("github_repos_{$user->id}", collect(), now()->addMinutes(5));

        $this->actingAs($user)
            ->get(route('github.setup', ['installation_id' => $installation->installation_id]))
            ->assertRedirect(route('customer.integrations'));

        $this->assertFalse(Cache::has("github_repos_{$user->id}"));
    }

    public function test_integrations_page_shows_installations_and_plugin_repo_coverage(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $this->pluginFor($user, 'acme/covered-plugin');
        $this->pluginFor($user, 'other-org/uncovered-plugin');

        $installation = GitHubInstallation::factory()->forOrganization()->create([
            'user_id' => $user->id,
            'account_login' => 'acme',
            'selection_type' => 'all',
        ]);

        Livewire::actingAs($user)
            ->test(GitHubAppStatus::class)
            ->assertSee('acme')
            ->assertSee('All repositories')
            ->assertSee("https://github.com/settings/installations/{$installation->installation_id}")
            ->assertSee('acme/covered-plugin')
            ->assertSee('other-org/uncovered-plugin')
            ->assertSeeInOrder(['other-org/uncovered-plugin', 'Not accessible']);
    }

    public function test_integrations_page_prompts_to_install_when_there_are_no_installations(): void
    {
        $user = User::factory()->withGitHubApp()->create();

        Livewire::actingAs($user)
            ->test(GitHubAppStatus::class)
            ->assertSee('No GitHub App installations found')
            ->assertSee(self::INSTALL_URL);
    }

    public function test_disconnect_modal_explains_the_app_stays_installed(): void
    {
        $user = User::factory()->withGitHubApp()->create();

        $this->actingAs($user)
            ->get(route('customer.integrations'))
            ->assertSee('give the NativePHP GitHub App access to the repositories you need')
            ->assertSee('stays installed on your GitHub accounts');
    }
}
