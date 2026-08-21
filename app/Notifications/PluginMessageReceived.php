<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells a developer that the review team has messaged them about a plugin.
 *
 * The message body is deliberately withheld — they must sign in to read it and reply.
 */
class PluginMessageReceived extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New message about your plugin')
            ->greeting('Hello,')
            ->line("The NativePHP team has sent you a message about your plugin **{$this->plugin->name}**.")
            ->line('Please sign in to read it and reply.')
            ->action('View Message', $this->actionUrl())
            ->line('*Please do not reply to this email — responses must be sent from your plugin\'s page.*');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New message about your plugin',
            'body' => "The NativePHP team has sent you a message about {$this->plugin->name}. Sign in to read it and reply.",
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'action_url' => $this->actionUrl(),
            'action_label' => 'View Message',
        ];
    }

    protected function actionUrl(): string
    {
        return route('customer.plugins.show', [...$this->plugin->routeParams(), 'tab' => 'activity']);
    }
}
