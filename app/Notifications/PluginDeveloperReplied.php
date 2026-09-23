<?php

namespace App\Notifications;

use App\Filament\Resources\PluginResource;
use App\Models\Plugin;
use App\Models\PluginActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PluginDeveloperReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Plugin $plugin,
        public PluginActivity $activity
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

        return (new MailMessage)
            ->subject('New plugin reply: '.$plugin->name)
            ->greeting('A developer has replied about their plugin!')
            ->line("**Plugin:** {$plugin->name}")
            ->line('**From:** '.($plugin->user?->name ?? 'Unknown'))
            ->line('**Reply:**')
            ->line(Str::limit((string) $this->activity->note, 500))
            ->action('View Plugin', PluginResource::getUrl('edit', ['record' => $plugin]));
    }
}
