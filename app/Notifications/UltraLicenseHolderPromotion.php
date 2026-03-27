<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UltraLicenseHolderPromotion extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $planName) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = $notifiable->name ? explode(' ', $notifiable->name)[0] : null;
        $greeting = $firstName ? "Hi {$firstName}," : 'Hi there,';

        return (new MailMessage)
            ->subject('Unlock More with NativePHP Ultra')
            ->greeting($greeting)
            ->line("You previously purchased a **{$this->planName}** license - thank you for supporting NativePHP early on!")
            ->line('Although NativePHP for Mobile is now free and open source and doesn\'t require licenses any more, we\'ve created a subscription plan that gives you some incredible benefits:')
            ->line('- **Teams** - invite up to 10 collaborators to share your plugin access')
            ->line('- **Free official plugins** - every NativePHP-published plugin, included with your subscription')
            ->line('- **Plugin Dev Kit** - tools and resources to build and publish your own plugins')
            ->line('- **90% Marketplace revenue** - keep up to 90% of earnings on paid plugins you publish')
            ->line('- **Priority support** - get help faster when you need it')
            ->line('- **Early access** - be first to try new features and plugins')
            ->line('- **Exclusive content** - tutorials, guides, and deep dives just for Ultra members')
            ->line('- **Shape the roadmap** - your feedback directly influences what we build next')
            ->line('---')
            ->line('Ultra is available with **annual or monthly billing** - choose what works best for you.')
            ->action('See Ultra Plans', route('pricing'))
            ->salutation("Cheers,\n\nThe NativePHP Team");
    }
}
