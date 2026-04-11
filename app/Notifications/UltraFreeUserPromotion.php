<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UltraFreeUserPromotion extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = $notifiable->name ? explode(' ', $notifiable->name)[0] : null;
        $greeting = $firstName ? "Hi {$firstName}," : 'Hi there,';

        return (new MailMessage)
            ->subject('NativePHP is Free — And Ultra Takes It Further')
            ->greeting($greeting)
            ->line('We wanted to make sure you heard the news: **[NativePHP for Mobile is now completely free and open source!](https://nativephp.com/blog/nativephp-for-mobile-is-now-free)**')
            ->line('That means you can build native iOS and Android apps with Laravel and PHP — no license required. Just install and go.')
            ->line('But if you want to take things to the next level, **NativePHP Ultra** gives you some incredible benefits:')
            ->line('- **Teams** - up to 5 seats (you + 4 collaborators) to share your plugin access')
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
