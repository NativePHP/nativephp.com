<?php

namespace App\Jobs\Concerns;

use App\Models\Plugin;
use Illuminate\Support\Facades\Log;

trait ResolvesGitHubToken
{
    protected function getGitHubToken(): ?string
    {
        /** @var Plugin $plugin */
        $plugin = $this->plugin;
        $user = $plugin->user;

        if ($user && $user->hasGitHubToken()) {
            Log::debug('[GitHub] Using plugin owner OAuth token', [
                'plugin_id' => $plugin->id,
                'user_id' => $user->id,
                'github_username' => $user->github_username,
            ]);

            return $user->getGitHubToken();
        }

        $platformToken = config('services.github.token');

        Log::debug('[GitHub] Using platform token fallback', [
            'plugin_id' => $plugin->id,
            'has_token' => ! empty($platformToken),
        ]);

        return $platformToken;
    }
}
