<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\GitHubInstallation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Tests\Concerns\InteractsWithGitHubApp;
use Tests\TestCase;

class GitHubAppSetupTest extends TestCase
{
    use InteractsWithGitHubApp;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);

        $this->configureGitHubApp();
    }

    /**
     * @param  array<int, int>  $userInstallationIds
     * @param  array<int, string>  $repositories
     */
    private function fakeGitHub(array $userInstallationIds, string $selection = 'selected', array $repositories = []): void
    {
        Http::fake([
            'api.github.com/user/installations*' => Http::response([
                'installations' => array_map(fn (int $id) => ['id' => $id], $userInstallationIds),
            ]),
            'api.github.com/app/installations/555/access_tokens' => Http::response([
                'token' => 'ghs_installation_token',
                'expires_at' => now()->addHour()->toIso8601String(),
            ]),
            'api.github.com/app/installations/555' => Http::response([
                'id' => 555,
                'account' => ['login' => 'acme', 'type' => 'Organization', 'id' => 99],
                'repository_selection' => $selection,
                'suspended_at' => null,
            ]),
            'api.github.com/installation/repositories*' => Http::response([
                'repositories' => array_map(fn (string $name) => ['full_name' => $name], $repositories),
            ]),
        ]);
    }

    public function test_setup_records_an_installation_the_user_can_see_on_github(): void
    {
        $this->fakeGitHub([555], 'selected', ['acme/one', 'acme/two']);

        $user = User::factory()->withGitHubApp()->create();

        $this->actingAs($user)
            ->get(route('github.setup', ['installation_id' => 555]))
            ->assertRedirect(route('customer.integrations'))
            ->assertSessionHas('success');

        $installation = $user->githubInstallations()->sole();

        $this->assertSame(555, (int) $installation->installation_id);
        $this->assertSame('acme', $installation->account_login);
        $this->assertSame('Organization', $installation->account_type);
        $this->assertSame('selected', $installation->selection_type);
        $this->assertSame(['acme/one', 'acme/two'], $installation->repository_selection);
    }

    public function test_setup_does_not_store_a_repository_list_when_all_repos_are_selected(): void
    {
        $this->fakeGitHub([555], 'all');

        $user = User::factory()->withGitHubApp()->create();

        $this->actingAs($user)->get(route('github.setup', ['installation_id' => 555]));

        $installation = $user->githubInstallations()->sole();

        $this->assertSame('all', $installation->selection_type);
        $this->assertNull($installation->repository_selection);
    }

    public function test_setup_rejects_an_installation_the_user_cannot_see_on_github(): void
    {
        $this->fakeGitHub([111, 222]);

        $user = User::factory()->withGitHubApp()->create();

        $this->actingAs($user)
            ->get(route('github.setup', ['installation_id' => 555]))
            ->assertRedirect(route('customer.integrations'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('github_installations', 0);
    }

    public function test_setup_rejects_users_still_on_the_legacy_oauth_app(): void
    {
        $this->fakeGitHub([555]);

        $user = User::factory()->withLegacyGitHub()->create();

        $this->actingAs($user)
            ->get(route('github.setup', ['installation_id' => 555]))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('github_installations', 0);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/user/installations'));
    }

    public function test_setup_will_not_hand_over_an_installation_linked_to_someone_else(): void
    {
        $this->fakeGitHub([555]);

        $owner = User::factory()->withGitHubApp()->create();
        GitHubInstallation::factory()->create([
            'user_id' => $owner->id,
            'installation_id' => 555,
        ]);

        $otherUser = User::factory()->withGitHubApp()->create();

        $this->actingAs($otherUser)
            ->get(route('github.setup', ['installation_id' => 555]))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('github_installations', [
            'installation_id' => 555,
            'user_id' => $owner->id,
        ]);
    }

    public function test_setup_refreshes_an_installation_the_webhook_already_recorded(): void
    {
        $this->fakeGitHub([555], 'selected', ['acme/new-repo']);

        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->selectedRepos([])->create([
            'user_id' => $user->id,
            'installation_id' => 555,
            'account_login' => 'acme',
        ]);

        $this->actingAs($user)
            ->get(route('github.setup', ['installation_id' => 555]))
            ->assertSessionHas('success');

        $this->assertSame(['acme/new-repo'], $installation->fresh()->repository_selection);
    }

    public function test_sync_command_updates_installations_and_removes_ones_github_no_longer_has(): void
    {
        $this->fakeGitHub([], 'selected', ['acme/one']);
        Http::fake(['api.github.com/app/installations/777' => Http::response([], 404)]);

        $user = User::factory()->withGitHubApp()->create();
        $current = GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'installation_id' => 555,
            'selection_type' => 'all',
        ]);
        $removed = GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'installation_id' => 777,
        ]);

        $this->artisan('github:sync-installations')
            ->expectsOutputToContain('Synced: 1, removed: 1, failed: 0')
            ->assertSuccessful();

        $this->assertSame(['acme/one'], $current->fresh()->repository_selection);
        $this->assertModelMissing($removed);
    }
}
