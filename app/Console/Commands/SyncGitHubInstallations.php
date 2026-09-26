<?php

namespace App\Console\Commands;

use App\Models\GitHubInstallation;
use App\Services\GitHubAppService;
use App\Services\GitHubUserService;
use Illuminate\Console\Command;

class SyncGitHubInstallations extends Command
{
    protected $signature = 'github:sync-installations';

    protected $description = 'Refresh every GitHub App installation from GitHub, catching any installation webhooks we missed';

    public function handle(GitHubAppService $appService): int
    {
        $synced = 0;
        $removed = 0;
        $failed = 0;

        GitHubInstallation::query()->with('user')->chunkById(100, function ($installations) use ($appService, &$synced, &$removed, &$failed): void {
            foreach ($installations as $installation) {
                if ($appService->syncInstallation($installation)) {
                    $synced++;
                } elseif (! $installation->exists) {
                    $removed++;
                    $this->line("Removed installation {$installation->installation_id} ({$installation->account_login}), GitHub no longer has it");
                } else {
                    $failed++;
                    $this->error("Failed to sync installation {$installation->installation_id} ({$installation->account_login})");
                }

                if ($installation->user) {
                    GitHubUserService::for($installation->user)->clearRepositoryCache();
                }
            }
        });

        $this->info("Synced: {$synced}, removed: {$removed}, failed: {$failed}");

        return self::SUCCESS;
    }
}
