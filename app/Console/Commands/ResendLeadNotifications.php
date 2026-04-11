<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Notifications\NewLeadSubmitted;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class ResendLeadNotifications extends Command
{
    protected $signature = 'app:resend-lead-notifications {date : The date to resend leads from (Y-m-d)}';

    protected $description = 'Re-send lead notifications to the sales email for leads submitted on or after a given date.';

    public function handle(): void
    {
        $date = Carbon::createFromFormat('Y-m-d', $this->argument('date'))->startOfDay();

        $leads = Lead::query()
            ->where('created_at', '>=', $date)
            ->orderBy('created_at')
            ->get();

        if ($leads->isEmpty()) {
            $this->info('No leads found from '.$date->toDateString().' onwards.');

            return;
        }

        $this->info("Found {$leads->count()} lead(s) from {$date->toDateString()} onwards.");

        foreach ($leads as $lead) {
            Notification::route('mail', 'sales@nativephp.com')
                ->notify(new NewLeadSubmitted($lead));

            $this->line("Sent notification for lead: {$lead->company} ({$lead->email})");
        }

        $this->info('Done.');
    }
}
