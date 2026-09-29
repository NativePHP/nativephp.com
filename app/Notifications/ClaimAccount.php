<?php

namespace App\Notifications;

use App\Contracts\TransactionalNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimAccount extends Notification implements ShouldQueue, TransactionalNotification
{
    use Queueable;

    public function __construct(public string $token) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Welcome to NativePHP — Claim Your Account')
            ->greeting('Welcome to NativePHP!')
            ->line('Thanks for your purchase. We\'ve created an account for you so you can access your licenses and downloads.')
            ->line('To finish setting up your account, please click the button below to verify your email address and set a password.')
            ->action('Claim Your Account', $url)
            ->line('This link will expire in '.config('auth.passwords.users.expire').' minutes. If it expires, you can request a new one from the password reset page.')
            ->salutation("Happy coding!\n\nThe NativePHP Team");
    }
}
