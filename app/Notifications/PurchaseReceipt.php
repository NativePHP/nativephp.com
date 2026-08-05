<?php

namespace App\Notifications;

use App\Contracts\TransactionalNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseReceipt extends Notification implements ShouldQueue, TransactionalNotification
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
            ? "Thank you, {$notifiable->first_name}!"
            : 'Thank you for your purchase!';

        return (new MailMessage)
            ->subject('Thank you for your purchase')
            ->greeting($greeting)
            ->line('Your order is complete. Thank you for supporting NativePHP — every purchase helps fund the open source projects the ecosystem is built on.')
            ->line('A receipt for this payment will arrive separately from Stripe, our payment processor.')
            ->line('You can review your order history and access everything you have purchased from your dashboard.')
            ->action('View Your Dashboard', route('customer.purchase-history.index'))
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
            'title' => 'Thank you for your purchase',
            'body' => 'Your order is complete. You can access your purchases from your dashboard.',
        ];
    }
}
