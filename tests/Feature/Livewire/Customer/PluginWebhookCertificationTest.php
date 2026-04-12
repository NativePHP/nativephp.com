<?php

namespace Tests\Feature\Livewire\Customer;

use App\Livewire\Customer\Plugins\Show;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class PluginWebhookCertificationTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'github_id' => '12345',
            'github_username' => 'testuser',
            'github_token' => encrypt('fake-token'),
        ]);
    }

    private function mountShowComponent(User $user, Plugin $plugin)
    {
        [$vendor, $package] = explode('/', $plugin->name);

        return Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ]);
    }

    public function test_certify_webhook_marks_plugin_as_webhook_installed(): void
    {
        $user = $this->createUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/certify-plugin',
            'webhook_secret' => bin2hex(random_bytes(32)),
            'webhook_installed' => false,
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('certifyWebhook');

        $plugin->refresh();
        $this->assertTrue($plugin->webhook_installed);
    }

    public function test_certify_webhook_generates_secret_if_missing(): void
    {
        $user = $this->createUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/no-secret-plugin',
            'webhook_secret' => null,
            'webhook_installed' => false,
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('certifyWebhook');

        $plugin->refresh();
        $this->assertTrue($plugin->webhook_installed);
        $this->assertNotNull($plugin->webhook_secret);
    }

    public function test_certify_webhook_is_idempotent(): void
    {
        $user = $this->createUser();
        $secret = bin2hex(random_bytes(32));
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/already-installed',
            'webhook_secret' => $secret,
            'webhook_installed' => true,
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('certifyWebhook');

        $plugin->refresh();
        $this->assertTrue($plugin->webhook_installed);
        $this->assertEquals($secret, $plugin->webhook_secret);
    }

    public function test_certify_webhook_makes_plugin_pass_webhook_check(): void
    {
        $user = $this->createUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/check-plugin',
            'webhook_secret' => bin2hex(random_bytes(32)),
            'webhook_installed' => false,
            'review_checks' => [
                'has_license_file' => true,
                'has_release_version' => true,
            ],
        ]);

        $this->assertFalse($plugin->passesRequiredReviewChecks());

        $this->mountShowComponent($user, $plugin)
            ->call('certifyWebhook');

        $plugin->refresh();
        $this->assertTrue($plugin->passesRequiredReviewChecks());
    }

    public function test_retry_webhook_installs_successfully(): void
    {
        $user = $this->createUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/retry-plugin',
            'repository_url' => 'https://github.com/testuser/retry-plugin',
            'webhook_secret' => bin2hex(random_bytes(32)),
            'webhook_installed' => false,
        ]);

        Http::fake([
            'https://api.github.com/repos/testuser/retry-plugin/hooks' => Http::response(['id' => 1], 201),
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('retryWebhook');

        $plugin->refresh();
        $this->assertTrue($plugin->webhook_installed);
    }

    public function test_retry_webhook_handles_failure(): void
    {
        $user = $this->createUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/retry-fail-plugin',
            'repository_url' => 'https://github.com/testuser/retry-fail-plugin',
            'webhook_secret' => bin2hex(random_bytes(32)),
            'webhook_installed' => false,
        ]);

        Http::fake([
            'https://api.github.com/repos/testuser/retry-fail-plugin/hooks' => Http::response(['message' => 'Not Found'], 404),
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('retryWebhook');

        $plugin->refresh();
        $this->assertFalse($plugin->webhook_installed);
    }

    public function test_retry_webhook_without_github_token_shows_error(): void
    {
        $user = User::factory()->create([
            'github_id' => null,
            'github_token' => null,
        ]);
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/no-token-plugin',
            'repository_url' => 'https://github.com/testuser/no-token-plugin',
            'webhook_secret' => bin2hex(random_bytes(32)),
            'webhook_installed' => false,
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('retryWebhook');

        $plugin->refresh();
        $this->assertFalse($plugin->webhook_installed);
    }
}
