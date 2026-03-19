<?php

namespace App\Console\Commands;

use App\Jobs\ImportAnystackContacts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ImportAnystackContactsCommand extends Command
{
    protected $signature = 'app:import-anystack-contacts';

    protected $description = 'Import existing contact data from Anystack.';

    public function handle(): void
    {
        Bus::batch([
            new ImportAnystackContacts(1),
        ])
            ->name('import-anystack-contacts')
            ->dispatch();
    }
}
