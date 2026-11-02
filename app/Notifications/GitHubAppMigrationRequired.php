<?php

namespace App\Notifications;

use App\Services\GitHubAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GitHubAppMigrationRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $name = $notifiable->name ?? null;
        $firstName = $name ? explode(' ', $name)[0] : null;
        $cutoffDate = app(GitHubAppService::class)->legacyOAuthCutoffDate();
        $deadline = $cutoffDate ? $cutoffDate->format('j F Y') : 'we switch off the old connection';

        return (new MailMessage)
            ->subject('A security improvement to how NativePHP connects to GitHub')
            ->greeting($firstName ? "Hi {$firstName}," : 'Hi there,')
            ->line('We\'re moving away from GitHub OAuth on nativephp.com, which gave us broad access to your repositories, to a GitHub App that gives you fine-grained control over what we can see.')
            ->line('## How does this affect you?')
            ->line('**If you use "Login with GitHub"**, you don\'t need to do anything. The difference is that the credentials we use no longer grant us access to any of your repositories. We simply use GitHub to confirm your identity.')
            ->line("**If you are a plugin author**, you need to connect our new GitHub App from your dashboard. If you don't do it before {$deadline}, we won't be able to keep your plugins up to date and may remove them from the Marketplace.")
            ->action('Connect the GitHub App', route('customer.integrations'))
            ->line('This does not affect [Bifrost](https://bifrost.nativephp.com), which already uses its own GitHub App with fine-grained access to only the repositories you explicitly allow.')
            ->line('Bifrost is the fastest way to ship native apps with AI and we have 30% off annual plans until the end of the year!')
            ->salutation("Cheers,\n\nThe NativePHP Team");
    }
}
