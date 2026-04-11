<?php

namespace App\Jobs\Concerns;

use App\Models\Plugin;
use App\Services\GitHubAppService;
use Illuminate\Support\Facades\Log;

trait ResolvesGitHubToken
{
    protected function getGitHubToken(): ?string
    {
        /** @var Plugin $plugin */
        $plugin = $this->plugin;
        $user = $plugin->user;
        $repo = $plugin->getRepositoryOwnerAndName();

        // Priority 1: Installation token (GitHub App)
        if ($user && $repo && $user->isUsingGitHubApp()) {
            $appService = app(GitHubAppService::class);
            $installation = $appService->findInstallationForRepo($user, $repo['owner'], $repo['repo']);

            if ($installation) {
                $token = $appService->getInstallationToken($installation);

                if ($token) {
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
        if ($user && $user->hasGitHubToken()) {
            Log::debug('[GitHub] Using plugin owner OAuth token', [
                'plugin_id' => $plugin->id,
                'user_id' => $user->id,
                'github_username' => $user->github_username,
            ]);

            return $user->getGitHubToken();
        }

        // Priority 3: Platform token
        $platformToken = config('services.github.token');

        Log::debug('[GitHub] Using platform token fallback', [
            'plugin_id' => $plugin->id,
            'has_token' => ! empty($platformToken),
        ]);

        return $platformToken;
    }
}
