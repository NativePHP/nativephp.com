<?php

namespace Tests\Feature\Notifications;

use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\User;
use App\Notifications\DailyPayoutSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Tests\TestCase;

class DailyPayoutSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_shows_the_totals_and_the_stripe_error_for_each_failed_payout(): void
    {
        $developer = User::factory()->create(['name' => 'Ana Developer', 'email' => 'ana@example.com']);
        $developerAccount = DeveloperAccount::factory()->create(['user_id' => $developer->id]);
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developer->id, 'name' => 'acme/camera-plugin']);

        $failedPayout = PluginPayout::factory()->failed()->create([
            'plugin_license_id' => PluginLicense::factory()->create(['plugin_id' => $plugin->id])->id,
            'developer_account_id' => $developerAccount->id,
            'developer_amount' => 1000,
            'failure_reason' => 'Funds cannot be sent to accounts located in MX when the account is under the full service agreement.',
        ]);

        $notification = new DailyPayoutSummary(
            attemptedPayouts: collect([
                PluginPayout::factory()->transferred()->create(['developer_amount' => 2030]),
                PluginPayout::factory()->transferred()->create(['developer_amount' => 3430]),
                $failedPayout,
            ]),
            upcomingPayouts: collect([
                PluginPayout::factory()->pending()->create(['developer_amount' => 2500]),
            ]),
            pendingPayouts: collect([
                PluginPayout::factory()->create(['status' => PayoutStatus::Held, 'developer_amount' => 1000]),
                PluginPayout::factory()->create(['status' => PayoutStatus::Held, 'developer_amount' => 2000]),
            ]),
            totalPaidOut: 123456,
        );

        $mail = $notification->toMail(new AnonymousNotifiable);
        $rendered = $mail->render()->toHtml();

        $this->assertSame('Payout summary: 1 failed', $mail->subject);
        $this->assertStringContainsString('Total payouts: 3', $rendered);
        $this->assertStringContainsString('Total amount: $64.60', $rendered);
        $this->assertStringContainsString('Successful: 2 ($54.60 paid out)', $rendered);
        $this->assertStringContainsString('Failed: 1', $rendered);
        $this->assertStringContainsString('Upcoming: 1 ($25.00), for active accounts', $rendered);
        $this->assertStringContainsString('Pending: 2 ($30.00), held until the account is active', $rendered);
        $this->assertStringContainsString('Paid out to date:</strong> $1,234.56', $rendered);
        $this->assertStringContainsString("Payout #{$failedPayout->id}: $10.00 for acme/camera-plugin to Ana Developer", $rendered);
        $this->assertStringContainsString('ana@example.com', $rendered);
        $this->assertStringContainsString('Stripe error: Funds cannot be sent to accounts located in MX when the account is under the full service agreement.', $rendered);
        $this->assertStringContainsString(route('filament.admin.resources.plugin-payouts.view', $failedPayout), $rendered);
    }

    public function test_subject_says_every_payout_was_sent_when_none_failed(): void
    {
        $notification = new DailyPayoutSummary(
            attemptedPayouts: collect([
                PluginPayout::factory()->transferred()->create(),
                PluginPayout::factory()->transferred()->create(),
            ]),
            upcomingPayouts: collect(),
            pendingPayouts: collect(),
            totalPaidOut: 5000,
        );

        $mail = $notification->toMail(new AnonymousNotifiable);
        $rendered = $mail->render()->toHtml();

        $this->assertSame('Payout summary: all 2 sent', $mail->subject);
        $this->assertStringContainsString('Failed: 0', $rendered);
        $this->assertStringNotContainsString('Failed payouts', $rendered);
    }

    public function test_subject_says_nothing_was_sent_when_payouts_are_only_waiting(): void
    {
        $notification = new DailyPayoutSummary(
            attemptedPayouts: collect(),
            upcomingPayouts: collect(),
            pendingPayouts: collect([
                PluginPayout::factory()->create(['status' => PayoutStatus::Held, 'developer_amount' => 1500]),
            ]),
            totalPaidOut: 5000,
        );

        $mail = $notification->toMail(new AnonymousNotifiable);

        $this->assertSame('Payout summary: nothing sent', $mail->subject);
        $this->assertStringContainsString('Pending: 1 ($15.00), held until the account is active', $mail->render()->toHtml());
    }
}
