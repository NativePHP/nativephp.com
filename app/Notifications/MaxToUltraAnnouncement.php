<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaxToUltraAnnouncement extends Notification implements ShouldQueue
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
            ->subject('Your Max Plan is Now NativePHP Ultra')
            ->greeting($greeting)
            ->line('We have some exciting news: **your Max plan has been upgraded to NativePHP Ultra** - at no extra cost.')
            ->line('Here\'s what you now get as an Ultra subscriber:')
            ->line('- **Teams** - invite up to 10 collaborators to share your plugin access')
            ->line('- **Free official plugins** - every NativePHP-published plugin, included with your subscription')
            ->line('- **Plugin Dev Kit** - tools and resources to build and publish your own plugins')
            ->line('- **90% Marketplace revenue** - keep up to 90% of earnings on paid plugins you publish')
            ->line('- **Priority support** - get help faster when you need it')
            ->line('- **Early access** - be first to try new features and plugins')
            ->line('- **Exclusive content** - tutorials, guides, and deep dives just for Ultra members')
            ->line('- **Shape the roadmap** - your feedback directly influences what we build next')
            ->line('---')
            ->line('**Nothing changes on your end.** Your billing stays exactly the same - you just get more.')
            ->action('See All Ultra Benefits', route('pricing'))
            ->salutation("Cheers,\n\nThe NativePHP Team");
    }
}
