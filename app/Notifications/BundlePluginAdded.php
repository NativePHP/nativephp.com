<?php

namespace App\Notifications;

use App\Models\Plugin;
use App\Models\PluginBundle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BundlePluginAdded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Plugin $plugin,
        public PluginBundle $bundle,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $parts = explode('/', $this->plugin->name ?? '');
        $vendor = $parts[0] ?? '';
        $package = $parts[1] ?? '';

        $pluginUrl = "https://nativephp.com/plugins/{$vendor}/{$package}";

        return (new MailMessage)
            ->subject("New plugin added to your {$this->bundle->name} bundle!")
            ->greeting('Great news!')
            ->line("We've added **{$this->plugin->name}** to the **{$this->bundle->name}** bundle — and because you already own the bundle, it's yours for free.")
            ->action('Check it out', $pluginUrl)
            ->line('Thank you for being a NativePHP customer!');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "New plugin added to your {$this->bundle->name} bundle!",
            'body' => "{$this->plugin->name} has been added to your bundle — it's yours for free.",
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
            'bundle_id' => $this->bundle->id,
            'bundle_name' => $this->bundle->name,
        ];
    }
}
