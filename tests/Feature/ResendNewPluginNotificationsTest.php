<?php

namespace Tests\Feature;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginAvailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendNewPluginNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_notifications_to_opted_in_users(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $optedIn = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $optedOut = User::factory()->create(['receives_new_plugin_notifications' => false]);

        $plugin = Plugin::factory()->approved()->for($author)->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin->name],
        ])->assertSuccessful();

        Notification::assertSentTo($optedIn, NewPluginAvailable::class);
        Notification::assertNotSentTo($optedOut, NewPluginAvailable::class);
    }

    public function test_does_not_send_to_plugin_author(): void
    {
        Notification::fake();

        $author = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin = Plugin::factory()->approved()->for($author)->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin->name],
        ])->assertSuccessful();

        Notification::assertNotSentTo($author, NewPluginAvailable::class);
    }

    public function test_fails_when_plugin_not_found(): void
    {
        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => ['nonexistent/plugin'],
        ])->assertFailed();
    }

    public function test_fails_when_plugin_is_not_approved(): void
    {
        $plugin = Plugin::factory()->pending()->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin->name],
        ])->assertFailed();
    }

    public function test_dry_run_does_not_send_notifications(): void
    {
        Notification::fake();

        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin = Plugin::factory()->approved()->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin->name],
            '--dry-run' => true,
        ])->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_handles_multiple_plugins(): void
    {
        Notification::fake();

        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin1 = Plugin::factory()->approved()->create();
        $plugin2 = Plugin::factory()->approved()->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin1->name, $plugin2->name],
        ])->assertSuccessful();

        Notification::assertSentTo($user, NewPluginAvailable::class, 2);
    }

    public function test_succeeds_with_no_opted_in_users(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->approved()->create();
        User::query()->update(['receives_new_plugin_notifications' => false]);

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin->name],
        ])->assertSuccessful();

        Notification::assertNothingSent();
    }
}
