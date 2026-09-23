<?php

namespace App\Notifications;

use App\Contracts\TransactionalNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UltraSubscriptionStarted extends Notification implements ShouldQueue, TransactionalNotification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $greeting = $notifiable->first_name
            ? "Welcome to Ultra, {$notifiable->first_name}!"
            : 'Welcome to Ultra!';

        return (new MailMessage)
            ->subject('Welcome to NativePHP Ultra')
            ->greeting($greeting)
            ->line('Thank you for subscribing to Ultra. Your support directly funds NativePHP and the open source projects the ecosystem is built on.')
            ->line('A receipt for this payment will arrive separately from Stripe, our payment processor.')
            ->line('You can manage your subscription and explore everything your Ultra membership unlocks from your Ultra dashboard.')
            ->action('Go to Your Ultra Dashboard', route('customer.ultra.index'))
            ->line("If you have any questions, just reply to this email and we'll be happy to help.")
            ->salutation("Happy coding!\n\nThe NativePHP Team")
            ->success();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Welcome to NativePHP Ultra',
            'body' => 'Your Ultra subscription is active. Explore your benefits from the Ultra dashboard.',
        ];
    }
}
