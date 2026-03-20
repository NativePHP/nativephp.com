<?php

namespace Tests\Feature;

use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\User;
use App\Notifications\BundlePluginAdded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GrantPluginToBundleOwnersTest extends TestCase
{
    use RefreshDatabase;

    public function test_grants_plugin_to_all_bundle_owners(): void
    {
        Notification::fake();

        $bundle = PluginBundle::factory()->active()->create();
        $existingPlugin = Plugin::factory()->paid()->create();
        $newPlugin = Plugin::factory()->paid()->create();

        $bundle->plugins()->attach($existingPlugin);

        // Create 3 users who purchased the bundle
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            PluginLicense::factory()->create([
                'user_id' => $user->id,
                'plugin_id' => $existingPlugin->id,
                'plugin_bundle_id' => $bundle->id,
            ]);
        }

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => $bundle->slug,
            'plugin' => $newPlugin->name,
        ])->assertSuccessful();

        foreach ($users as $user) {
            $this->assertDatabaseHas('plugin_licenses', [
                'user_id' => $user->id,
                'plugin_id' => $newPlugin->id,
                'plugin_bundle_id' => $bundle->id,
                'price_paid' => 0,
            ]);
        }

        Notification::assertSentTo($users, BundlePluginAdded::class);
    }

    public function test_skips_users_who_already_have_the_plugin(): void
    {
        Notification::fake();

        $bundle = PluginBundle::factory()->active()->create();
        $existingPlugin = Plugin::factory()->paid()->create();
        $newPlugin = Plugin::factory()->paid()->create();

        $bundle->plugins()->attach($existingPlugin);

        $userWithLicense = User::factory()->create();
        $userWithoutLicense = User::factory()->create();

        // Both users purchased the bundle
        foreach ([$userWithLicense, $userWithoutLicense] as $user) {
            PluginLicense::factory()->create([
                'user_id' => $user->id,
                'plugin_id' => $existingPlugin->id,
                'plugin_bundle_id' => $bundle->id,
            ]);
        }

        // One user already has the new plugin
        PluginLicense::factory()->create([
            'user_id' => $userWithLicense->id,
            'plugin_id' => $newPlugin->id,
        ]);

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => $bundle->slug,
            'plugin' => $newPlugin->name,
        ])->assertSuccessful();

        // Should only create one new license (for the user without)
        $this->assertDatabaseCount('plugin_licenses', 4); // 2 bundle + 1 existing + 1 new

        Notification::assertSentTo($userWithoutLicense, BundlePluginAdded::class);
        Notification::assertNotSentTo($userWithLicense, BundlePluginAdded::class);
    }

    public function test_dry_run_does_not_create_licenses_or_send_emails(): void
    {
        Notification::fake();

        $bundle = PluginBundle::factory()->active()->create();
        $existingPlugin = Plugin::factory()->paid()->create();
        $newPlugin = Plugin::factory()->paid()->create();

        $bundle->plugins()->attach($existingPlugin);

        $user = User::factory()->create();

        PluginLicense::factory()->create([
            'user_id' => $user->id,
            'plugin_id' => $existingPlugin->id,
            'plugin_bundle_id' => $bundle->id,
        ]);

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => $bundle->slug,
            'plugin' => $newPlugin->name,
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('plugin_licenses', [
            'user_id' => $user->id,
            'plugin_id' => $newPlugin->id,
        ]);

        Notification::assertNothingSent();
    }

    public function test_no_email_option_grants_without_sending_notification(): void
    {
        Notification::fake();

        $bundle = PluginBundle::factory()->active()->create();
        $existingPlugin = Plugin::factory()->paid()->create();
        $newPlugin = Plugin::factory()->paid()->create();

        $bundle->plugins()->attach($existingPlugin);

        $user = User::factory()->create();

        PluginLicense::factory()->create([
            'user_id' => $user->id,
            'plugin_id' => $existingPlugin->id,
            'plugin_bundle_id' => $bundle->id,
        ]);

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => $bundle->slug,
            'plugin' => $newPlugin->name,
            '--no-email' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('plugin_licenses', [
            'user_id' => $user->id,
            'plugin_id' => $newPlugin->id,
            'plugin_bundle_id' => $bundle->id,
        ]);

        Notification::assertNothingSent();
    }

    public function test_fails_with_invalid_bundle_slug(): void
    {
        $plugin = Plugin::factory()->create();

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => 'nonexistent-bundle',
            'plugin' => $plugin->name,
        ])->assertFailed();
    }

    public function test_fails_with_invalid_plugin_name(): void
    {
        $bundle = PluginBundle::factory()->active()->create();

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => $bundle->slug,
            'plugin' => 'nonexistent/plugin',
        ])->assertFailed();
    }

    public function test_handles_bundle_with_no_purchasers(): void
    {
        $bundle = PluginBundle::factory()->active()->create();
        $plugin = Plugin::factory()->paid()->create();

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => $bundle->slug,
            'plugin' => $plugin->name,
        ])->assertSuccessful()
            ->expectsOutput('No users found who have purchased this bundle.');
    }

    public function test_skips_users_with_expired_bundle_licenses(): void
    {
        Notification::fake();

        $bundle = PluginBundle::factory()->active()->create();
        $existingPlugin = Plugin::factory()->paid()->create();
        $newPlugin = Plugin::factory()->paid()->create();

        $bundle->plugins()->attach($existingPlugin);

        $activeUser = User::factory()->create();
        $expiredUser = User::factory()->create();

        // Active license
        PluginLicense::factory()->create([
            'user_id' => $activeUser->id,
            'plugin_id' => $existingPlugin->id,
            'plugin_bundle_id' => $bundle->id,
        ]);

        // Expired license
        PluginLicense::factory()->expired()->create([
            'user_id' => $expiredUser->id,
            'plugin_id' => $existingPlugin->id,
            'plugin_bundle_id' => $bundle->id,
        ]);

        $this->artisan('plugins:grant-to-bundle-owners', [
            'bundle' => $bundle->slug,
            'plugin' => $newPlugin->name,
        ])->assertSuccessful();

        $this->assertDatabaseHas('plugin_licenses', [
            'user_id' => $activeUser->id,
            'plugin_id' => $newPlugin->id,
        ]);

        $this->assertDatabaseMissing('plugin_licenses', [
            'user_id' => $expiredUser->id,
            'plugin_id' => $newPlugin->id,
        ]);

        Notification::assertSentTo($activeUser, BundlePluginAdded::class);
        Notification::assertNotSentTo($expiredUser, BundlePluginAdded::class);
    }
}
