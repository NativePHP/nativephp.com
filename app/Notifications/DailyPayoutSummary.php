<?php

namespace App\Notifications;

use App\Models\PluginPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DailyPayoutSummary extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, PluginPayout>  $attemptedPayouts  Transferred or failed in the last 24 hours
     * @param  Collection<int, PluginPayout>  $upcomingPayouts  Waiting to be sent to developers we can pay
     * @param  Collection<int, PluginPayout>  $pendingPayouts  Held until the developer's Stripe account is active
     */
    public function __construct(
        public Collection $attemptedPayouts,
        public Collection $upcomingPayouts,
        public Collection $pendingPayouts,
        public int $totalPaidOut,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $transferred = $this->attemptedPayouts->filter(fn (PluginPayout $payout): bool => $payout->isTransferred());
        $failed = $this->attemptedPayouts->filter(fn (PluginPayout $payout): bool => $payout->isFailed());

        $message = (new MailMessage)
            ->subject($this->subjectLine($failed->count()))
            ->greeting('Plugin payout summary')
            ->line('**Last 24 hours**')
            ->line("Total payouts: {$this->attemptedPayouts->count()}")
            ->line('Total amount: '.$this->formatAmount($this->attemptedPayouts->sum('developer_amount')))
            ->line("Successful: {$transferred->count()} (".$this->formatAmount($transferred->sum('developer_amount')).' paid out)')
            ->line("Failed: {$failed->count()}")
            ->line('**Not paid yet**')
            ->line("Upcoming: {$this->upcomingPayouts->count()} (".$this->formatAmount($this->upcomingPayouts->sum('developer_amount')).'), for active accounts')
            ->line("Pending: {$this->pendingPayouts->count()} (".$this->formatAmount($this->pendingPayouts->sum('developer_amount')).'), held until the account is active')
            ->line('**Paid out to date:** '.$this->formatAmount($this->totalPaidOut));

        if ($failed->isNotEmpty()) {
            $message->line('**Failed payouts**');
        }

        foreach ($failed as $payout) {
            $developer = $payout->developerAccount?->user;
            $pluginName = $payout->pluginLicense?->plugin?->name ?? 'Unknown plugin';
            $payoutUrl = route('filament.admin.resources.plugin-payouts.view', $payout);

            $message
                ->line("Payout #{$payout->id}: ".$this->formatAmount($payout->developer_amount)." for {$pluginName} to {$developer?->name} ({$developer?->email}). [View payout]({$payoutUrl})")
                ->line('Stripe error: '.($payout->failure_reason ?? 'none recorded'));
        }

        return $message;
    }

    private function subjectLine(int $failedCount): string
    {
        if ($failedCount > 0) {
            return "Payout summary: {$failedCount} failed";
        }

        if ($this->attemptedPayouts->isEmpty()) {
            return 'Payout summary: nothing sent';
        }

        return "Payout summary: all {$this->attemptedPayouts->count()} sent";
    }

    private function formatAmount(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }
}
