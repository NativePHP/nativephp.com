<?php

namespace App\Http\Controllers;

use App\Models\GitHubInstallation;
use App\Models\User;
use App\Services\GitHubUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubAppWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->verifySignature($request)) {
            Log::warning('[GitHubAppWebhook] Invalid signature');

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $event = $request->header('X-GitHub-Event');
        $payload = $request->all();

        return match ($event) {
            'installation' => $this->handleInstallation($payload),
            'installation_repositories' => $this->handleInstallationRepositories($payload),
            default => response()->json(['status' => 'ignored']),
        };
    }

    protected function verifySignature(Request $request): bool
    {
        $secret = config('services.github_app.webhook_secret');

        if (! $secret) {
            return false;
        }

        $signature = $request->header('X-Hub-Signature-256');

        if (! $signature) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    protected function handleInstallation(array $payload): JsonResponse
    {
        $action = $payload['action'] ?? null;
        $installation = $payload['installation'] ?? [];
        $sender = $payload['sender'] ?? [];

        $installationId = $installation['id'] ?? null;

        if (! $installationId) {
            return response()->json(['error' => 'Missing installation ID'], 400);
        }

        return match ($action) {
            'created' => $this->installationCreated($installation, $sender),
            'deleted' => $this->installationDeleted($installationId),
            'suspend' => $this->installationSuspended($installationId),
            'unsuspend' => $this->installationUnsuspended($installationId),
            default => response()->json(['status' => 'ignored']),
        };
    }

    protected function installationCreated(array $installation, array $sender): JsonResponse
    {
        $user = User::where('github_id', $sender['id'] ?? null)->first();

        if (! $user) {
            Log::info('[GitHubAppWebhook] Installation created by unknown user', [
                'sender_id' => $sender['id'] ?? null,
                'installation_id' => $installation['id'],
            ]);

            return response()->json(['status' => 'user_not_found']);
        }

        $repos = collect($installation['repositories'] ?? [])->pluck('full_name')->all();

        $user->githubInstallations()->updateOrCreate(
            ['installation_id' => $installation['id']],
            [
                'account_login' => $installation['account']['login'] ?? 'unknown',
                'account_type' => $installation['account']['type'] ?? 'User',
                'account_id' => $installation['account']['id'] ?? null,
                'selection_type' => $installation['repository_selection'] ?? 'all',
                'repository_selection' => ! empty($repos) ? $repos : null,
            ]
        );

        GitHubUserService::for($user)->clearRepositoryCache();

        Log::info('[GitHubAppWebhook] Installation created', [
            'user_id' => $user->id,
            'installation_id' => $installation['id'],
        ]);

        return response()->json(['status' => 'created']);
    }

    protected function installationDeleted(int $installationId): JsonResponse
    {
        GitHubInstallation::where('installation_id', $installationId)->delete();

        Log::info('[GitHubAppWebhook] Installation deleted', [
            'installation_id' => $installationId,
        ]);

        return response()->json(['status' => 'deleted']);
    }

    protected function installationSuspended(int $installationId): JsonResponse
    {
        GitHubInstallation::where('installation_id', $installationId)
            ->update(['suspended_at' => now()]);

        return response()->json(['status' => 'suspended']);
    }

    protected function installationUnsuspended(int $installationId): JsonResponse
    {
        GitHubInstallation::where('installation_id', $installationId)
            ->update(['suspended_at' => null]);

        return response()->json(['status' => 'unsuspended']);
    }

    protected function handleInstallationRepositories(array $payload): JsonResponse
    {
        $installationId = $payload['installation']['id'] ?? null;

        if (! $installationId) {
            return response()->json(['error' => 'Missing installation ID'], 400);
        }

        $installation = GitHubInstallation::where('installation_id', $installationId)->first();

        if (! $installation) {
            return response()->json(['status' => 'installation_not_found']);
        }

        $selection = $payload['repository_selection'] ?? $installation->selection_type;
        $currentRepos = $installation->repository_selection ?? [];

        $addedRepos = collect($payload['repositories_added'] ?? [])->pluck('full_name')->all();
        $removedRepos = collect($payload['repositories_removed'] ?? [])->pluck('full_name')->all();

        $updatedRepos = array_values(
            array_unique(
                array_diff(
                    array_merge($currentRepos, $addedRepos),
                    $removedRepos
                )
            )
        );

        $installation->update([
            'selection_type' => $selection,
            'repository_selection' => ! empty($updatedRepos) ? $updatedRepos : null,
        ]);

        GitHubUserService::for($installation->user)->clearRepositoryCache();

        Log::info('[GitHubAppWebhook] Repositories updated', [
            'installation_id' => $installationId,
            'added' => $addedRepos,
            'removed' => $removedRepos,
        ]);

        return response()->json(['status' => 'updated']);
    }
}
