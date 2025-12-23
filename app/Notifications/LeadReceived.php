<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thank you for your enquiry')
            ->greeting("Hi {$notifiable->name},")
            ->line('Thank you for reaching out to NativePHP about your app development project.')
            ->line('We have received your enquiry and one of our team members will be in touch soon to discuss your requirements.')
            ->line('In the meantime, feel free to explore our documentation or join our Discord community.')
            ->action('Visit NativePHP', url('/'))
            ->salutation('Best regards,<br>The NativePHP Team');
    }
}
