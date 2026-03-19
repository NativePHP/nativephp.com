<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LegacyLicenseThankYou extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public License $license
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $renewalUrl = route('license.renewal', ['license' => $this->license->key]);
        $starterKitUrl = 'https://nativephp.com/bundles/starter-kit';
        $blogPostUrl = 'https://nativephp.com/blog/nativephp-for-mobile-is-now-free';

        $firstName = $notifiable->name ? explode(' ', $notifiable->name)[0] : null;
        $greeting = $firstName ? "Hi {$firstName}," : 'Hi there,';

        return (new MailMessage)
            ->subject('Thank You for Making NativePHP Mobile Free')
            ->greeting($greeting)
            ->line('We have some exciting news to share with you.')
            ->line('**NativePHP for Mobile is now free for everyone** - and it\'s thanks to early supporters like you that this was possible. Your purchase helped fund the development that made this happen, and we\'re incredibly grateful.')
            ->line('[Read the full announcement]('.$blogPostUrl.')')
            ->line('---')
            ->line('**Renew & Claim Free Premium Plugins**')
            ->line('As a thank you, if you set up a subscription now, you can claim our premium plugins completely free. This offer is available until June 1st.')
            ->line('Your continued subscription will help fund ongoing development and unlock even more benefits in the future - we\'re still finalizing the details, but subscribers will be first in line.')
            ->action('Set Up Subscription & Claim Free Plugins', $renewalUrl)
            ->line('---')
            ->line('**Not Ready to Subscribe?**')
            ->line('No problem! As an Early Access Program member (a status you\'ll always keep), you can claim a significant discount on our Premium Plugins Starter Kit:')
            ->line('- **50% off** the listed bundle price')
            ->line('- **Almost 70% off** compared to buying the plugins separately')
            ->line('[Get the Starter Kit]('.$starterKitUrl.')')
            ->line('---')
            ->line('Thank you again for believing in NativePHP from the early days. We couldn\'t have done this without you.')
            ->salutation("With gratitude,\n\nThe NativePHP Team");
    }
}
