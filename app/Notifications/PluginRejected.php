<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PluginRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Plugin $plugin
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
        return (new MailMessage)
            ->subject('Plugin Submission Update')
            ->greeting('Hello,')
            ->line("Unfortunately, your plugin **{$this->plugin->name}** was not approved for the NativePHP Plugin Directory.")
            ->line('**Reason:**')
            ->line($this->plugin->rejection_reason)
            ->action('View Your Plugins', url('/customer/plugins'))
            ->line('If you have questions about this decision, please reach out to us.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'rejection_reason' => $this->plugin->rejection_reason,
        ];
    }
}
