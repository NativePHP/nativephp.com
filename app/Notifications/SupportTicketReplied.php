<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicket\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
            ->subject('Update on your support request: '.$this->ticket->mask)
            ->greeting("Hi {$notifiable->first_name},")
            ->line('Your support ticket **'.e($this->ticket->subject).'** has received a new reply.')
            ->line('Please log in to your dashboard to view the message and respond.')
            ->action('View Ticket', route('customer.support.tickets.show', $this->ticket))
            ->line('*Please do not reply to this email — responses must be submitted through the support portal.*');
    }
}
