<?php

namespace App\Listeners;

use App\Contracts\TransactionalNotification;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
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

        // Transactional notifications (account recovery, verification,
        // purchase receipts, entitlement grants) must always be delivered.
        // Framework notifications can't implement our marker, so they're
        // listed explicitly.
        if ($event->notification instanceof TransactionalNotification
            || $event->notification instanceof VerifyEmail
            || $event->notification instanceof ResetPassword) {
            return true;
        }

        if (! $event->notifiable->email_verified_at) {
            return false;
        }

        return (bool) $event->notifiable->receives_notification_emails;
    }
}
