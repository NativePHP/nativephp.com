<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GeneratePluginLicenseKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugins:generate-license-keys
                            {--force : Regenerate keys for all users, even those who already have one}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate plugin license keys for all users who do not have one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');

        $query = User::query();

        if (! $force) {
            $query->whereNull('plugin_license_key');
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('All users already have plugin license keys.');

            return self::SUCCESS;
        }

        $this->info("Generating plugin license keys for {$count} users...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunkById(100, function ($users) use ($bar): void {
            foreach ($users as $user) {
                $user->getPluginLicenseKey();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        $this->info("Generated plugin license keys for {$count} users.");

        return self::SUCCESS;
    }
}
