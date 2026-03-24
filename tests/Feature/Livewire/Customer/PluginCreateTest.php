<?php

namespace Tests\Feature\Livewire\Customer;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Plugins\Create;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class PluginCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
    }

    private function createGitHubUser(): User
    {
        return User::factory()->create([
            'github_id' => '12345',
            'github_username' => 'testuser',
            'github_token' => encrypt('fake-token'),
        ]);
    }

    private function sampleRepos(): array
    {
        return [
            ['id' => 1, 'full_name' => 'testuser/alpha-plugin', 'name' => 'alpha-plugin', 'owner' => 'testuser', 'private' => false],
            ['id' => 2, 'full_name' => 'testuser/beta-plugin', 'name' => 'beta-plugin', 'owner' => 'testuser', 'private' => true],
            ['id' => 3, 'full_name' => 'my-org/org-repo', 'name' => 'org-repo', 'owner' => 'my-org', 'private' => false],
            ['id' => 4, 'full_name' => 'my-org/another-repo', 'name' => 'another-repo', 'owner' => 'my-org', 'private' => false],
        ];
    }

    public function test_owners_are_extracted_from_repositories(): void
    {
        $user = $this->createGitHubUser();

        $component = Livewire::actingAs($user)->test(Create::class)
            ->set('repositories', $this->sampleRepos())
            ->set('reposLoaded', true);

        $owners = $component->get('owners');

        $this->assertCount(2, $owners);
        $this->assertContains('testuser', $owners);
        $this->assertContains('my-org', $owners);
    }

    public function test_selecting_owner_filters_repositories(): void
    {
        $user = $this->createGitHubUser();

        $component = Livewire::actingAs($user)->test(Create::class)
            ->set('repositories', $this->sampleRepos())
            ->set('reposLoaded', true)
            ->set('selectedOwner', 'my-org');

        $ownerRepos = $component->get('ownerRepositories');

        $this->assertCount(2, $ownerRepos);
        $this->assertEquals('another-repo', $ownerRepos[0]['name']);
        $this->assertEquals('org-repo', $ownerRepos[1]['name']);
    }

    public function test_changing_owner_resets_repository_selection(): void
    {
        $user = $this->createGitHubUser();

        Livewire::actingAs($user)->test(Create::class)
            ->set('repositories', $this->sampleRepos())
            ->set('reposLoaded', true)
            ->set('selectedOwner', 'testuser')
            ->set('repository', 'testuser/alpha-plugin')
            ->set('selectedOwner', 'my-org')
            ->assertSet('repository', '');
    }

    public function test_owner_repositories_sorted_alphabetically(): void
    {
        $user = $this->createGitHubUser();

        $component = Livewire::actingAs($user)->test(Create::class)
            ->set('repositories', $this->sampleRepos())
            ->set('reposLoaded', true)
            ->set('selectedOwner', 'testuser');

        $ownerRepos = $component->get('ownerRepositories');

        $this->assertEquals('alpha-plugin', $ownerRepos[0]['name']);
        $this->assertEquals('beta-plugin', $ownerRepos[1]['name']);
    }

    public function test_no_owner_selected_returns_empty_repositories(): void
    {
        $user = $this->createGitHubUser();

        $component = Livewire::actingAs($user)->test(Create::class)
            ->set('repositories', $this->sampleRepos())
            ->set('reposLoaded', true);

        $ownerRepos = $component->get('ownerRepositories');

        $this->assertEmpty($ownerRepos);
    }
}
