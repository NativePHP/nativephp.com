<?php

namespace Tests\Feature\Notifications;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginsAvailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class NewPluginsAvailableTest extends TestCase
{
    use RefreshDatabase;

    public function test_via_returns_empty_array_when_user_opted_out(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => false]);
        $plugin = Plugin::factory()->create();

        $notification = new NewPluginsAvailable(new Collection([$plugin]));

        $this->assertEmpty($notification->via($user));
    }

    public function test_via_returns_mail_and_database_when_user_opted_in(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);
        $plugin = Plugin::factory()->create();

        $notification = new NewPluginsAvailable(new Collection([$plugin]));

        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }

    public function test_mail_subject_names_the_single_plugin_when_only_one(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->create(['name' => 'acme/awesome-plugin']);

        $notification = new NewPluginsAvailable(new Collection([$plugin]));
        $mail = $notification->toMail($user);

        $this->assertEquals('New Plugin: acme/awesome-plugin', $mail->subject);
    }

    public function test_mail_subject_uses_a_count_when_multiple_plugins(): void
    {
        $user = User::factory()->create();
        $plugins = Plugin::factory()->count(3)->create();

        $notification = new NewPluginsAvailable($plugins);
        $mail = $notification->toMail($user);

        $this->assertEquals('3 New Plugins on the NativePHP Marketplace', $mail->subject);
    }

    public function test_mail_lists_every_plugin(): void
    {
        $user = User::factory()->create();
        $pluginOne = Plugin::factory()->create(['name' => 'acme/one']);
        $pluginTwo = Plugin::factory()->create(['name' => 'acme/two']);

        $notification = new NewPluginsAvailable(new Collection([$pluginOne, $pluginTwo]));
        $html = $notification->toMail($user)->render()->toHtml();

        $this->assertStringContainsString('acme/one', $html);
        $this->assertStringContainsString('acme/two', $html);
    }

    public function test_mail_links_to_each_plugin_page(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->create(['name' => 'acme/awesome-plugin']);

        $notification = new NewPluginsAvailable(new Collection([$plugin]));
        $html = $notification->toMail($user)->render()->toHtml();

        $this->assertStringContainsString(
            route('plugins.show', ['vendor' => 'acme', 'package' => 'awesome-plugin']),
            $html
        );
    }

    public function test_database_notification_contains_all_plugin_ids(): void
    {
        $user = User::factory()->create();
        $pluginOne = Plugin::factory()->create();
        $pluginTwo = Plugin::factory()->create();

        $notification = new NewPluginsAvailable(new Collection([$pluginOne, $pluginTwo]));
        $data = $notification->toArray($user);

        $this->assertEquals([$pluginOne->id, $pluginTwo->id], $data['plugin_ids']);
    }

    public function test_mail_contains_signed_unsubscribe_link(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->create();

        $notification = new NewPluginsAvailable(new Collection([$plugin]));
        $mail = $notification->toMail($user);

        $baseUrl = route('notifications.unsubscribe', ['user' => $user]);
        $found = collect($mail->introLines)->concat($mail->outroLines)->contains(function ($line) use ($baseUrl) {
            return str_contains($line, 'Unsubscribe from new plugin notifications')
                && str_contains($line, $baseUrl);
        });

        $this->assertTrue($found, 'Mail should contain a signed unsubscribe link.');
    }
}
