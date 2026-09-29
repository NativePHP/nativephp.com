<?php

namespace Tests\Feature\Commands;

use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Models\PluginPayout;
use App\Notifications\DailyPayoutSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendDailyPayoutSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_emails_support_a_summary_of_sent_and_waiting_payouts(): void
    {
        Notification::fake();

        $transferredPayout = PluginPayout::factory()->transferred()->create([
            'developer_amount' => 2000,
            'last_attempted_at' => now()->subHours(2),
        ]);
        $failedPayout = PluginPayout::factory()->failed()->create(['last_attempted_at' => now()->subHour()]);
        PluginPayout::factory()->transferred()->create([
            'developer_amount' => 5000,
            'last_attempted_at' => now()->subDays(2),
        ]);
        PluginPayout::factory()->failed()->create(['last_attempted_at' => now()->subHours(25)]);

        $upcomingPayout = PluginPayout::factory()->pending()->create(['eligible_for_payout_at' => now()->addDays(10)]);
        $heldPayout = PluginPayout::factory()->create(['status' => PayoutStatus::Held]);
        $payoutForInactiveAccount = PluginPayout::factory()->pending()->create([
            'developer_account_id' => DeveloperAccount::factory()->pending()->create()->id,
        ]);

        $this->artisan('payouts:send-daily-summary')
            ->expectsOutputToContain('Sent the daily payout summary')
            ->assertExitCode(0);

        Notification::assertSentOnDemand(
            DailyPayoutSummary::class,
            fn (DailyPayoutSummary $notification, array $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === config('mail.support_address')
                && $notification->attemptedPayouts->pluck('id')->all() === [$transferredPayout->id, $failedPayout->id]
                && $notification->upcomingPayouts->pluck('id')->all() === [$upcomingPayout->id]
                && $notification->pendingPayouts->pluck('id')->all() === [$heldPayout->id, $payoutForInactiveAccount->id]
                && $notification->totalPaidOut === 7000
        );
    }

    public function test_emails_when_nothing_was_sent_but_payouts_are_waiting(): void
    {
        Notification::fake();

        $heldPayout = PluginPayout::factory()->create(['status' => PayoutStatus::Held]);

        $this->artisan('payouts:send-daily-summary')->assertExitCode(0);

        Notification::assertSentOnDemand(
            DailyPayoutSummary::class,
            fn (DailyPayoutSummary $notification) => $notification->attemptedPayouts->isEmpty()
                && $notification->pendingPayouts->pluck('id')->all() === [$heldPayout->id]
        );
    }

    public function test_does_not_email_when_there_is_nothing_to_report(): void
    {
        Notification::fake();

        PluginPayout::factory()->transferred()->create(['last_attempted_at' => now()->subDays(2)]);
        PluginPayout::factory()->failed()->create(['last_attempted_at' => now()->subDays(3)]);

        $this->artisan('payouts:send-daily-summary')
            ->expectsOutputToContain('No payouts to report')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }
}
