<?php

namespace App\Notifications;

use App\Enums\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseKeyGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $licenseKey,
        public ?Subscription $subscription = null,
        public ?string $firstName = null,
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

    public function toMail(object $notifiable): MailMessage
    {
        $greeting = $this->firstName
            ? "{$this->firstName}, your license is ready!"
            : 'Your license is ready!';

        return (new MailMessage)
            ->subject('Your NativePHP License Key')
            ->greeting($greeting)
            ->line('Thank you for purchasing a NativePHP for Mobile license.')
            ->line('Your license key is:')
            ->line("**{$this->licenseKey}**")
            ->line('When prompted by Composer, use your email address as the username and this license key as the password.')
            ->action('View Installation Guide', url('/docs/mobile/1/getting-started/installation'))
            ->line('If you need to manage your subscription for this license, you can do so on [Stripe](https://billing.stripe.com/p/login/4gwaGV5VK0uU44E288).')
            ->line("If you have any questions, please don't hesitate to reach out to our support team.")
            ->lineIf($this->subscription === Subscription::Max, 'As a Max subscriber, you also have access to the NativePHP/mobile repository. To access it, please log in to [Anystack.sh](https://auth.anystack.sh/?accountType=customer) using the same email address you used for your purchase.')
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
            'license_key' => $this->licenseKey,
            'firstName' => $this->firstName,
        ];
    }
}
