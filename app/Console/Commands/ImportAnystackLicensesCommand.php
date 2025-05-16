<?php

namespace App\Console\Commands;

use App\Jobs\ImportAnystackLicenses;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ImportAnystackLicensesCommand extends Command
{
    protected $signature = 'app:import-anystack-licenses';

    protected $description = 'Import existing license data from Anystack.';

    public function handle(): void
    {
        Bus::batch([
            new ImportAnystackLicenses(1),
        ])
            ->name('import-anystack-licenses')
            ->dispatch();
    }
}
