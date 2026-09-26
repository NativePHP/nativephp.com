<?php

namespace App\Console\Commands;

use App\Enums\GitHubAuthType;
use App\Models\User;
use App\Notifications\GitHubAppMigrationRequired;
use App\Services\GitHubAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendGitHubAppMigrationNotice extends Command
{
    protected $signature = 'github:send-app-migration-notice
                            {--dry-run : Show who would be emailed without sending anything}
                            {--preview= : Send a single copy to this address instead of to users}';

    protected $description = 'Email users still on the legacy GitHub OAuth App about the move to the GitHub App';

    public function handle(GitHubAppService $appService): int
    {
        if ($preview = $this->option('preview')) {
            Notification::route('mail', $preview)->notifyNow(new GitHubAppMigrationRequired);

            $this->info("Sent a preview to {$preview}");

            return self::SUCCESS;
        }

        $dryRun = $this->option('dry-run');

        if (! $dryRun && ! $appService->legacyOAuthCutoffDate()) {
            $this->error('Set GITHUB_LEGACY_OAUTH_CUTOFF_DATE first, the email tells plugin authors when the old connection stops working.');

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->info('DRY RUN - No emails will be sent');
        }

        $users = User::query()
            ->where('github_auth_type', GitHubAuthType::OAuth)
            ->whereNull('github_app_migration_notified_at')
            ->whereNotNull('email_verified_at')
            ->withCount('plugins')
            ->get();

        $this->info("Found {$users->count()} user(s) still on the legacy OAuth App who haven't been emailed, {$users->where('plugins_count', '>', 0)->count()} of them plugin authors");

        foreach ($users as $user) {
            if ($dryRun) {
                $this->line("Would send to: {$user->email} ({$user->plugins_count} plugin(s))");

                continue;
            }

            $user->notify(new GitHubAppMigrationRequired);
            $user->update(['github_app_migration_notified_at' => now()]);

            $this->line("Sent to: {$user->email}");
        }

        $this->newLine();
        $this->info($dryRun ? "Would send: {$users->count()} email(s)" : "Sent: {$users->count()} email(s)");

        return self::SUCCESS;
    }
}
