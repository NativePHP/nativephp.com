<?php

namespace App\Notifications;

use App\Filament\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SupportTicketSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket->loadMissing('user');

        return (new MailMessage)
            ->subject('New Support Ticket: '.$ticket->subject)
            ->replyTo($ticket->user->email, $ticket->user->name)
            ->greeting('New support ticket received!')
            ->line("**Product:** {$ticket->product}")
            ->line('**Issue Type:** '.($ticket->issue_type ?? 'N/A'))
            ->line("**Subject:** {$ticket->subject}")
            ->line('**Message:**')
            ->line(Str::limit($ticket->message, 500))
            ->action('View Ticket', SupportTicketResource::getUrl('view', ['record' => $ticket]));
    }
}
