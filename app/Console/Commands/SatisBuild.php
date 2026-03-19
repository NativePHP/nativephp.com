<?php

namespace App\Console\Commands;

use App\Services\SatisService;
use Illuminate\Console\Command;

class SatisBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'satis:build
                            {--plugin= : Build only a specific plugin by name (e.g., vendor/package)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger a Satis repository build';

    /**
     * Execute the console command.
     */
    public function handle(SatisService $satisService): int
    {
        $pluginName = $this->option('plugin');

        if ($pluginName) {
            $plugin = \App\Models\Plugin::where('name', $pluginName)->first();

            if (! $plugin) {
                $this->error("Plugin '{$pluginName}' not found.");

                return self::FAILURE;
            }

            if (! $plugin->isApproved()) {
                $this->error("Plugin '{$pluginName}' is not approved.");

                return self::FAILURE;
            }

            $this->info("Triggering Satis build for: {$pluginName}");
            $result = $satisService->build([$plugin]);
        } else {
            $this->info('Triggering Satis build for all approved plugins...');
            $result = $satisService->buildAll();
        }

        if ($result['success']) {
            $this->info('Build triggered successfully!');
            $this->line("Job ID: {$result['job_id']}");

            if (isset($result['plugins_count'])) {
                $this->line("Plugins: {$result['plugins_count']}");
            }

            return self::SUCCESS;
        }

        $this->error('Build trigger failed: '.$result['error']);

        if (isset($result['status'])) {
            $this->line("HTTP Status: {$result['status']}");
        }

        $this->line('API URL: '.config('services.satis.url'));
        $this->line('API Key configured: '.(config('services.satis.api_key') ? 'Yes' : 'No'));

        return self::FAILURE;
    }
}
