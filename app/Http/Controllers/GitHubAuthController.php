<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $driver = $this->resolveDriver();

        session([
            'github_auth_intent' => 'login',
            'github_auth_driver' => $driver,
        ]);

        return Socialite::driver($driver)
            ->scopes(['read:user', 'user:email'])
            ->redirect();
    }

    protected function resolveDriver(): string
    {
        if (config('services.github_app.client_id')) {
            return 'github-app';
        }

        return 'github';
    }
}
