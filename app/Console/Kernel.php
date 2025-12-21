<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send license expiry warnings daily at 9 AM UTC
        $schedule->command('licenses:send-expiry-warnings')
            ->dailyAt('09:00')
            ->onOneServer()
            ->runInBackground();

        // Remove GitHub access for users with expired Max licenses
        $schedule->command('github:remove-expired-access')
            ->dailyAt('10:00')
            ->onOneServer()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
