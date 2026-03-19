<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        session(['github_auth_intent' => 'login']);

        return Socialite::driver('github')
            ->scopes(['read:user', 'user:email'])
            ->redirect();
    }
}
