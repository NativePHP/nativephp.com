<?php

namespace App\Console\Commands;

use App\Models\Plugin;
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
            $plugin = Plugin::with('user')->where('name', $pluginName)->first();

            if (! $plugin) {
                $this->error("Plugin '{$pluginName}' not found.");

                return self::FAILURE;
            }

            if (! $plugin->isApproved()) {
                $this->error("Plugin '{$pluginName}' is not approved.");

                return self::FAILURE;
            }

            $this->info("Triggering Satis build for: {$pluginName}");
            $result = $satisService->buildForPlugin($plugin);

            if ($result['success'] ?? false) {
                $this->info('Build triggered successfully!');
                $this->line("Job ID: {$result['job_id']}");

                return self::SUCCESS;
            }

            $this->error('Build trigger failed: '.($result['error'] ?? 'Unknown error'));

            if (isset($result['status'])) {
                $this->line("HTTP Status: {$result['status']}");
            }

            $this->line('API URL: '.config('services.satis.url'));
            $this->line('API Key configured: '.(config('services.satis.api_key') ? 'Yes' : 'No'));

            return self::FAILURE;
        }

        $this->info('Triggering individual Satis builds for all approved paid plugins...');
        $result = $satisService->buildAll();

        $count = $result['plugins_count'] ?? 0;
        $failed = $result['failed'] ?? [];

        if ($count === 0) {
            $this->warn($result['error'] ?? 'No plugins to build.');

            return self::SUCCESS;
        }

        $this->line('Dispatched '.($count - count($failed))."/{$count} plugin build(s).");

        if (! empty($failed)) {
            $this->error('Failed to dispatch: '.implode(', ', $failed));

            return self::FAILURE;
        }

        $this->info('All builds triggered successfully!');

        return self::SUCCESS;
    }
}
