<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Filament\Resources\PluginReportResource;
use App\Models\PluginReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class PluginReported extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PluginReport $report
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
        $report = $this->report->loadMissing(['plugin', 'user']);

        return (new MailMessage)
            ->subject('Plugin Reported: '.$report->plugin->name)
            ->greeting('A plugin has been reported.')
            ->line("**Plugin:** {$report->plugin->name}")
            ->line('**Reported by:** '.$report->user->email)
            ->line('**Reason:** '.$report->category->label())
            ->line('**Message:**')
            ->line(Str::limit($report->message, 500))
            ->action('Review Report', PluginReportResource::getUrl('view', ['record' => $report]));
    }
}
