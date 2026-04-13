<?php

namespace App\Jobs;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginAvailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendNewPluginNotifications implements ShouldQueue
{
    use Queueable;

    public function __construct(public Plugin $plugin) {}

    public function handle(): void
    {
        $recipients = User::query()
            ->where('receives_new_plugin_notifications', true)
            ->where('id', '!=', $this->plugin->user_id)
            ->get();

        Notification::send($recipients, new NewPluginAvailable($this->plugin));
    }
}
