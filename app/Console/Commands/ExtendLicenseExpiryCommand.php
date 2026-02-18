<?php

namespace App\Console\Commands;

use App\Models\License;
use Illuminate\Console\Command;

class ExtendLicenseExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licenses:renew {license_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extend the expiry date of a license';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $licenseId = $this->argument('license_id');

        // Find the license
        $license = License::find($licenseId);
        if (! $license) {
            $this->error("License with ID {$licenseId} not found");

            return Command::FAILURE;
        }

        // Dispatch the job to update the license expiry
        dispatch(new \App\Jobs\UpdateAnystackLicenseExpiryJob($license));

        $this->info("License expiry updated to {$license->expires_at->format('Y-m-d')}");

        return Command::SUCCESS;
    }
}
