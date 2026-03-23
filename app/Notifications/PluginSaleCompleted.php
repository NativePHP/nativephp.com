<?php

namespace App\Notifications;

use App\Models\PluginPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PluginSaleCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, PluginPayout>  $payouts
     */
    public function __construct(
        public Collection $payouts
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
        $totalPayout = $this->payouts->sum('developer_amount');

        $message = (new MailMessage)
            ->subject("You've made a sale!")
            ->greeting('Great news!')
            ->line('A sale has been completed for the following plugin(s):');

        foreach ($this->payouts as $payout) {
            $pluginName = $payout->pluginLicense->plugin->name ?? 'Unknown Plugin';
            $amount = number_format($payout->developer_amount / 100, 2);
            $message->line("- **{$pluginName}**: \${$amount}");
        }

        $formattedTotal = number_format($totalPayout / 100, 2);

        $message->line("**Total payout: \${$formattedTotal}**")
            ->action('View Developer Dashboard', route('customer.developer.dashboard'))
            ->line('Thank you for contributing to the NativePHP ecosystem!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payout_ids' => $this->payouts->pluck('id')->toArray(),
            'total_developer_amount' => $this->payouts->sum('developer_amount'),
        ];
    }
}
