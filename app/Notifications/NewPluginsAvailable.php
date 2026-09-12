<?php

namespace App\Notifications;

use App\Http\Controllers\NotificationUnsubscribeController;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NewPluginsAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Plugin>  $plugins
     */
    public function __construct(
        public Collection $plugins
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
        /** @var User $notifiable */
        $unsubscribeUrl = NotificationUnsubscribeController::signedUnsubscribeUrl($notifiable);

        $count = $this->plugins->count();

        $mail = (new MailMessage)
            ->subject($count === 1
                ? "New Plugin: {$this->plugins->first()->name}"
                : "{$count} New Plugins on the NativePHP Marketplace")
            ->greeting($count === 1 ? 'A new plugin is available!' : 'New plugins are available!')
            ->line($count === 1
                ? 'The following plugin has just been added to the NativePHP Plugin Marketplace:'
                : 'The following plugins have just been added to the NativePHP Plugin Marketplace:');

        foreach ($this->plugins as $plugin) {
            $mail->line('**['.$plugin->name.']('.route('plugins.show', $plugin->routeParams()).')**');
        }

        return $mail->line('[Unsubscribe from new plugin notifications]('.$unsubscribeUrl.').');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->plugins->count() === 1
                ? "New Plugin: {$this->plugins->first()->name}"
                : "{$this->plugins->count()} New Plugins on the NativePHP Marketplace",
            'plugin_ids' => $this->plugins->pluck('id')->all(),
            'plugin_names' => $this->plugins->pluck('name')->all(),
        ];
    }
}
