<?php

namespace Tests\Unit;

use App\Models\GitHubInstallation;
use App\Models\User;
use App\Services\GitHubAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubAppServiceTest extends TestCase
{
    use RefreshDatabase;

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
