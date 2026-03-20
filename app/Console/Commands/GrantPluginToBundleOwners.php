<?php

namespace App\Console\Commands;

use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\User;
use App\Notifications\BundlePluginAdded;
use Illuminate\Console\Command;

class GrantPluginToBundleOwners extends Command
{
    protected $signature = 'plugins:grant-to-bundle-owners
        {bundle : The bundle slug}
        {plugin : The plugin name (vendor/package)}
        {--dry-run : Preview what would happen without making changes}
        {--no-email : Grant the plugin without sending notification emails}';

    protected $description = 'Grant a plugin to all users who have purchased a specific bundle and notify them via email';

    public function handle(): int
    {
        $bundle = PluginBundle::where('slug', $this->argument('bundle'))->first();

        if (! $bundle) {
            $this->error("Bundle not found: {$this->argument('bundle')}");

            return Command::FAILURE;
        }

        $plugin = Plugin::where('name', $this->argument('plugin'))->first();

        if (! $plugin) {
            $this->error("Plugin not found: {$this->argument('plugin')}");

            return Command::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $noEmail = $this->option('no-email');

        // Find all unique users who have purchased this bundle
        // (they have at least one active PluginLicense linked to this bundle)
        $userIds = PluginLicense::where('plugin_bundle_id', $bundle->id)
            ->active()
            ->distinct()
            ->pluck('user_id');

        $users = User::whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            $this->warn('No users found who have purchased this bundle.');

            return Command::SUCCESS;
        }

        $this->info("Bundle: {$bundle->name} (slug: {$bundle->slug})");
        $this->info("Plugin: {$plugin->name}");
        $this->info("Users found: {$users->count()}");

        if ($dryRun) {
            $this->warn('[DRY RUN] No changes will be made.');
        }

        $this->newLine();

        $granted = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Check if user already has an active license for this plugin
            $existingLicense = PluginLicense::where('user_id', $user->id)
                ->where('plugin_id', $plugin->id)
                ->active()
                ->exists();

            if ($existingLicense) {
                $this->line("  Skipped {$user->email} — already has an active license");
                $skipped++;

                continue;
            }

            if (! $dryRun) {
                PluginLicense::create([
                    'user_id' => $user->id,
                    'plugin_id' => $plugin->id,
                    'plugin_bundle_id' => $bundle->id,
                    'price_paid' => 0,
                    'currency' => 'USD',
                    'is_grandfathered' => true,
                    'purchased_at' => now(),
                ]);

                if (! $noEmail) {
                    $user->notify(
                        (new BundlePluginAdded($plugin, $bundle))
                            ->delay(now()->addSeconds($granted * 2))
                    );
                }
            }

            $this->line("  Granted to {$user->email}");
            $granted++;
        }

        $this->newLine();
        $this->info("Granted: {$granted}");
        $this->info("Skipped (already licensed): {$skipped}");

        if ($dryRun) {
            $this->warn('This was a dry run. Run again without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }
}
