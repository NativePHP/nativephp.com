<?php

namespace App\Http\Controllers;

use App\Support\GitHubOAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitHubIntegrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function redirectToGitHub(): RedirectResponse
    {
        return Socialite::driver('github')
            ->scopes(['read:user'])
            ->redirect();
    }

    public function handleCallback(): RedirectResponse
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = Auth::user();
            $user->update([
                'github_id' => $githubUser->id,
                'github_username' => $githubUser->nickname,
            ]);

            return redirect()->route('customer.licenses.index')
                ->with('success', 'GitHub account connected successfully!');
        } catch (\Exception $e) {
            return redirect()->route('customer.licenses.index')
                ->with('error', 'Failed to connect GitHub account. Please try again.');
        }
    }

    public function requestRepoAccess(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->github_username) {
            return back()->with('error', 'Please connect your GitHub account first.');
        }

        if (! $user->hasActiveMaxLicense()) {
            return back()->with('error', 'You need an active Max license to access the mobile repository.');
        }

        $github = GitHubOAuth::make();
        $success = $github->inviteToMobileRepo($user->github_username);

        if ($success) {
            $user->update([
                'mobile_repo_access_granted_at' => now(),
            ]);

            return back()->with('success', 'Repository invitation sent! Please check your GitHub notifications to accept the invitation.');
        }

        return back()->with('error', 'Failed to send repository invitation. Please try again or contact support.');
    }

    public function disconnect(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->mobile_repo_access_granted_at && $user->github_username) {
            $github = GitHubOAuth::make();
            $github->removeFromMobileRepo($user->github_username);
        }

        $user->update([
            'github_id' => null,
            'github_username' => null,
            'mobile_repo_access_granted_at' => null,
        ]);

        return back()->with('success', 'GitHub account disconnected successfully.');
    }
}
