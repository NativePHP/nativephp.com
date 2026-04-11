<?php

namespace Tests\Feature\Notifications;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginApprovedTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_action_links_to_plugin_page(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new PluginApproved($plugin);
        $mail = $notification->toMail($user);

        $this->assertEquals(route('plugins.show', ['vendor' => 'acme', 'package' => 'awesome-plugin']), $mail->actionUrl);
    }

    public function test_database_notification_contains_action_url(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new PluginApproved($plugin);
        $data = $notification->toArray($user);

        $this->assertEquals(route('plugins.show', ['vendor' => 'acme', 'package' => 'awesome-plugin']), $data['action_url']);
        $this->assertEquals('View Plugin', $data['action_label']);
    }

    public function test_mail_contains_plugin_name(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new PluginApproved($plugin);
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('acme/awesome-plugin', $mail->render()->toHtml());
    }

    public function test_via_returns_mail_and_database(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create();

        $notification = new PluginApproved($plugin);

        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }
}
