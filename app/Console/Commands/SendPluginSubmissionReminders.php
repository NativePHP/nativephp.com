<?php

namespace App\Console\Commands;

use App\Enums\PluginStatus;
use App\Models\User;
use App\Notifications\PluginSubmissionReminder;
use Illuminate\Console\Command;

class SendPluginSubmissionReminders extends Command
{
    protected $signature = 'plugins:send-submission-reminders
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send a reminder to users with unapproved plugin submissions to finalize their configuration';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - No notifications will be sent');
        }

        $users = User::query()
            ->whereNotNull('email_verified_at')
            ->whereHas('plugins', function ($query) {
                $query->whereIn('status', [PluginStatus::Draft, PluginStatus::Pending, PluginStatus::Rejected]);
            })
            ->with(['plugins' => function ($query) {
                $query->whereIn('status', [PluginStatus::Draft, PluginStatus::Pending, PluginStatus::Rejected])
                    ->orderBy('name');
            }])
            ->get();

        $this->info("Found {$users->count()} user(s) with unapproved plugins");

        $sent = 0;

        foreach ($users as $user) {
            $pluginNames = $user->plugins->pluck('name')->join(', ');

            if ($dryRun) {
                $this->line("Would send to: {$user->email} ({$user->plugins->count()} plugin(s): {$pluginNames})");
            } else {
                $user->notify(new PluginSubmissionReminder($user->plugins));
                $this->line("Sent to: {$user->email} ({$user->plugins->count()} plugin(s): {$pluginNames})");
            }

            $sent++;
        }

        $this->newLine();
        $this->info($dryRun ? "Would send: {$sent} notification(s)" : "Sent: {$sent} notification(s)");

        return Command::SUCCESS;
    }
}
