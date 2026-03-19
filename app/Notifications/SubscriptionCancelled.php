<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $resubscribeUrl = route('pricing');
        $blogPostUrl = 'https://nativephp.com/blog/nativephp-for-mobile-is-now-free';

        $firstName = $notifiable->name ? explode(' ', $notifiable->name)[0] : null;
        $greeting = $firstName ? "Hi {$firstName}," : 'Hi there,';

        return (new MailMessage)
            ->subject('We\'re Sorry to See You Go')
            ->greeting($greeting)
            ->line('We noticed you\'ve cancelled your NativePHP subscription, and we wanted to reach out personally.')
            ->line('**Thank you** for supporting NativePHP. Your contribution has helped us build something amazing - including making [NativePHP for Mobile free for everyone]('.$blogPostUrl.').')
            ->line('We understand circumstances change, and we respect your decision. But if there\'s anything we could have done better, we\'d love to hear from you.')
            ->line('---')
            ->line('**The door is always open**')
            ->line('If you ever want to come back, your support helps us:')
            ->line('- Continue developing new features')
            ->line('- Maintain and improve the framework')
            ->line('- Keep NativePHP free for the community')
            ->line('Subscribers also get access to exclusive benefits and premium plugins.')
            ->action('Resubscribe & Support NativePHP', $resubscribeUrl)
            ->line('---')
            ->line('Whatever you decide, thank you for being part of the NativePHP journey. We hope to see you again.')
            ->salutation("With gratitude,\n\nThe NativePHP Team");
    }
}
