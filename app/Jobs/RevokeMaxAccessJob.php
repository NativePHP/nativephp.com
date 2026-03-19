<?php

namespace App\Jobs;

use App\Models\User;
use App\Support\DiscordApi;
use App\Support\GitHubOAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RevokeMaxAccessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public string $email) {}

    public function handle(): void
    {
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            Log::info('Skipping access revocation - no user found for email', [
                'email' => $this->email,
            ]);

            return;
        }

        // Check if user still has Max access through another license or sub-license
        if ($user->hasMaxAccess()) {
            Log::info('Skipping access revocation - user still has Max access', [
                'user_id' => $user->id,
                'email' => $this->email,
            ]);

            return;
        }

        // Revoke GitHub access
        $this->revokeGitHubAccess($user);

        // Revoke Discord role
        $this->revokeDiscordRole($user);
    }

    private function revokeGitHubAccess(User $user): void
    {
        if (! $user->github_username || ! $user->mobile_repo_access_granted_at) {
            return;
        }

        $github = GitHubOAuth::make();
        $success = $github->removeFromMobileRepo($user->github_username);

        if ($success) {
            $user->update(['mobile_repo_access_granted_at' => null]);

            Log::info('GitHub access revoked for user', [
                'user_id' => $user->id,
                'github_username' => $user->github_username,
            ]);
        } else {
            Log::warning('Failed to revoke GitHub access for user', [
                'user_id' => $user->id,
                'github_username' => $user->github_username,
            ]);
        }
    }

    private function revokeDiscordRole(User $user): void
    {
        if (! $user->discord_id || ! $user->discord_role_granted_at) {
            return;
        }

        $discord = DiscordApi::make();
        $success = $discord->removeMaxRole($user->discord_id);

        if ($success) {
            $user->update(['discord_role_granted_at' => null]);

            Log::info('Discord Max role revoked for user', [
                'user_id' => $user->id,
                'discord_id' => $user->discord_id,
            ]);
        } else {
            Log::warning('Failed to revoke Discord Max role for user', [
                'user_id' => $user->id,
                'discord_id' => $user->discord_id,
            ]);
        }
    }
}
