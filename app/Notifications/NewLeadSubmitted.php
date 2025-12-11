<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Lead $lead
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Build My App Enquiry: '.$this->lead->company)
            ->replyTo($this->lead->email, $this->lead->name)
            ->greeting('New lead received!')
            ->line("**Name:** {$this->lead->name}")
            ->line("**Email:** {$this->lead->email}")
            ->line("**Company:** {$this->lead->company}")
            ->line("**Budget:** {$this->lead->budget_label}")
            ->line('**Project Description:**')
            ->line($this->lead->description);
    }
}
