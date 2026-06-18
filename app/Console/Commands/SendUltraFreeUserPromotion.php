<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\UltraFreeUserPromotion;
use Illuminate\Console\Command;

class SendUltraFreeUserPromotion extends Command
{
    protected $signature = 'ultra:send-free-user-promo
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send a promotional email to users who signed up but never purchased a license or subscription, encouraging them to try Ultra';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - No emails will be sent');
        }

        $users = User::query()
            ->whereNotNull('email_verified_at')
            ->whereDoesntHave('licenses')
            ->whereDoesntHave('subscriptions')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            if ($dryRun) {
                $this->line("Would send to: {$user->email}");
            } else {
                $user->notify(new UltraFreeUserPromotion);
                $this->line("Sent to: {$user->email}");
            }

            $sent++;
        }

        $this->newLine();
        $this->info("Found {$sent} eligible user(s)");
        $this->info($dryRun ? "Would send: {$sent} email(s)" : "Sent: {$sent} email(s)");

        return Command::SUCCESS;
    }
}
