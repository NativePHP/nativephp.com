<?php

namespace Tests\Feature\Notifications;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginRejected;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginRejectedTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_action_links_to_dashboard_plugin_page(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new PluginRejected($plugin);
        $mail = $notification->toMail($user);

        $this->assertEquals(route('customer.plugins.show', ['vendor' => 'acme', 'package' => 'awesome-plugin']), $mail->actionUrl);
    }

    public function test_database_notification_contains_action_url(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create(['name' => 'acme/awesome-plugin']);

        $notification = new PluginRejected($plugin);
        $data = $notification->toArray($user);

        $this->assertEquals(route('customer.plugins.show', ['vendor' => 'acme', 'package' => 'awesome-plugin']), $data['action_url']);
        $this->assertEquals('View Plugin', $data['action_label']);
    }

    public function test_mail_contains_rejection_reason(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create([
            'name' => 'acme/awesome-plugin',
            'rejection_reason' => 'Missing license file',
        ]);

        $notification = new PluginRejected($plugin);
        $rendered = $notification->toMail($user)->render()->toHtml();

        $this->assertStringContainsString('Missing license file', $rendered);
    }

    public function test_database_notification_contains_rejection_reason(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create([
            'name' => 'acme/awesome-plugin',
            'rejection_reason' => 'Missing license file',
        ]);

        $notification = new PluginRejected($plugin);
        $data = $notification->toArray($user);

        $this->assertEquals('Missing license file', $data['rejection_reason']);
    }

    public function test_via_returns_mail_and_database(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->create();

        $notification = new PluginRejected($plugin);

        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }
}
