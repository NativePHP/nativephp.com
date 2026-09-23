<?php

namespace App\Notifications;

use App\Models\EmailChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EmailChange $emailChange
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Email Has Been Changed')
            ->greeting('Hello!')
            ->line("The email address on your NativePHP account has been changed from {$this->emailChange->old_email} to {$this->emailChange->new_email}.")
            ->line('From now on, use the new address to log in.')
            ->line('If this was not you, contact our support team immediately.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'email_change_id' => $this->emailChange->id,
            'new_email' => $this->emailChange->new_email,
        ];
    }
}
