<?php

namespace App\Jobs\Concerns;

use App\Models\Plugin;
use App\Services\GitHubAppService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait ResolvesGitHubToken
{
    protected function getGitHubToken(): ?string
    {
        /** @var Plugin $plugin */
        $plugin = $this->plugin;

        return $this->resolveGitHubTokenFor($plugin);
    }

    protected function resolveGitHubTokenFor(Plugin $plugin): ?string
    {
        $user = $plugin->user;
        $repo = $plugin->getRepositoryOwnerAndName();

        // Priority 1: Installation token (GitHub App)
        if ($user && $repo && $user->isUsingGitHubApp()) {
            $appService = app(GitHubAppService::class);
            $installation = $appService->findInstallationForRepo($user, $repo['owner'], $repo['repo']);

            if ($installation) {
                $token = $appService->getInstallationToken($installation);

                if ($token && $this->gitHubAcceptsToken($plugin, $token)) {
                    Log::debug('[GitHub] Using installation token', [
                        'plugin_id' => $plugin->id,
                        'user_id' => $user->id,
                        'installation_id' => $installation->installation_id,
                    ]);

                    return $token;
                }
            }
        }

        // Priority 2: User OAuth token
        $userToken = $user?->getGitHubToken();

        if ($userToken && $this->gitHubAcceptsToken($plugin, $userToken)) {
            Log::debug('[GitHub] Using plugin owner OAuth token', [
                'plugin_id' => $plugin->id,
                'user_id' => $user->id,
                'github_username' => $user->github_username,
            ]);

            return $userToken;
        }

        // Priority 3: Platform token
        $platformToken = config('services.github.token');

        Log::debug('[GitHub] Using platform token fallback', [
            'plugin_id' => $plugin->id,
            'has_token' => ! empty($platformToken),
        ]);

        return $platformToken;
    }

    /**
     * Whether GitHub still accepts a stored token for the plugin's repository. Access can be revoked
     * on GitHub's side without us hearing about it, and a revoked token fails everything it's handed
     * to, Satis builds included. Only a 401 (revoked) or 404 (can't see the repository) counts
     * against a token, since a rate limit or an outage says nothing about the token itself. The
     * platform token is the last resort, so it isn't tried.
     */
    protected function gitHubAcceptsToken(Plugin $plugin, string $token): bool
    {
        $repo = $plugin->getRepositoryOwnerAndName();

        if (! $repo) {
            return true;
        }

        try {
            $response = Http::withToken($token)
                ->accept('application/vnd.github+json')
                ->timeout(10)
                ->get("https://api.github.com/repos/{$repo['owner']}/{$repo['repo']}");
        } catch (ConnectionException) {
            return true;
        }

        if ($response->unauthorized() || $response->notFound()) {
            Log::warning('[GitHub] Token rejected, trying the next one', [
                'plugin_id' => $plugin->id,
                'status' => $response->status(),
            ]);

            return false;
        }

        return true;
    }
}
