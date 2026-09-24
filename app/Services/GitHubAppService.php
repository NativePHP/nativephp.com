<?php

namespace App\Services;

use App\Models\GitHubInstallation;
use App\Models\User;
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
}
