<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicket\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SupportTicketReplied extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Update on your support request: '.$this->ticket->subject)
            ->greeting("Hi {$notifiable->first_name},")
            ->line('Your support ticket has received a new reply.')
            ->line('**'.e($this->ticket->subject).'**')
            ->line(Str::limit($this->reply->message, 500))
            ->action('View Ticket', route('support.tickets.show', $this->ticket));
    }
}
