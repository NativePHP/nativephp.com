<?php

namespace App\Notifications;

use App\Filament\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\SupportTicket\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SupportTicketUserReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public Reply $reply
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
        $ticket = $this->ticket->loadMissing('user');

        return (new MailMessage)
            ->subject('New Reply on Support Ticket: '.$ticket->subject)
            ->replyTo($ticket->user->email, $ticket->user->name)
            ->greeting('New reply on a support ticket!')
            ->line("**From:** {$this->reply->user->name}")
            ->line("**Subject:** {$ticket->subject}")
            ->line('**Reply:**')
            ->line(Str::limit($this->reply->message, 500))
            ->action('View Ticket', SupportTicketResource::getUrl('view', ['record' => $ticket]));
    }
}
