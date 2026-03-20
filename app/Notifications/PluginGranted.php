<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PluginGranted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Plugin $plugin,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $parts = explode('/', $this->plugin->name ?? '');
        $vendor = $parts[0] ?? '';
        $package = $parts[1] ?? '';

        $pluginUrl = "https://nativephp.com/plugins/{$vendor}/{$package}";

        return (new MailMessage)
            ->subject("You've been granted access to {$this->plugin->name}!")
            ->greeting('Great news!')
            ->line("You've been granted access to **{$this->plugin->name}**.")
            ->action('Check it out', $pluginUrl)
            ->line('Thank you for being a NativePHP customer!');
    }

    /**
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
