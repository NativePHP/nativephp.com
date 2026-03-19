<?php

namespace App\Console\Commands;

use App\Models\Plugin;
use Illuminate\Console\Command;

class SyncPlugins extends Command
{
    protected $signature = 'plugins:sync';

    protected $description = 'Dispatch jobs to sync all plugins from their repositories';

    public function handle(): int
    {
        $plugins = Plugin::all();

        $count = $plugins->count();

        if ($count === 0) {
            $this->info('No plugins to sync.');

            return self::SUCCESS;
        }

        $this->info("Dispatching sync jobs for {$count} plugins...");

        $plugins->each(fn (Plugin $plugin) => dispatch(new \App\Jobs\SyncPlugin($plugin)));

        $this->info('Done. Jobs have been dispatched to the queue.');

        return self::SUCCESS;
    }
}
