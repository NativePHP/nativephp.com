<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

class MatchUsersWithStripeCustomers extends Command
{
    protected $signature = 'users:match-stripe-customers
                            {--limit= : Limit the number of users to process}
                            {--dry-run : Show what would be updated without making changes}';

    protected $description = 'Match users without Stripe IDs to their Stripe customer records';

    public function handle(): int
    {
        $query = User::whereNull('stripe_id');

        $totalUsers = $query->count();

        if ($totalUsers === 0) {
            $this->info('No users found without Stripe IDs.');

            return self::SUCCESS;
        }

        $this->info("Found {$totalUsers} users without Stripe IDs.");

        $limit = $this->option('limit');
        if ($limit) {
            $query->limit((int) $limit);
            $this->info("Processing first {$limit} users...");
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $users = $query->get();

        $matched = 0;
        $notFound = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        /** @var User $user */
        foreach ($users as $user) {
            try {
                /** @var Customer $customer */
                $customer = $user->findStripeCustomerRecords()->first(fn (Customer $result) => $result->next_invoice_sequence === 1);

                if ($customer) {
                    $matched++;

                    if (! $dryRun) {
                        $user->update(['stripe_id' => $customer->id]);
                    }

                    $this->newLine();
                    $this->line("  ✓ Matched: {$user->email} → {$customer->id}");
                } else {
                    $notFound++;
                    $this->newLine();
                    $this->line("  - No match: {$user->email}");
                }
            } catch (ApiErrorException $e) {
                $errors++;
                $this->newLine();
                $this->error("  ✗ Error for {$user->email}: {$e->getMessage()}");
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("  ✗ Unexpected error for {$user->email}: {$e->getMessage()}");
            }

            $progressBar->advance();

            // Add a small delay to avoid rate limiting
            \Illuminate\Support\Sleep::usleep(100000); // 0.1 seconds
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Matched', $matched],
                ['Not Found', $notFound],
                ['Errors', $errors],
                ['Total Processed', $users->count()],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        }

        return self::SUCCESS;
    }
}
