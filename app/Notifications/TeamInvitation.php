<?php

namespace App\Notifications;

use App\Models\TeamUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitation extends Notification implements ShouldQueue
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
        $team = $this->teamUser->team;
        $ownerName = $team->owner->display_name;

        return (new MailMessage)
            ->subject("You've been invited to join {$team->name} on NativePHP")
            ->greeting('Hello!')
            ->line("**{$ownerName}** ({$team->owner->email}) has invited you to join **{$team->name}** on NativePHP.")
            ->line('As a team member, you will receive:')
            ->line('- Free access to all first-party NativePHP plugins')
            ->line('- Access to the Plugin Dev Kit GitHub repository')
            ->action('Accept Invitation', route('team.invitation.accept', $this->teamUser->invitation_token))
            ->line('If you did not expect this invitation, you can safely ignore this email.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'team_user_id' => $this->teamUser->id,
            'team_name' => $this->teamUser->team->name,
            'email' => $this->teamUser->email,
        ];
    }
}
