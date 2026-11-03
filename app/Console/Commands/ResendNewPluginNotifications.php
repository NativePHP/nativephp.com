<?php

namespace App\Console\Commands;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\NewPluginsAvailable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ResendNewPluginNotifications extends Command
{
    protected $signature = 'plugins:resend-new-plugin-notifications
        {plugins* : Plugin names (vendor/package) to resend notifications for}
        {--dry-run : Preview what would happen without sending notifications}';

    protected $description = 'Email opted-in users a single combined digest for the specified plugins';

    public function handle(): int
    {
        $pluginNames = $this->argument('plugins');
        $dryRun = $this->option('dry-run');

        $plugins = Plugin::query()
            ->whereIn('name', $pluginNames)
            ->where('status', 'approved')
            ->get();

        $missingNames = collect($pluginNames)->diff($plugins->pluck('name'));

        if ($missingNames->isNotEmpty()) {
            foreach ($missingNames as $name) {
                $this->error("Plugin not found or not approved: {$name}");
            }

            return Command::FAILURE;
        }

        $recipients = User::query()
            ->whereNotNull('email_verified_at')
            ->where('receives_new_plugin_notifications', true)
            ->whereNotIn('id', $plugins->pluck('user_id'))
            ->get();

        if ($recipients->isEmpty()) {
            $this->warn('No opted-in users found to notify.');

            return Command::SUCCESS;
        }

        $this->info("Plugins: {$plugins->pluck('name')->implode(', ')}");
        $this->info("Recipients: {$recipients->count()} opted-in users");

        if ($dryRun) {
            $this->warn('[DRY RUN] No notifications will be sent.');

            return Command::SUCCESS;
        }

        Notification::send($recipients, new NewPluginsAvailable($plugins));

        $plugins->each->update(['new_plugin_notified_at' => now()]);

        $this->info("Sent a single digest covering {$plugins->count()} plugin(s) to {$recipients->count()} users.");

        $this->newLine();
        $this->info('Done. All notifications queued.');

        return Command::SUCCESS;
    }
}
