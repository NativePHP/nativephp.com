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

        return $event->notifiable->receives_notification_emails;
    }
}
