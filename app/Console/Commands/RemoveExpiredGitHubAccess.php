<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\GitHubOAuth;
use Illuminate\Console\Command;

class RemoveExpiredGitHubAccess extends Command
{
    protected $signature = 'github:remove-expired-access';

    protected $description = 'Remove GitHub repository access for users whose Max licenses have expired';

    public function handle(): int
    {
        $github = GitHubOAuth::make();
        $removed = 0;

        // Find users with GitHub access granted
        $users = User::query()
            ->whereNotNull('mobile_repo_access_granted_at')
            ->whereNotNull('github_username')
            ->get();

        foreach ($users as $user) {
            // Check if user still has an active Max license
            if (! $user->hasActiveMaxLicense()) {
                // Remove from repository
                $success = $github->removeFromMobileRepo($user->github_username);

                if ($success) {
                    // Clear the access timestamp
                    $user->update([
                        'mobile_repo_access_granted_at' => null,
                    ]);

                    $this->info("Removed access for user: {$user->email} (@{$user->github_username})");
                    $removed++;
                } else {
                    $this->error("Failed to remove access for user: {$user->email} (@{$user->github_username})");
                }
            }
        }

        $this->info("Total users with access removed: {$removed}");

        return Command::SUCCESS;
    }
}
