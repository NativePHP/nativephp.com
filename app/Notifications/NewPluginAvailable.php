<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPluginAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Plugin $plugin
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (! $notifiable->receives_new_plugin_notifications) {
            return [];
        }

        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Plugin: {$this->plugin->name}")
            ->greeting('A new plugin is available!')
            ->line("**{$this->plugin->name}** has just been added to the NativePHP Plugin Marketplace.")
            ->action('View Plugin', route('plugins.show', $this->plugin->routeParams()))
            ->line('You can manage your notification preferences in your account settings.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "New Plugin: {$this->plugin->name}",
            'body' => "{$this->plugin->name} has just been added to the NativePHP Plugin Marketplace.",
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'action_url' => route('plugins.show', $this->plugin->routeParams()),
            'action_label' => 'View Plugin',
        ];
    }
}
