<?php

namespace App\Console\Commands;

use App\Enums\GitHubAuthType;
use App\Models\User;
use App\Services\GitHubAppService;
use Illuminate\Console\Command;

class RetireLegacyGitHubOAuth extends Command
{
    protected $signature = 'github:retire-legacy-oauth
                            {--dry-run : Show what would change without changing anything}
                            {--force : Skip the cutoff date check and confirmation}';

    protected $description = 'Clear the stored tokens from the legacy GitHub OAuth App once it has been switched off';

    public function handle(GitHubAppService $appService): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (! $force && ! $appService->legacyOAuthHasBeenRetired()) {
            $cutoffDate = $appService->legacyOAuthCutoffDate();

            $this->error($cutoffDate
                ? "The cutoff date ({$cutoffDate->format('j F Y')}) hasn't passed yet. Use --force to run it anyway."
                : 'GITHUB_LEGACY_OAUTH_CUTOFF_DATE isn\'t set. Set it, or use --force to run it anyway.');

            return self::FAILURE;
        }

        $users = User::query()
            ->where('github_auth_type', GitHubAuthType::OAuth)
            ->whereNotNull('github_token')
            ->with('plugins')
            ->get();

        $authors = $users->filter(fn (User $user): bool => $user->plugins->isNotEmpty());

        $this->info("Found {$users->count()} user(s) with a legacy OAuth token, {$authors->count()} of them plugin authors");

        if ($authors->isNotEmpty()) {
            $this->newLine();
            $this->warn('These plugin authors never connected the GitHub App. Their plugins will only sync if the repo is public:');
            $this->table(
                ['User', 'Email', 'Plugins'],
                $authors->map(fn (User $user): array => [
                    $user->id,
                    $user->email,
                    $user->plugins->pluck('name')->filter()->implode(', '),
                ])
            );
        }

        if ($dryRun) {
            $this->info('DRY RUN - No tokens were cleared');

            return self::SUCCESS;
        }

        if (! $force && ! $this->confirm("Clear {$users->count()} legacy token(s)? GitHub IDs and usernames are kept, so sign-in and repo invites keep working.")) {
            $this->info('Nothing changed.');

            return self::SUCCESS;
        }

        $cleared = User::query()
            ->whereKey($users->modelKeys())
            ->update([
                'github_token' => null,
                'github_refresh_token' => null,
                'github_token_expires_at' => null,
            ]);

        $this->info("Cleared {$cleared} legacy token(s).");
        $this->line('If you haven\'t already, delete the old OAuth App on GitHub and remove GITHUB_CLIENT_ID and GITHUB_CLIENT_SECRET.');

        return self::SUCCESS;
    }
}
