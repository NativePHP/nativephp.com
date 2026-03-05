<?php

namespace App\Notifications;

use App\Models\TeamUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamUserRemoved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TeamUser $teamUser
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
        $teamName = $this->teamUser->team->name;

        return (new MailMessage)
            ->subject("You have been removed from {$teamName}")
            ->greeting('Hello!')
            ->line("You have been removed from **{$teamName}** on NativePHP.")
            ->line('Your team benefits, including free plugin access and subscriber-tier pricing, have been revoked.')
            ->action('View Plans', route('pricing'))
            ->line('If you believe this was a mistake, please contact the team owner.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'team_user_id' => $this->teamUser->id,
            'team_name' => $this->teamUser->team->name,
        ];
    }
}
