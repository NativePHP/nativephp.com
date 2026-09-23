<?php

namespace App\Notifications;

use App\Models\EmailChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyNewEmailAddress extends Notification implements ShouldQueue
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
            ->subject('Confirm Your New Email Address')
            ->greeting('Hello!')
            ->line('A request was made to change the email address on your NativePHP account to this address.')
            ->line('Nothing will change until you confirm by clicking the button below. This link expires in 60 minutes.')
            ->action('Confirm Email Change', $this->emailChange->confirmationUrl())
            ->line('**Heads up:** once confirmed, your plugin repository (Composer) credentials will use this new email address. Any `auth.json` files or CI secrets configured with your old address will stop working until you update them.')
            ->line('If you did not request this change, you can safely ignore this email.');
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
