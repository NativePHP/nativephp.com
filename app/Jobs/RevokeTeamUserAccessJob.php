<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use App\Support\GitHubOAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RevokeTeamUserAccessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        $pluginDevKit = Product::where('slug', 'plugin-dev-kit')->first();

        if (! $pluginDevKit) {
            return;
        }

        // If user still has access via direct license or another team, skip
        if ($user->productLicenses()->forProduct($pluginDevKit)->exists()) {
            return;
        }

        if ($user->isUltraTeamMember()) {
            return;
        }

        if (! $user->github_username || ! $user->claude_plugins_repo_access_granted_at) {
            return;
        }

        $github = GitHubOAuth::make();
        $github->removeFromClaudePluginsRepo($user->github_username);

        $user->update(['claude_plugins_repo_access_granted_at' => null]);

        Log::info('Revoked claude-plugins repo access for removed team member', [
            'user_id' => $user->id,
        ]);
    }
}
