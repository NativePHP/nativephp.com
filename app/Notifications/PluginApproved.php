<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PluginApproved extends Notification implements ShouldQueue
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
            ->subject('Your Plugin Has Been Approved!')
            ->greeting('Great news!')
            ->line("Your plugin **{$this->plugin->name}** has been approved and is now listed in the NativePHP Plugin Directory.")
            ->action('View Plugin Directory', url('/plugins'))
            ->line('Thank you for contributing to the NativePHP ecosystem!');
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
        ];
    }
}
