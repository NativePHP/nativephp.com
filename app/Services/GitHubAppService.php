<?php

namespace App\Services;

use App\Models\GitHubInstallation;
use App\Models\User;
use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubAppService
{
    /**
     * The GitHub page where a user installs the app or changes which repositories it can access.
     */
    public function installationUrl(): ?string
    {
        $slug = config('services.github_app.slug');

        return $slug ? "https://github.com/apps/{$slug}/installations/new" : null;
    }

    /**
     * When the legacy OAuth App stops working, if a date has been set.
     */
    public function legacyOAuthCutoffDate(): ?CarbonImmutable
    {
        $date = config('services.github.legacy_oauth_cutoff_date');

        return $date ? CarbonImmutable::parse($date) : null;
    }

    /**
     * Whether the legacy OAuth App's cutoff date has passed, after which its tokens are no longer used.
     */
    public function legacyOAuthHasBeenRetired(): bool
    {
        return $this->legacyOAuthCutoffDate()?->isPast() ?? false;
    }

    public function generateJwt(): string
    {
        $privateKeyPath = config('services.github_app.private_key_path');
        $privateKey = file_get_contents($privateKeyPath);
        $appId = config('services.github_app.app_id');

        $now = time();

        $payload = [
            'iat' => $now - 60,
            'exp' => $now + (9 * 60),
            'iss' => $appId,
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    public function getInstallationToken(GitHubInstallation $installation): ?string
    {
        $cacheKey = "github_installation_token_{$installation->installation_id}";

        return Cache::remember($cacheKey, now()->addMinutes(55), function () use ($installation) {
            return $this->refreshInstallationToken($installation);
        });
    }

    public function refreshInstallationToken(GitHubInstallation $installation): ?string
    {
        try {
            $jwt = $this->generateJwt();

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$jwt}",
                'Accept' => 'application/vnd.github+json',
            ])->post("https://api.github.com/app/installations/{$installation->installation_id}/access_tokens");

            if ($response->failed()) {
                Log::warning('[GitHubApp] Failed to get installation token', [
                    'installation_id' => $installation->installation_id,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return null;
            }

            $data = $response->json();
            $token = $data['token'];
            $expiresAt = $data['expires_at'];

            $installation->update([
                'access_token' => encrypt($token),
                'token_expires_at' => $expiresAt,
            ]);

            // Update cache with correct TTL
            $cacheKey = "github_installation_token_{$installation->installation_id}";
            Cache::put($cacheKey, $token, now()->addMinutes(55));

            return $token;
        } catch (\Exception $e) {
            Log::error('[GitHubApp] Exception getting installation token', [
                'installation_id' => $installation->installation_id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function findInstallationForRepo(User $user, string $owner, string $repo): ?GitHubInstallation
    {
        return $user->githubInstallations()
            ->whereNull('suspended_at')
            ->get()
            ->first(fn (GitHubInstallation $installation) => $installation->hasAccessToRepo($owner, $repo));
    }

    /**
     * Whether the installation is one the user can see on GitHub. GitHub's post-install redirect
     * passes the installation ID in the query string, so it can't be trusted on its own.
     */
    public function userCanAccessInstallation(User $user, int $installationId): bool
    {
        $token = $user->getGitHubToken();

        if (! $token) {
            return false;
        }

        $page = 1;

        do {
            $response = Http::withToken($token)
                ->accept('application/vnd.github+json')
                ->get('https://api.github.com/user/installations', [
                    'per_page' => 100,
                    'page' => $page,
                ]);

            if ($response->failed()) {
                Log::warning('[GitHubApp] Failed to list user installations', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                ]);

                return false;
            }

            $installations = collect($response->json('installations', []));

            if ($installations->contains('id', $installationId)) {
                return true;
            }

            $page++;
        } while ($installations->count() === 100 && $page <= 10);

        return false;
    }

    /**
     * Refresh an installation's account details and repository list from GitHub. An installation
     * GitHub no longer knows about is deleted.
     */
    public function syncInstallation(GitHubInstallation $installation): bool
    {
        try {
            $response = Http::withToken($this->generateJwt())
                ->accept('application/vnd.github+json')
                ->get("https://api.github.com/app/installations/{$installation->installation_id}");
        } catch (\Exception $e) {
            Log::warning('[GitHubApp] Exception syncing installation', [
                'installation_id' => $installation->installation_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        if ($response->notFound()) {
            $installation->delete();

            return false;
        }

        if ($response->failed()) {
            Log::warning('[GitHubApp] Failed to sync installation', [
                'installation_id' => $installation->installation_id,
                'status' => $response->status(),
            ]);

            return false;
        }

        $data = $response->json();
        $selectionType = $data['repository_selection'] ?? 'all';

        $installation->update([
            'account_login' => $data['account']['login'] ?? $installation->account_login,
            'account_type' => $data['account']['type'] ?? $installation->account_type,
            'account_id' => $data['account']['id'] ?? $installation->account_id,
            'selection_type' => $selectionType,
            'suspended_at' => $data['suspended_at'] ?? null,
            'repository_selection' => $selectionType === 'selected'
                ? ($this->fetchInstallationRepositories($installation) ?? $installation->repository_selection)
                : null,
        ]);

        return true;
    }

    /**
     * @return array<int, string>|null Full names of the repositories the installation can access, or null if they couldn't be fetched.
     */
    protected function fetchInstallationRepositories(GitHubInstallation $installation): ?array
    {
        $token = $this->getInstallationToken($installation);

        if (! $token) {
            return null;
        }

        $repositories = [];
        $page = 1;

        do {
            $response = Http::withToken($token)
                ->accept('application/vnd.github+json')
                ->get('https://api.github.com/installation/repositories', [
                    'per_page' => 100,
                    'page' => $page,
                ]);

            if ($response->failed()) {
                return null;
            }

            $pageRepositories = collect($response->json('repositories', []))->pluck('full_name');
            $repositories = [...$repositories, ...$pageRepositories->all()];
            $page++;
        } while ($pageRepositories->count() === 100 && $page <= 10);

        return $repositories;
    }

    /**
     * Swap an expired GitHub App user token for a new one. Clears the stored tokens if GitHub
     * rejects the refresh token, so the user is asked to reconnect.
     */
    public function refreshUserToken(User $user): ?string
    {
        $refreshToken = $user->getGitHubRefreshToken();

        if (! $refreshToken) {
            return null;
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->post('https://github.com/login/oauth/access_token', [
                    'client_id' => config('services.github_app.client_id'),
                    'client_secret' => config('services.github_app.client_secret'),
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]);
        } catch (\Exception $e) {
            Log::warning('[GitHubApp] Exception refreshing user token', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        $accessToken = $response->json('access_token');

        if ($response->failed() || ! $accessToken) {
            Log::warning('[GitHubApp] Failed to refresh user token', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'error' => $response->json('error'),
            ]);

            if ($response->json('error') === 'bad_refresh_token') {
                $user->update([
                    'github_token' => null,
                    'github_refresh_token' => null,
                    'github_token_expires_at' => null,
                ]);
            }

            return null;
        }

        $user->update([
            'github_token' => encrypt($accessToken),
            'github_refresh_token' => encrypt($response->json('refresh_token') ?? $refreshToken),
            'github_token_expires_at' => $response->json('expires_in') ? now()->addSeconds($response->json('expires_in')) : null,
        ]);

        return $accessToken;
    }
}
