<?php

namespace Tests\Feature;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginSubmissionReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendPluginSubmissionRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_notification_to_user_with_draft_plugin(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->draft()->for($user)->create();

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertSentTo($user, PluginSubmissionReminder::class);
    }

    public function test_sends_notification_to_user_with_pending_plugin(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->pending()->for($user)->create();

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertSentTo($user, PluginSubmissionReminder::class);
    }

    public function test_sends_notification_to_user_with_rejected_plugin(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->rejected()->for($user)->create();

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertSentTo($user, PluginSubmissionReminder::class);
    }

    public function test_does_not_send_to_user_with_only_approved_plugins(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->approved()->for($user)->create();

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    public function test_sends_one_notification_per_user_with_multiple_plugins(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->draft()->for($user)->create(['name' => 'acme/plugin-one']);
        Plugin::factory()->pending()->for($user)->create(['name' => 'acme/plugin-two']);

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertSentToTimes($user, PluginSubmissionReminder::class, 1);

        Notification::assertSentTo($user, PluginSubmissionReminder::class, function ($notification) {
            return $notification->plugins->count() === 2;
        });
    }

    public function test_notification_includes_plugin_names(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->draft()->for($user)->create(['name' => 'acme/my-plugin']);

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertSentTo($user, PluginSubmissionReminder::class, function ($notification) {
            return $notification->plugins->first()->name === 'acme/my-plugin';
        });
    }

    public function test_excludes_approved_plugins_from_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->draft()->for($user)->create(['name' => 'acme/draft-one']);
        Plugin::factory()->approved()->for($user)->create(['name' => 'acme/approved-one']);

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertSentTo($user, PluginSubmissionReminder::class, function ($notification) {
            return $notification->plugins->count() === 1
                && $notification->plugins->first()->name === 'acme/draft-one';
        });
    }

    public function test_dry_run_does_not_send_notifications(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Plugin::factory()->draft()->for($user)->create();

        $this->artisan('plugins:send-submission-reminders', ['--dry-run' => true])
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    public function test_notification_email_contains_expected_content(): void
    {
        $user = User::factory()->create(['name' => 'Jane']);
        $plugin = Plugin::factory()->draft()->for($user)->create(['name' => 'acme/test-plugin']);

        $notification = new PluginSubmissionReminder(collect([$plugin]));
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('Action Required', $mail->subject);
        $this->assertStringContainsString('acme/test-plugin', implode(' ', array_map(fn ($line) => (string) $line, $mail->introLines)));
    }

    public function test_notification_database_array_contains_plugin_names(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->draft()->for($user)->create(['name' => 'acme/test-plugin']);

        $notification = new PluginSubmissionReminder(collect([$plugin]));
        $data = $notification->toArray($user);

        $this->assertArrayHasKey('plugin_names', $data);
        $this->assertContains('acme/test-plugin', $data['plugin_names']);
    }

    public function test_does_not_send_to_unverified_users(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        Plugin::factory()->draft()->for($user)->create();

        $this->artisan('plugins:send-submission-reminders')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }
}
