<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubOAuth
{
    private const ORGANIZATION = 'nativephp';

    private const MOBILE_REPOSITORY = 'mobile';

    private const CLAUDE_PLUGINS_REPOSITORY = 'ClaudePlugins';

    public function __construct(
        private ?string $token
    ) {}

    public static function make(): static
    {
        return new static(config('services.github.token') ?? '');
    }

    /**
     * Invite a user to a repository with read-only access.
     */
    public function inviteToRepo(string $repository, string $githubUsername): bool
    {
        $response = Http::withToken($this->token)
            ->put(
                sprintf(
                    'https://api.github.com/repos/%s/%s/collaborators/%s',
                    self::ORGANIZATION,
                    $repository,
                    $githubUsername
                ),
                [
                    'permission' => 'pull', // Read-only access
                ]
            );

        if ($response->failed()) {
            Log::error('Failed to invite user to GitHub repository', [
                'repository' => $repository,
                'username' => $githubUsername,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Remove a user from a repository.
     */
    public function removeFromRepo(string $repository, string $githubUsername): bool
    {
        $response = Http::withToken($this->token)
            ->delete(
                sprintf(
                    'https://api.github.com/repos/%s/%s/collaborators/%s',
                    self::ORGANIZATION,
                    $repository,
                    $githubUsername
                )
            );

        if ($response->failed()) {
            Log::error('Failed to remove user from GitHub repository', [
                'repository' => $repository,
                'username' => $githubUsername,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Check the collaborator status for a user on a specific repository.
     */
    public function checkRepoCollaboratorStatus(string $repository, string $githubUsername): ?string
    {
        $response = Http::withToken($this->token)
            ->get(
                sprintf(
                    'https://api.github.com/repos/%s/%s/collaborators/%s',
                    self::ORGANIZATION,
                    $repository,
                    $githubUsername
                )
            );

        if ($response->status() === 204) {
            return 'active';
        }

        if ($this->hasRepoPendingInvitation($repository, $githubUsername)) {
            return 'pending';
        }

        if ($response->status() === 404) {
            return null;
        }

        return 'unknown';
    }

    /**
     * Check if a user has a pending invitation for a specific repository.
     */
    public function hasRepoPendingInvitation(string $repository, string $githubUsername): bool
    {
        $response = Http::withToken($this->token)
            ->get(
                sprintf(
                    'https://api.github.com/repos/%s/%s/invitations',
                    self::ORGANIZATION,
                    $repository
                )
            );

        if ($response->failed()) {
            return false;
        }

        $invitations = $response->json();

        return collect($invitations)->contains(function ($invitation) use ($githubUsername) {
            return strtolower($invitation['invitee']['login'] ?? '') === strtolower($githubUsername);
        });
    }

    // Backward compatible methods for mobile repo

    public function inviteToMobileRepo(string $githubUsername): bool
    {
        return $this->inviteToRepo(self::MOBILE_REPOSITORY, $githubUsername);
    }

    public function removeFromMobileRepo(string $githubUsername): bool
    {
        return $this->removeFromRepo(self::MOBILE_REPOSITORY, $githubUsername);
    }

    public function checkCollaboratorStatus(string $githubUsername): ?string
    {
        return $this->checkRepoCollaboratorStatus(self::MOBILE_REPOSITORY, $githubUsername);
    }

    public function hasPendingInvitation(string $githubUsername): bool
    {
        return $this->hasRepoPendingInvitation(self::MOBILE_REPOSITORY, $githubUsername);
    }

    // ClaudePlugins repo methods

    public function inviteToClaudePluginsRepo(string $githubUsername): bool
    {
        return $this->inviteToRepo(self::CLAUDE_PLUGINS_REPOSITORY, $githubUsername);
    }

    public function removeFromClaudePluginsRepo(string $githubUsername): bool
    {
        return $this->removeFromRepo(self::CLAUDE_PLUGINS_REPOSITORY, $githubUsername);
    }

    public function checkClaudePluginsCollaboratorStatus(string $githubUsername): ?string
    {
        return $this->checkRepoCollaboratorStatus(self::CLAUDE_PLUGINS_REPOSITORY, $githubUsername);
    }
}
