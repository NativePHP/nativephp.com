<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GitHubUserService;
use App\Support\GitHubOAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GitHubIntegrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('handleCallback');
    }

    public function redirectToGitHub(): RedirectResponse
    {
        session(['github_auth_intent' => 'link']);

        // Store the return URL if provided
        if (request()->has('return')) {
            session(['github_return_url' => request()->get('return')]);
        }

        return Socialite::driver('github')
            ->scopes(['read:user', 'repo'])
            ->redirect();
    }

    public function handleCallback(): RedirectResponse
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $intent = session()->pull('github_auth_intent', 'link');

            if (Auth::check()) {
                return $this->handleLinkAccount($githubUser);
            }

            if ($intent === 'login') {
                return $this->handleLogin($githubUser);
            }

            return redirect()->route('customer.login')
                ->with('error', 'Please log in first to connect your GitHub account.');
        } catch (\Exception $e) {
            Log::error('GitHub OAuth callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $route = Auth::check() ? 'customer.licenses' : 'customer.login';

            return redirect()->route($route)
                ->with('error', 'GitHub authentication failed. Please try again.');
        }
    }

    protected function handleLinkAccount($githubUser): RedirectResponse
    {
        $user = Auth::user();
        $user->update([
            'github_id' => $githubUser->id,
            'github_username' => $githubUser->nickname,
            'github_token' => encrypt($githubUser->token),
        ]);

        $returnUrl = session()->pull('github_return_url');

        if ($returnUrl) {
            return redirect($returnUrl)
                ->with('success', 'GitHub account connected successfully!');
        }

        return redirect()->route('dashboard')
            ->with('success', 'GitHub account connected successfully!');
    }

    protected function handleLogin($githubUser): RedirectResponse
    {
        $user = User::where('github_id', $githubUser->id)->first();

        if ($user) {
            $user->update([
                'github_token' => encrypt($githubUser->token),
            ]);

            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back!');
        }

        $user = User::where('email', $githubUser->email)->first();

        if ($user) {
            $user->update([
                'github_id' => $githubUser->id,
                'github_username' => $githubUser->nickname,
                'github_token' => encrypt($githubUser->token),
            ]);

            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'GitHub account connected and logged in!');
        }

        $user = User::create([
            'name' => $githubUser->name ?? $githubUser->nickname,
            'email' => $githubUser->email,
            'github_id' => $githubUser->id,
            'github_username' => $githubUser->nickname,
            'github_token' => encrypt($githubUser->token),
            'password' => bcrypt(Str::random(24)),
            'email_verified_at' => now(),
        ]);

        Auth::login($user, remember: true);

        return redirect()->route('dashboard')
            ->with('success', 'Account created successfully!');
    }

    public function requestRepoAccess(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->github_username) {
            return back()->with('error', 'Please connect your GitHub account first.');
        }

        if (! $user->hasMaxAccess()) {
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
            'github_token' => null,
            'mobile_repo_access_granted_at' => null,
        ]);

        return back()->with('success', 'GitHub account disconnected successfully.');
    }

    public function repositories(): JsonResponse
    {
        $user = Auth::user();

        if (! $user->hasGitHubToken()) {
            return response()->json([
                'error' => 'GitHub account not connected or token expired',
                'repositories' => [],
            ], 401);
        }

        $service = GitHubUserService::for($user);
        $repositories = $service->getRepositories(includePrivate: true);

        return response()->json([
            'repositories' => $repositories,
        ]);
    }
}
