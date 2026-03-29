<?php

namespace Tests\Feature\Livewire\Customer;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Plugins\Create;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

    private function fakeComposerJson(string $owner, string $repo, string $packageName): void
    {
        $composerJson = base64_encode(json_encode(['name' => $packageName]));

        Http::fake([
            "api.github.com/repos/{$owner}/{$repo}/contents/composer.json*" => Http::response([
                'content' => $composerJson,
            ]),
            'api.github.com/*' => Http::response([], 404),
        ]);
    }

    // ========================================
    // Owner/Repository Selection Tests
    // ========================================

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

    // ========================================
    // Namespace Validation Tests
    // ========================================

    public function test_submission_blocked_when_namespace_claimed_by_another_user(): void
    {
        $existingUser = User::factory()->create();
        Plugin::factory()->for($existingUser)->create(['name' => 'acme/existing-plugin']);

        $user = $this->createGitHubUser();

        $this->fakeComposerJson('acme', 'new-plugin', 'acme/new-plugin');

        Livewire::actingAs($user)->test(Create::class)
            ->set('repository', 'acme/new-plugin')
            ->set('pluginType', 'free')
            ->call('submitPlugin')
            ->assertNoRedirect();

        $this->assertDatabaseMissing('plugins', [
            'repository_url' => 'https://github.com/acme/new-plugin',
        ]);
    }

    public function test_submission_blocked_for_reserved_namespace(): void
    {
        $user = $this->createGitHubUser();

        $this->fakeComposerJson('nativephp', 'my-plugin', 'nativephp/my-plugin');

        Livewire::actingAs($user)->test(Create::class)
            ->set('repository', 'nativephp/my-plugin')
            ->set('pluginType', 'free')
            ->call('submitPlugin')
            ->assertNoRedirect();

        $this->assertDatabaseMissing('plugins', [
            'repository_url' => 'https://github.com/nativephp/my-plugin',
        ]);
    }

    public function test_submission_allowed_for_own_namespace(): void
    {
        $user = $this->createGitHubUser();
        Plugin::factory()->for($user)->create(['name' => 'myvendor/first-plugin']);

        $composerJson = base64_encode(json_encode(['name' => 'myvendor/second-plugin']));

        Http::fake([
            'api.github.com/repos/myvendor/second-plugin/contents/composer.json*' => Http::response([
                'content' => $composerJson,
            ]),
            'api.github.com/repos/myvendor/second-plugin/hooks' => Http::response(['id' => 1]),
            'api.github.com/*' => Http::response([], 404),
        ]);

        Livewire::actingAs($user)->test(Create::class)
            ->set('repository', 'myvendor/second-plugin')
            ->set('pluginType', 'free')
            ->set('supportChannel', 'support@myvendor.io')
            ->call('submitPlugin');

        $this->assertDatabaseHas('plugins', [
            'repository_url' => 'https://github.com/myvendor/second-plugin',
            'user_id' => $user->id,
        ]);
    }

    public function test_submission_blocked_when_composer_json_missing(): void
    {
        $user = $this->createGitHubUser();

        Http::fake([
            'api.github.com/*' => Http::response([], 404),
        ]);

        Livewire::actingAs($user)->test(Create::class)
            ->set('repository', 'testuser/no-composer')
            ->set('pluginType', 'free')
            ->call('submitPlugin')
            ->assertNoRedirect();

        $this->assertDatabaseMissing('plugins', [
            'repository_url' => 'https://github.com/testuser/no-composer',
        ]);
    }
}
