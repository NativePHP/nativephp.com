<?php

namespace App\Listeners;

use App\Models\User;
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

        if (! $event->notifiable->email_verified_at) {
            return false;
        }

        return $event->notifiable->receives_notification_emails;
    }
}
