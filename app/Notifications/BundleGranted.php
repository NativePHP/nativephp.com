<?php

namespace App\Notifications;

use App\Models\Plugin;
use App\Models\PluginBundle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class BundleGranted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Plugin>  $grantedPlugins
     */
    public function __construct(
        public PluginBundle $bundle,
        public Collection $grantedPlugins,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pluginUrls = $this->grantedPlugins->mapWithKeys(function ($plugin) {
            $params = $plugin->routeParams();

            return [$plugin->id => "https://nativephp.com/plugins/{$params['vendor']}/{$params['package']}"];
        })->toArray();

        return (new MailMessage)
            ->subject("You've been granted the {$this->bundle->name} bundle!")
            ->markdown('mail.bundle-granted', [
                'bundle' => $this->bundle,
                'grantedPlugins' => $this->grantedPlugins,
                'pluginUrls' => $pluginUrls,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'bundle_id' => $this->bundle->id,
            'bundle_name' => $this->bundle->name,
            'granted_plugin_ids' => $this->grantedPlugins->pluck('id')->toArray(),
        ];
    }
}
