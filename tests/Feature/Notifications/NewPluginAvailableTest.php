<?php

namespace Tests\Feature\Notifications;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginAvailable;
use App\Services\PluginSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewPluginAvailableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(PluginSyncService::class, function ($mock): void {
            $mock->shouldReceive('sync')->andReturn(true);
        });
    }

    public function test_notification_is_sent_to_opted_in_users_on_first_approval(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $optedIn = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $optedOut = User::factory()->create(['receives_new_plugin_notifications' => false]);

        $plugin = Plugin::factory()->pending()->for($author)->create();
        $admin = User::factory()->create();

        $plugin->approve($admin->id);

        Notification::assertSentTo($optedIn, NewPluginAvailable::class);
        Notification::assertNotSentTo($optedOut, NewPluginAvailable::class);
        Notification::assertNotSentTo($author, NewPluginAvailable::class);
    }

    public function test_notification_is_not_sent_on_re_approval(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $optedIn = User::factory()->create(['receives_new_plugin_notifications' => true]);

        $plugin = Plugin::factory()->pending()->for($author)->create([
            'approved_at' => now()->subDay(),
        ]);
        $admin = User::factory()->create();

        $plugin->approve($admin->id);

        Notification::assertNotSentTo($optedIn, NewPluginAvailable::class);
    }

    public function test_via_returns_empty_array_when_user_opted_out(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => false]);
        $plugin = Plugin::factory()->for($user)->create();

        $notification = new NewPluginAvailable($plugin);

        $this->assertEmpty($notification->via($user));
    }

    public function test_via_returns_mail_and_database_when_user_opted_in(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin = Plugin::factory()->for($user)->create();

        $notification = new NewPluginAvailable($plugin);

        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }

    public function test_mail_contains_plugin_name(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new NewPluginAvailable($plugin);
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('acme/awesome-plugin', $mail->subject);
    }

    public function test_database_notification_contains_plugin_data(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new NewPluginAvailable($plugin);
        $data = $notification->toArray($user);

        $this->assertEquals($plugin->id, $data['plugin_id']);
        $this->assertEquals('acme/awesome-plugin', $data['plugin_name']);
        $this->assertStringContainsString('acme/awesome-plugin', $data['title']);
    }

    public function test_new_users_receive_new_plugin_notifications_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->receives_new_plugin_notifications);
    }
}
