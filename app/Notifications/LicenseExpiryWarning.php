<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpiryWarning extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public License $license,
        public int $daysUntilExpiry
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $renewalUrl = route('license.renewal', ['license' => $this->license->key]);

        $licenseName = $this->license->name ?: $this->license->policy_name;

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hi {$notifiable->name},")
            ->line($this->getMainMessage())
            ->line("**License:** {$licenseName}")
            ->line("**License Key:** {$this->license->key}")
            ->line("**Expires:** {$this->license->expires_at->format('F j, Y \\a\\t g:i A T')}")
            ->line('To ensure uninterrupted access to NativePHP, you need to set up a subscription for automatic renewal.')
            ->line('**Good news:** As an early adopter, you qualify for our Early Access Pricing - the same great rates you enjoyed when you first purchased!')
            ->action('Renew Your License', $renewalUrl)
            ->line('If you have any questions about your renewal, please don\'t hesitate to contact our support team.')
            ->salutation("Best regards,\nThe NativePHP Team");
    }

    private function getSubject(): string
    {
        return match ($this->daysUntilExpiry) {
            30 => 'Your NativePHP License Expires in 30 Days',
            7 => 'Important: Your NativePHP License Expires in 7 Days',
            1 => 'Urgent: Your NativePHP License Expires Tomorrow',
            0 => 'Your NativePHP License Expires Today',
            default => "Your NativePHP License Expires in {$this->daysUntilExpiry} Days",
        };
    }

    private function getMainMessage(): string
    {
        return match ($this->daysUntilExpiry) {
            30 => 'This is a friendly reminder that your NativePHP license will expire in 30 days.',
            7 => 'Your NativePHP license will expire in just 7 days. This is an important reminder to set up your renewal.',
            1 => 'Your NativePHP license expires tomorrow! Please take immediate action to avoid interruption.',
            0 => 'Your NativePHP license expires today. Renew now to maintain access.',
            default => "Your NativePHP license will expire in {$this->daysUntilExpiry} days.",
        };
    }
}
