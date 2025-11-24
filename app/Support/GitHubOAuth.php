<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubOAuth
{
    private const ORGANIZATION = 'nativephp';

    private const REPOSITORY = 'mobile';

    public function __construct(
        private ?string $token
    ) {}

    public static function make(): static
    {
        return new static(config('services.github.token') ?? '');
    }

    public function inviteToMobileRepo(string $githubUsername): bool
    {
        $response = Http::withToken($this->token)
            ->put(
                sprintf(
                    'https://api.github.com/repos/%s/%s/collaborators/%s',
                    self::ORGANIZATION,
                    self::REPOSITORY,
                    $githubUsername
                ),
                [
                    'permission' => 'pull', // Read-only access
                ]
            );

        if ($response->failed()) {
            Log::error('Failed to invite user to GitHub repository', [
                'username' => $githubUsername,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    public function removeFromMobileRepo(string $githubUsername): bool
    {
        $response = Http::withToken($this->token)
            ->delete(
                sprintf(
                    'https://api.github.com/repos/%s/%s/collaborators/%s',
                    self::ORGANIZATION,
                    self::REPOSITORY,
                    $githubUsername
                )
            );

        if ($response->failed()) {
            Log::error('Failed to remove user from GitHub repository', [
                'username' => $githubUsername,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    public function checkCollaboratorStatus(string $githubUsername): ?string
    {
        $response = Http::withToken($this->token)
            ->get(
                sprintf(
                    'https://api.github.com/repos/%s/%s/collaborators/%s',
                    self::ORGANIZATION,
                    self::REPOSITORY,
                    $githubUsername
                )
            );

        if ($response->status() === 204) {
            return 'active';
        }

        if ($response->status() === 404) {
            return null;
        }

        return 'unknown';
    }
}
