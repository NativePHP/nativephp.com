<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UltraUpgradePromotion extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $currentPlanName) {}

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
            ->line("You're currently on the **{$this->currentPlanName}** plan - and we'd love to show you what you're missing.")
            ->line('**NativePHP Ultra** gives you everything you need to build and ship faster:')
            ->line('- **Teams** - up to 5 seats (you + 4 collaborators) to share your plugin access')
            ->line('- **Free official plugins** - every NativePHP-published plugin, included with your subscription')
            ->line('- **Plugin Dev Kit** - tools and resources to build and publish your own plugins')
            ->line('- **90% Marketplace revenue** - keep up to 90% of earnings on paid plugins you publish')
            ->line('- **Priority support** - get help faster when you need it')
            ->line('- **Early access** - be first to try new features and plugins')
            ->line('- **Exclusive content** - tutorials, guides, and deep dives just for Ultra members')
            ->line('- **Shape the roadmap** - your feedback directly influences what we build next')
            ->line('---')
            ->line('**Upgrading is seamless.** You\'ll only pay the prorated difference for the rest of your billing cycle - no double charges. Ultra is available with **annual or monthly billing**.')
            ->action('Upgrade to Ultra', route('pricing'))
            ->salutation("Cheers,\n\nThe NativePHP Team");
    }
}
