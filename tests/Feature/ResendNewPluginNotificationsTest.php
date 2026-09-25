<?php

namespace Tests\Feature;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginsAvailable;
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

        Notification::assertSentTo($optedIn, NewPluginsAvailable::class);
        Notification::assertNotSentTo($optedOut, NewPluginsAvailable::class);
    }

    public function test_does_not_send_to_plugin_author(): void
    {
        Notification::fake();

        $author = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin = Plugin::factory()->approved()->for($author)->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin->name],
        ])->assertSuccessful();

        Notification::assertNotSentTo($author, NewPluginsAvailable::class);
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

    public function test_handles_multiple_plugins_as_a_single_combined_digest(): void
    {
        Notification::fake();

        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin1 = Plugin::factory()->approved()->create();
        $plugin2 = Plugin::factory()->approved()->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin1->name, $plugin2->name],
        ])->assertSuccessful();

        Notification::assertSentTo($user, NewPluginsAvailable::class, 1);
        Notification::assertSentTo($user, NewPluginsAvailable::class, function ($notification) use ($plugin1, $plugin2) {
            return $notification->plugins->pluck('id')->sort()->values()->all()
                === collect([$plugin1->id, $plugin2->id])->sort()->values()->all();
        });
    }

    public function test_excludes_all_specified_plugin_authors_from_the_combined_digest(): void
    {
        Notification::fake();

        $authorOne = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $authorTwo = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin1 = Plugin::factory()->approved()->for($authorOne)->create();
        $plugin2 = Plugin::factory()->approved()->for($authorTwo)->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin1->name, $plugin2->name],
        ])->assertSuccessful();

        Notification::assertNotSentTo($authorOne, NewPluginsAvailable::class);
        Notification::assertNotSentTo($authorTwo, NewPluginsAvailable::class);
    }

    public function test_marks_sent_plugins_as_notified(): void
    {
        Notification::fake();

        User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin = Plugin::factory()->approved()->create();

        $this->artisan('plugins:resend-new-plugin-notifications', [
            'plugins' => [$plugin->name],
        ])->assertSuccessful();

        $this->assertNotNull($plugin->fresh()->new_plugin_notified_at);
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
