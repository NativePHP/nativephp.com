<?php

namespace Tests\Feature;

use App\Listeners\SuppressMailNotificationListener;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\NotificationSending;
use Tests\TestCase;

class SuppressMailNotificationListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_mail_when_user_has_opted_in(): void
    {
        $user = User::factory()->create(['receives_notification_emails' => true]);

        $event = new NotificationSending(
            $user,
            new PluginApproved(
                plugin: Plugin::factory()->for($user)->create(),
            ),
            'mail',
        );

        $listener = new SuppressMailNotificationListener;

        $this->assertTrue($listener->handle($event));
    }

    public function test_suppresses_mail_when_user_has_opted_out(): void
    {
        $user = User::factory()->create(['receives_notification_emails' => false]);

        $event = new NotificationSending(
            $user,
            new PluginApproved(
                plugin: Plugin::factory()->for($user)->create(),
            ),
            'mail',
        );

        $listener = new SuppressMailNotificationListener;

        $this->assertFalse($listener->handle($event));
    }

    public function test_allows_non_mail_channels_regardless_of_preference(): void
    {
        $user = User::factory()->create(['receives_notification_emails' => false]);

        $event = new NotificationSending(
            $user,
            new PluginApproved(
                plugin: Plugin::factory()->for($user)->create(),
            ),
            'database',
        );

        $listener = new SuppressMailNotificationListener;

        $this->assertTrue($listener->handle($event));
    }

    public function test_new_users_receive_notifications_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->receives_notification_emails);
    }

    public function test_suppresses_mail_when_user_email_is_not_verified(): void
    {
        $user = User::factory()->unverified()->create(['receives_notification_emails' => true]);

        $event = new NotificationSending(
            $user,
            new PluginApproved(
                plugin: Plugin::factory()->for($user)->create(),
            ),
            'mail',
        );

        $listener = new SuppressMailNotificationListener;

        $this->assertFalse($listener->handle($event));
    }

    public function test_allows_non_mail_channels_for_unverified_users(): void
    {
        $user = User::factory()->unverified()->create(['receives_notification_emails' => true]);

        $event = new NotificationSending(
            $user,
            new PluginApproved(
                plugin: Plugin::factory()->for($user)->create(),
            ),
            'database',
        );

        $listener = new SuppressMailNotificationListener;

        $this->assertTrue($listener->handle($event));
    }
}
