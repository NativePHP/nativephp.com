<?php

namespace App\Notifications;

use App\Models\EmailChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeRequested extends Notification implements ShouldQueue
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
            ->subject('Email Change Requested')
            ->greeting('Hello!')
            ->line("We received a request to change the email address on your NativePHP account from {$this->emailChange->old_email} to {$this->emailChange->new_email}.")
            ->line('A confirmation link has been sent to the new address. Your email will only change once that link is clicked.')
            ->line('If this was not you, reset your password immediately and contact our support team.');
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
