<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Laravel\Cashier\Subscription;

class MarkCompedSubscriptions extends Command
{
    protected $signature = 'subscriptions:mark-comped
        {file : Path to a CSV file containing email addresses (one per line or in an "email" column)}';

    protected $description = 'Mark subscriptions as comped for email addresses in a CSV file';

    public function handle(): int
    {
        $path = $this->argument('file');

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $emails = $this->parseEmails($path);

        if (empty($emails)) {
            $this->error('No valid email addresses found in the file.');

            return self::FAILURE;
        }

        $this->info('Found '.count($emails).' email(s) to process.');

        $updated = 0;
        $skipped = [];

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $skipped[] = "{$email} — user not found";

                continue;
            }

            $subscription = Subscription::where('user_id', $user->id)
                ->where('stripe_status', 'active')
                ->first();

            if (! $subscription) {
                $skipped[] = "{$email} — no active subscription";

                continue;
            }

            if ($subscription->is_comped) {
                $skipped[] = "{$email} — already marked as comped";

                continue;
            }

            $subscription->update(['is_comped' => true]);
            $updated++;
            $this->info("Marked {$email} as comped (subscription #{$subscription->id})");
        }

        if (count($skipped) > 0) {
            $this->warn('Skipped:');
            foreach ($skipped as $reason) {
                $this->warn("  - {$reason}");
            }
        }

        $this->info("Done. {$updated} subscription(s) marked as comped.");

        return self::SUCCESS;
    }

    /**
     * Parse email addresses from a CSV file.
     * Supports: plain list (one email per line), or CSV with an "email" column header.
     *
     * @return array<string>
     */
    private function parseEmails(string $path): array
    {
        $handle = fopen($path, 'r');

        if (! $handle) {
            return [];
        }

        $emails = [];
        $emailColumnIndex = null;
        $isFirstRow = true;

        while (($row = fgetcsv($handle)) !== false) {
            if ($isFirstRow) {
                $isFirstRow = false;
                $headers = array_map(fn ($h) => strtolower(trim($h)), $row);
                $emailColumnIndex = array_search('email', $headers);

                // If the first row looks like an email itself (no header), treat it as data
                if ($emailColumnIndex === false && filter_var(trim($row[0]), FILTER_VALIDATE_EMAIL)) {
                    $emailColumnIndex = 0;
                    $emails[] = strtolower(trim($row[0]));
                }

                continue;
            }

            $value = trim($row[$emailColumnIndex] ?? '');

            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($value);
            }
        }

        fclose($handle);

        return array_unique($emails);
    }
}
