<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PluginSubmissionReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Plugin>  $plugins
     */
    public function __construct(public Collection $plugins) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Action Required: Finalize Your Plugin Submission')
            ->greeting("Hi {$notifiable->name},")
            ->line('We\'ve recently updated the plugin submission process with new requirements. Please review your pending plugin submissions to ensure they are configured correctly — particularly whether you intended to submit a **free** or **paid** plugin.')
            ->line('The following plugins need your attention:');

        foreach ($this->plugins as $plugin) {
            $message->line("- **{$plugin->name}** ({$plugin->status->label()})");
        }

        $message->action('Review Your Plugins', route('customer.plugins.index'))
            ->line('Please visit your plugin dashboard, review each submission, and re-submit when ready.')
            ->salutation("Thanks,\n\nThe NativePHP Team");

        return $message;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Action Required: Finalize Your Plugin Submissions',
            'body' => 'Please review your pending plugin submissions to ensure they are configured correctly.',
            'plugin_names' => $this->plugins->pluck('name')->all(),
        ];
    }
}
