<?php

namespace App\Http\Controllers;

use App\Enums\GitHubAuthType;
use App\Models\GitHubInstallation;
use App\Models\Product;
use App\Models\User;
use App\Services\GitHubUserService;
use App\Support\GitHubOAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $driver = $this->resolveDriver();

        session([
            'github_auth_intent' => 'link',
            'github_auth_driver' => $driver,
        ]);

        // Store the return URL if provided
        if (request()->has('return')) {
            session(['github_return_url' => request()->get('return')]);
        }

        $scopes = $driver === 'github-app'
            ? ['read:user', 'user:email']
            : ['read:user', 'repo'];

        return Socialite::driver($driver)
            ->scopes($scopes)
            ->redirect();
    }

    public function handleCallback(): RedirectResponse
    {
        try {
            $driver = session()->pull('github_auth_driver', 'github');
            $githubUser = Socialite::driver($driver)->user();

            $intent = session()->pull('github_auth_intent', 'link');
            $authType = $driver === 'github-app' ? GitHubAuthType::App : GitHubAuthType::OAuth;

            if (Auth::check()) {
                return $this->handleLinkAccount($githubUser, $authType);
            }

            if ($intent === 'login') {
                return $this->handleLogin($githubUser, $authType);
            }

            return to_route('customer.login')
                ->with('error', 'Please log in first to connect your GitHub account.');
        } catch (\Exception $e) {
            Log::error('GitHub OAuth callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $route = Auth::check() ? 'customer.licenses.list' : 'customer.login';

            return to_route($route)
                ->with('error', 'GitHub authentication failed. Please try again.');
        }
    }

    protected function handleLinkAccount($githubUser, GitHubAuthType $authType): RedirectResponse
    {
        $user = Auth::user();
        $user->update([
            'github_id' => $githubUser->id,
            'github_username' => $githubUser->nickname,
            'github_token' => encrypt($githubUser->token),
            'github_auth_type' => $authType,
        ]);

        $returnUrl = session()->pull('github_return_url');

        // For GitHub App users, redirect to install the app for repo access
        if ($authType === GitHubAuthType::App && $slug = config('services.github_app.slug')) {
            $installUrl = "https://github.com/apps/{$slug}/installations/new";

            if ($returnUrl) {
                session(['github_return_url' => $returnUrl]);
            }

            return redirect($installUrl);
        }

        if ($returnUrl) {
            return redirect($returnUrl)
                ->with('success', 'GitHub account connected successfully!');
        }

        return to_route('dashboard')
            ->with('success', 'GitHub account connected successfully!');
    }

    protected function handleLogin($githubUser, GitHubAuthType $authType): RedirectResponse
    {
        $user = User::where('github_id', $githubUser->id)->first();

        if ($user) {
            $user->update([
                'github_token' => encrypt($githubUser->token),
                'github_auth_type' => $authType,
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
                'github_auth_type' => $authType,
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
            'github_auth_type' => $authType,
            'password' => bcrypt(Str::random(24)),
            'email_verified_at' => now(),
        ]);

        Auth::login($user, remember: true);

        return to_route('dashboard')
            ->with('success', 'Account created successfully!');
    }

    public function handleSetup(Request $request): RedirectResponse
    {
        $installationId = $request->query('installation_id');

        if (! $installationId) {
            return to_route('customer.integrations')
                ->with('error', 'No installation ID provided.');
        }

        $user = Auth::user();

        // Record the installation if the webhook hasn't already
        $existing = GitHubInstallation::where('installation_id', $installationId)->first();

        if (! $existing) {
            // Fetch installation details from GitHub
            try {
                $appService = app(\App\Services\GitHubAppService::class);
                $jwt = $appService->generateJwt();

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => "Bearer {$jwt}",
                    'Accept' => 'application/vnd.github+json',
                ])->get("https://api.github.com/app/installations/{$installationId}");

                if ($response->successful()) {
                    $data = $response->json();

                    $user->githubInstallations()->create([
                        'installation_id' => $installationId,
                        'account_login' => $data['account']['login'] ?? 'unknown',
                        'account_type' => $data['account']['type'] ?? 'User',
                        'account_id' => $data['account']['id'] ?? null,
                        'selection_type' => $data['repository_selection'] ?? 'all',
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch GitHub App installation details', [
                    'installation_id' => $installationId,
                    'error' => $e->getMessage(),
                ]);

                // Still create a basic record so the user isn't stuck
                $user->githubInstallations()->create([
                    'installation_id' => $installationId,
                    'account_login' => $user->github_username ?? 'unknown',
                    'account_type' => 'User',
                    'selection_type' => 'all',
                ]);
            }
        }

        $returnUrl = session()->pull('github_return_url');

        if ($returnUrl) {
            return redirect($returnUrl)
                ->with('success', 'GitHub App installed successfully!');
        }

        return to_route('customer.integrations')
            ->with('success', 'GitHub App installed successfully!');
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

    public function requestClaudePluginsAccess(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->github_username) {
            return back()->with('error', 'Please connect your GitHub account first.');
        }

        // Check if user has a Plugin Dev Kit license
        $pluginDevKit = Product::where('slug', 'plugin-dev-kit')->first();

        if (! $pluginDevKit || ! $user->hasProductLicense($pluginDevKit)) {
            return back()->with('error', 'You need a Plugin Dev Kit license to access the claude-code repository.');
        }

        $github = GitHubOAuth::make();
        $success = $github->inviteToClaudePluginsRepo($user->github_username);

        if ($success) {
            $user->update([
                'claude_plugins_repo_access_granted_at' => now(),
            ]);

            return back()->with('success', 'Repository invitation sent! Please check your GitHub notifications to accept the invitation.');
        }

        return back()->with('error', 'Failed to send repository invitation. Please try again or contact support.');
    }

    public function disconnect(): RedirectResponse
    {
        $user = Auth::user();
        $github = GitHubOAuth::make();

        if ($user->mobile_repo_access_granted_at && $user->github_username) {
            $github->removeFromMobileRepo($user->github_username);
        }

        if ($user->claude_plugins_repo_access_granted_at && $user->github_username) {
            $github->removeFromClaudePluginsRepo($user->github_username);
        }

        $user->githubInstallations()->delete();

        $user->update([
            'github_id' => null,
            'github_username' => null,
            'github_token' => null,
            'github_auth_type' => null,
            'mobile_repo_access_granted_at' => null,
            'claude_plugins_repo_access_granted_at' => null,
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

    protected function resolveDriver(): string
    {
        if (config('services.github_app.client_id')) {
            return 'github-app';
        }

        return 'github';
    }
}
