<?php

namespace Tests\Feature\Notifications;

use App\Jobs\SendNewPluginNotifications;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginAvailable;
use App\Services\PluginSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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

    public function test_notification_job_is_dispatched_on_first_approval(): void
    {
        Bus::fake(SendNewPluginNotifications::class);

        $author = User::factory()->create();
        $plugin = Plugin::factory()->pending()->for($author)->create();
        $admin = User::factory()->create();

        $plugin->approve($admin->id);

        Bus::assertDispatched(SendNewPluginNotifications::class, function ($job) use ($plugin) {
            return $job->plugin->id === $plugin->id;
        });
    }

    public function test_notification_job_is_not_dispatched_on_re_approval(): void
    {
        Bus::fake(SendNewPluginNotifications::class);

        $author = User::factory()->create();
        $plugin = Plugin::factory()->pending()->for($author)->create([
            'approved_at' => now()->subDay(),
        ]);
        $admin = User::factory()->create();

        $plugin->approve($admin->id);

        Bus::assertNotDispatched(SendNewPluginNotifications::class);
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

    public function test_mail_action_links_to_plugin_page(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new NewPluginAvailable($plugin);
        $mail = $notification->toMail($user);

        $this->assertEquals(route('plugins.show', ['vendor' => 'acme', 'package' => 'awesome-plugin']), $mail->actionUrl);
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
        $this->assertEquals(route('plugins.show', ['vendor' => 'acme', 'package' => 'awesome-plugin']), $data['action_url']);
        $this->assertEquals('View Plugin', $data['action_label']);
    }

    public function test_mail_contains_signed_unsubscribe_link(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create();

        $notification = new NewPluginAvailable($plugin);
        $mail = $notification->toMail($user);

        $baseUrl = route('notifications.unsubscribe', ['user' => $user]);
        $found = collect($mail->introLines)->concat($mail->outroLines)->contains(function ($line) use ($baseUrl) {
            return str_contains($line, 'Unsubscribe from new plugin notifications')
                && str_contains($line, $baseUrl);
        });

        $this->assertTrue($found, 'Mail should contain a signed unsubscribe link.');
    }

    public function test_new_users_receive_new_plugin_notifications_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->receives_new_plugin_notifications);
    }
}
