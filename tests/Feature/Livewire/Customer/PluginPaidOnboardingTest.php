<?php

namespace Tests\Feature\Livewire\Customer;

use App\Enums\PluginType;
use App\Features\AllowPaidPlugins;
use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Plugins\Create;
use App\Livewire\Customer\Plugins\Show;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class PluginPaidOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
        Feature::define(AllowPaidPlugins::class, true);
    }

    private function createGitHubUser(): User
    {
        return User::factory()->create([
            'github_id' => '12345',
            'github_username' => 'testuser',
            'github_token' => encrypt('fake-token'),
        ]);
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
    // Create: Paid option disabled without onboarding
    // ========================================

    public function test_create_page_shows_paid_option_disabled_without_onboarding(): void
    {
        $user = $this->createGitHubUser();

        Livewire::actingAs($user)->test(Create::class)
            ->assertSee('complete developer onboarding')
            ->assertSeeHtml('disabled');
    }

    public function test_create_page_shows_paid_option_enabled_with_onboarding(): void
    {
        $user = $this->createGitHubUser();
        DeveloperAccount::factory()->for($user)->create();

        Livewire::actingAs($user)->test(Create::class)
            ->assertDontSee('complete developer onboarding');
    }

    public function test_create_paid_plugin_blocked_without_onboarding(): void
    {
        $user = $this->createGitHubUser();

        $this->fakeComposerJson('testuser', 'my-plugin', 'testuser/my-plugin');

        Livewire::actingAs($user)->test(Create::class)
            ->set('repository', 'testuser/my-plugin')
            ->set('pluginType', 'paid')
            ->call('createPlugin')
            ->assertNoRedirect();

        $this->assertDatabaseMissing('plugins', [
            'repository_url' => 'https://github.com/testuser/my-plugin',
        ]);
    }

    public function test_create_paid_plugin_allowed_with_onboarding(): void
    {
        $user = $this->createGitHubUser();
        DeveloperAccount::factory()->for($user)->create();

        $this->fakeComposerJson('testuser', 'paid-plugin', 'testuser/paid-plugin');

        Livewire::actingAs($user)->test(Create::class)
            ->set('repository', 'testuser/paid-plugin')
            ->set('pluginType', 'paid')
            ->call('createPlugin');

        $this->assertDatabaseHas('plugins', [
            'repository_url' => 'https://github.com/testuser/paid-plugin',
            'type' => 'paid',
            'status' => 'draft',
        ]);
    }

    public function test_create_free_plugin_allowed_without_onboarding(): void
    {
        $user = $this->createGitHubUser();

        $this->fakeComposerJson('testuser', 'free-plugin', 'testuser/free-plugin');

        Livewire::actingAs($user)->test(Create::class)
            ->set('repository', 'testuser/free-plugin')
            ->set('pluginType', 'free')
            ->call('createPlugin');

        $this->assertDatabaseHas('plugins', [
            'repository_url' => 'https://github.com/testuser/free-plugin',
            'type' => 'free',
            'status' => 'draft',
        ]);
    }

    // ========================================
    // Edit Draft: Paid option disabled without onboarding
    // ========================================

    public function test_edit_draft_shows_paid_option_disabled_without_onboarding(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/onboard-test',
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ])
            ->assertSee('complete developer onboarding')
            ->assertSeeHtml('disabled');
    }

    public function test_edit_draft_shows_paid_option_enabled_with_onboarding(): void
    {
        $user = $this->createGitHubUser();
        DeveloperAccount::factory()->for($user)->create();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/onboard-enabled',
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ])
            ->assertDontSee('complete developer onboarding');
    }

    public function test_save_draft_as_paid_blocked_without_onboarding(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/save-paid-test',
            'support_channel' => 'support@test.io',
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ])
            ->set('description', 'A test plugin')
            ->set('supportChannel', 'support@test.io')
            ->set('pluginType', 'paid')
            ->set('tier', 'gold')
            ->call('save');

        $plugin->refresh();
        $this->assertNotEquals(PluginType::Paid, $plugin->type);
    }

    public function test_save_draft_as_paid_allowed_with_onboarding(): void
    {
        $user = $this->createGitHubUser();
        DeveloperAccount::factory()->for($user)->create();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/save-paid-ok',
            'support_channel' => 'support@test.io',
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ])
            ->set('description', 'A test plugin')
            ->set('supportChannel', 'support@test.io')
            ->set('pluginType', 'paid')
            ->set('tier', 'gold')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals(PluginType::Paid, $plugin->type);
    }

    public function test_create_page_shows_onboarding_link_without_onboarding(): void
    {
        $user = $this->createGitHubUser();

        Livewire::actingAs($user)->test(Create::class)
            ->assertSeeHtml(route('customer.developer.onboarding'));
    }

    public function test_edit_draft_shows_onboarding_link_without_onboarding(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/link-test',
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ])
            ->assertSeeHtml(route('customer.developer.onboarding'));
    }
}
