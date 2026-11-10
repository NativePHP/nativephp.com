<?php

namespace App\Console\Commands;

use App\Support\GitHub;
use Illuminate\Console\Command;

class SyncGitHubRepositoryStars extends Command
{
    protected $signature = 'github:sync-repo-stars';

    protected $description = 'Fetch and cache the GitHub star counts shown in the site header for the mobile-air and desktop repositories';

    public function handle(): int
    {
        $failed = false;

        foreach ([GitHub::mobileAir(), GitHub::desktop()] as $repository) {
            $stars = $repository->refreshStars();

            if ($stars === null) {
                $failed = true;
                $this->error("Failed to fetch stars for {$repository->package}, keeping the last cached count");

                continue;
            }

            $this->info("{$repository->package}: {$stars} stars");
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
