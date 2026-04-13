<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendNewPluginNotifications;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginAvailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendNewPluginNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_notification_to_opted_in_users(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $optedIn = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $optedOut = User::factory()->create(['receives_new_plugin_notifications' => false]);

        $plugin = Plugin::factory()->approved()->for($author)->create();

        (new SendNewPluginNotifications($plugin))->handle();

        Notification::assertSentTo($optedIn, NewPluginAvailable::class);
        Notification::assertNotSentTo($optedOut, NewPluginAvailable::class);
    }

    public function test_job_does_not_notify_plugin_author(): void
    {
        Notification::fake();

        $author = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin = Plugin::factory()->approved()->for($author)->create();

        (new SendNewPluginNotifications($plugin))->handle();

        Notification::assertNotSentTo($author, NewPluginAvailable::class);
    }
}
