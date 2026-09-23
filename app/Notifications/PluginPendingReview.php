<?php

namespace App\Notifications;

use App\Filament\Resources\PluginResource;
use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PluginPendingReview extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plugin = $this->plugin->loadMissing('user');
        $user = $plugin->user;

        return (new MailMessage)
            ->subject('New Plugin Submission: '.$plugin->name)
            ->greeting('A plugin has been submitted for review!')
            ->line("**Plugin:** {$plugin->name}")
            ->when($plugin->display_name, fn (MailMessage $message) => $message->line("**Display name:** {$plugin->display_name}"))
            ->line('**Submitted by:** '.($user?->name ?? 'Unknown'))
            ->line('**Type:** '.$plugin->type->label())
            ->when($plugin->tier, fn (MailMessage $message) => $message->line('**Tier:** '.$plugin->tier->label()))
            ->action('Review Plugin', PluginResource::getUrl('edit', ['record' => $plugin]));
    }
}
