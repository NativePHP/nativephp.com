<?php

namespace Tests\Feature\Api;

use App\Enums\PluginStatus;
use App\Enums\PluginType;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_401_without_credentials(): void
    {
        $response = $this->getJson('/api/plugins/access');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Authentication required']);
    }

    public function test_returns_401_with_invalid_credentials(): void
    {
        $response = $this->asBasicAuth('invalid@example.com', 'invalid-key')
            ->getJson('/api/plugins/access');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid credentials']);
    }

    public function test_returns_accessible_plugins_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'plugin_license_key' => 'test-license-key-123',
        ]);

        // Create a free plugin
        $freePlugin = Plugin::factory()->create([
            'name' => 'vendor/free-plugin',
            'type' => PluginType::Free,
            'status' => PluginStatus::Approved,
            'is_active' => true,
        ]);

        // Create a paid plugin
        $paidPlugin = Plugin::factory()->create([
            'name' => 'vendor/paid-plugin',
            'type' => PluginType::Paid,
            'status' => PluginStatus::Approved,
            'is_active' => true,
        ]);

        // Give user a license for the paid plugin
        PluginLicense::factory()->create([
            'user_id' => $user->id,
            'plugin_id' => $paidPlugin->id,
            'expires_at' => null, // Never expires
        ]);

        $response = $this->asBasicAuth($user->email, 'test-license-key-123')
            ->getJson('/api/plugins/access');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => ['email' => $user->email],
            ])
            ->assertJsonCount(2, 'plugins');

        $plugins = $response->json('plugins');
        $pluginNames = array_column($plugins, 'name');

        $this->assertContains('vendor/free-plugin', $pluginNames);
        $this->assertContains('vendor/paid-plugin', $pluginNames);
    }

    public function test_excludes_expired_plugin_licenses(): void
    {
        $user = User::factory()->create([
            'plugin_license_key' => 'test-license-key-123',
        ]);

        $paidPlugin = Plugin::factory()->create([
            'name' => 'vendor/paid-plugin',
            'type' => PluginType::Paid,
            'status' => PluginStatus::Approved,
            'is_active' => true,
        ]);

        // Create an expired license
        PluginLicense::factory()->create([
            'user_id' => $user->id,
            'plugin_id' => $paidPlugin->id,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->asBasicAuth($user->email, 'test-license-key-123')
            ->getJson('/api/plugins/access');

        $response->assertStatus(200);

        $plugins = $response->json('plugins');
        $pluginNames = array_column($plugins, 'name');

        $this->assertNotContains('vendor/paid-plugin', $pluginNames);
    }

    public function test_check_access_returns_true_for_licensed_plugin(): void
    {
        $user = User::factory()->create([
            'plugin_license_key' => 'test-license-key-123',
        ]);

        $paidPlugin = Plugin::factory()->create([
            'name' => 'vendor/paid-plugin',
            'type' => PluginType::Paid,
            'status' => PluginStatus::Approved,
            'is_active' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $user->id,
            'plugin_id' => $paidPlugin->id,
        ]);

        $response = $this->asBasicAuth($user->email, 'test-license-key-123')
            ->getJson('/api/plugins/access/vendor/paid-plugin');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'package' => 'vendor/paid-plugin',
                'has_access' => true,
            ]);
    }

    public function test_check_access_returns_false_for_unlicensed_plugin(): void
    {
        $user = User::factory()->create([
            'plugin_license_key' => 'test-license-key-123',
        ]);

        Plugin::factory()->create([
            'name' => 'vendor/paid-plugin',
            'type' => PluginType::Paid,
            'status' => PluginStatus::Approved,
            'is_active' => true,
        ]);

        $response = $this->asBasicAuth($user->email, 'test-license-key-123')
            ->getJson('/api/plugins/access/vendor/paid-plugin');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'package' => 'vendor/paid-plugin',
                'has_access' => false,
            ]);
    }

    public function test_check_access_returns_true_for_free_plugin(): void
    {
        $user = User::factory()->create([
            'plugin_license_key' => 'test-license-key-123',
        ]);

        Plugin::factory()->create([
            'name' => 'vendor/free-plugin',
            'type' => PluginType::Free,
            'status' => PluginStatus::Approved,
            'is_active' => true,
        ]);

        $response = $this->asBasicAuth($user->email, 'test-license-key-123')
            ->getJson('/api/plugins/access/vendor/free-plugin');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'package' => 'vendor/free-plugin',
                'has_access' => true,
            ]);
    }

    public function test_check_access_returns_404_for_nonexistent_plugin(): void
    {
        $user = User::factory()->create([
            'plugin_license_key' => 'test-license-key-123',
        ]);

        $response = $this->asBasicAuth($user->email, 'test-license-key-123')
            ->getJson('/api/plugins/access/vendor/nonexistent');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Plugin not found']);
    }

    protected function asBasicAuth(string $username, string $password): static
    {
        return $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode("{$username}:{$password}"),
        ]);
    }
}
