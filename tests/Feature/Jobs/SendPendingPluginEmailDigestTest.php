<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendPendingPluginEmailDigest;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginsAvailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendPendingPluginEmailDigestTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_one_digest_covering_all_pending_plugins(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $optedIn = User::factory()->create(['receives_new_plugin_notifications' => true]);

        $pluginOne = Plugin::factory()->approved()->for($author)->create();
        $pluginTwo = Plugin::factory()->approved()->for($author)->create();

        (new SendPendingPluginEmailDigest)->handle();

        Notification::assertSentTo($optedIn, NewPluginsAvailable::class, function ($notification) use ($pluginOne, $pluginTwo) {
            return $notification->plugins->pluck('id')->sort()->values()->all()
                === collect([$pluginOne->id, $pluginTwo->id])->sort()->values()->all();
        });
    }

    public function test_job_does_not_notify_opted_out_users(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $optedOut = User::factory()->create(['receives_new_plugin_notifications' => false]);
        Plugin::factory()->approved()->for($author)->create();

        (new SendPendingPluginEmailDigest)->handle();

        Notification::assertNotSentTo($optedOut, NewPluginsAvailable::class);
    }

    public function test_job_does_not_notify_unverified_users(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $unverified = User::factory()->unverified()->create(['receives_new_plugin_notifications' => true]);
        Plugin::factory()->approved()->for($author)->create();

        (new SendPendingPluginEmailDigest)->handle();

        Notification::assertNotSentTo($unverified, NewPluginsAvailable::class);
    }

    public function test_job_excludes_a_recipients_own_plugins_from_their_digest(): void
    {
        Notification::fake();

        $author = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $otherAuthor = User::factory()->create();

        $ownPlugin = Plugin::factory()->approved()->for($author)->create();
        $otherPlugin = Plugin::factory()->approved()->for($otherAuthor)->create();

        (new SendPendingPluginEmailDigest)->handle();

        Notification::assertSentTo($author, NewPluginsAvailable::class, function ($notification) use ($ownPlugin, $otherPlugin) {
            return ! $notification->plugins->contains('id', $ownPlugin->id)
                && $notification->plugins->contains('id', $otherPlugin->id);
        });
    }

    public function test_job_skips_a_recipient_entirely_when_all_pending_plugins_are_their_own(): void
    {
        Notification::fake();

        $author = User::factory()->create(['receives_new_plugin_notifications' => true]);
        Plugin::factory()->approved()->for($author)->create();

        (new SendPendingPluginEmailDigest)->handle();

        Notification::assertNotSentTo($author, NewPluginsAvailable::class);
    }

    public function test_job_marks_pending_plugins_as_notified(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $plugin = Plugin::factory()->approved()->for($author)->create();

        $this->assertNull($plugin->new_plugin_notified_at);

        (new SendPendingPluginEmailDigest)->handle();

        $this->assertNotNull($plugin->fresh()->new_plugin_notified_at);
    }

    public function test_job_ignores_plugins_already_notified(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $recipient = User::factory()->create(['receives_new_plugin_notifications' => true]);

        Plugin::factory()->approved()->for($author)->create([
            'new_plugin_notified_at' => now()->subDay(),
        ]);

        (new SendPendingPluginEmailDigest)->handle();

        Notification::assertNotSentTo($recipient, NewPluginsAvailable::class);
    }

    public function test_job_does_nothing_when_no_plugins_are_pending(): void
    {
        Notification::fake();

        User::factory()->create(['receives_new_plugin_notifications' => true]);

        (new SendPendingPluginEmailDigest)->handle();

        Notification::assertNothingSent();
    }
}
