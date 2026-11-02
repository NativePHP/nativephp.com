<?php

namespace Tests\Feature;

use App\Enums\PluginReportCategory;
use App\Enums\PluginReportStatus;
use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginReport;
use App\Models\User;
use App\Notifications\PluginReported;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_guest_is_redirected_to_login_when_reporting(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        $response = $this->post(route('plugins.report.store', $plugin->routeParams()), [
            'category' => PluginReportCategory::MaliciousCode->value,
            'message' => 'This plugin exfiltrates data.',
        ]);

        $response->assertRedirect(route('customer.login'));
        $this->assertDatabaseCount('plugin_reports', 0);
    }

    public function test_logged_in_user_can_report_a_plugin_and_admins_are_notified(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->approved()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plugins.report.store', $plugin->routeParams()), [
            'category' => PluginReportCategory::UnresponsiveAuthor->value,
            'message' => "I've messaged the author for weeks with no response.",
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('plugin_reports', [
            'plugin_id' => $plugin->id,
            'user_id' => $user->id,
            'category' => PluginReportCategory::UnresponsiveAuthor->value,
            'status' => PluginReportStatus::Open->value,
        ]);

        Notification::assertSentOnDemand(
            PluginReported::class,
            fn (PluginReported $notification, array $channels, object $notifiable): bool => $notifiable->routes['mail'] === config('mail.support_address')
                && $notification->report->plugin->is($plugin)
        );
    }

    public function test_user_cannot_report_their_own_plugin(): void
    {
        $owner = User::factory()->create();
        $plugin = Plugin::factory()->approved()->for($owner)->create();

        $response = $this->actingAs($owner)->post(route('plugins.report.store', $plugin->routeParams()), [
            'category' => PluginReportCategory::Other->value,
            'message' => 'Testing self-report.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('plugin_reports', 0);
    }

    public function test_report_requires_a_valid_category_and_message(): void
    {
        $plugin = Plugin::factory()->approved()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plugins.report.store', $plugin->routeParams()), [
            'category' => 'not-a-real-category',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['category', 'message']);
        $this->assertDatabaseCount('plugin_reports', 0);
    }

    public function test_user_cannot_file_a_second_open_report_for_the_same_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->create();
        $user = User::factory()->create();

        PluginReport::file($plugin, $user, PluginReportCategory::MaliciousCode, 'First report.');

        $response = $this->actingAs($user)->post(route('plugins.report.store', $plugin->routeParams()), [
            'category' => PluginReportCategory::MaliciousCode->value,
            'message' => 'Second report while the first is still open.',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('plugin_reports', 1);
    }

    public function test_user_can_file_a_new_report_once_their_previous_one_is_resolved(): void
    {
        $plugin = Plugin::factory()->approved()->create();
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $firstReport = PluginReport::file($plugin, $user, PluginReportCategory::MaliciousCode, 'First report.');
        $firstReport->resolve($admin);

        $response = $this->actingAs($user)->post(route('plugins.report.store', $plugin->routeParams()), [
            'category' => PluginReportCategory::MaliciousCode->value,
            'message' => 'Second report after the first was resolved.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('plugin_reports', 2);
    }
}
