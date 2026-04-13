<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Events\NotificationSending;

class SuppressMailNotificationListener
{
    public function handle(NotificationSending $event): bool
    {
        if ($event->channel !== 'mail') {
            return true;
        }

        if (! $event->notifiable instanceof User) {
            return true;
        }

        // System notifications like email verification should always be sent
        if ($event->notification instanceof VerifyEmail) {
            return true;
        }

        return (bool) $event->notifiable->receives_notification_emails;
    }
}
