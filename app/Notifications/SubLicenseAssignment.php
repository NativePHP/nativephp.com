<?php

namespace App\Notifications;

use App\Models\SubLicense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubLicenseAssignment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SubLicense $subLicense
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
            ->subject('Your NativePHP License Key')
            ->greeting('Hello!')
            ->line('You have been assigned a NativePHP license key.')
            ->line('**License Key:** `'.$this->subLicense->key.'`')
            ->action('View Documentation', 'https://nativephp.com/docs/mobile/getting-started/installation')
            ->line('If you have any questions, feel free to reach out to our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sub_license_id' => $this->subLicense->id,
            'license_key' => $this->subLicense->key,
            'assigned_email' => $this->subLicense->assigned_email,
        ];
    }
}
