<?php

namespace App\Jobs;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginsAvailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPendingPluginEmailDigest implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $plugins = Plugin::query()->pendingNewPluginNotification()->get();

        if ($plugins->isEmpty()) {
            return;
        }

        $recipients = User::query()
            ->whereNotNull('email_verified_at')
            ->where('receives_new_plugin_notifications', true)
            ->get();

        foreach ($recipients as $recipient) {
            $notifiablePlugins = $plugins->reject(fn (Plugin $plugin) => $plugin->user_id === $recipient->id);

            if ($notifiablePlugins->isEmpty()) {
                continue;
            }

            $recipient->notify(new NewPluginsAvailable($notifiablePlugins));
        }

        Plugin::query()
            ->whereIn('id', $plugins->pluck('id'))
            ->update(['new_plugin_notified_at' => now()]);
    }
}
