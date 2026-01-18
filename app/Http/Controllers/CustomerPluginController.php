<?php

namespace App\Http\Controllers;

use App\Enums\PluginStatus;
use App\Features\AllowPaidPlugins;
use App\Http\Requests\SubmitPluginRequest;
use App\Http\Requests\UpdatePluginDescriptionRequest;
use App\Http\Requests\UpdatePluginLogoRequest;
use App\Http\Requests\UpdatePluginPriceRequest;
use App\Models\Plugin;
use App\Services\GitHubUserService;
use App\Services\PluginSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Laravel\Pennant\Feature;

class CustomerPluginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();
        $plugins = $user->plugins()->orderBy('created_at', 'desc')->get();
        $developerAccount = $user->developerAccount;

        return view('customer.plugins.index', compact('plugins', 'developerAccount'));
    }

    public function create(): View
    {
        return view('customer.plugins.create');
    }

    public function store(SubmitPluginRequest $request, PluginSyncService $syncService): RedirectResponse
    {
        $user = Auth::user();

        // Reject paid plugin submissions if the feature is disabled
        if ($request->type === 'paid' && ! Feature::active(AllowPaidPlugins::class)) {
            return redirect()->route('customer.plugins.create')
                ->with('error', 'Paid plugin submissions are not currently available.');
        }

        // For paid plugins, link to the user's developer account if they have one
        $developerAccountId = null;
        if ($request->type === 'paid' && $user->developerAccount) {
            $developerAccountId = $user->developerAccount->id;
        }

        // Build the full repository URL from vendor/repo format
        $repository = trim($request->repository, '/');
        $repositoryUrl = 'https://github.com/'.$repository;
        [$owner, $repo] = explode('/', $repository);

        $plugin = $user->plugins()->create([
            'repository_url' => $repositoryUrl,
            'type' => $request->type,
            'status' => PluginStatus::Pending,
            'developer_account_id' => $developerAccountId,
        ]);

        $webhookSecret = $plugin->generateWebhookSecret();

        // Attempt to automatically install the webhook if the user has a GitHub token
        $webhookInstalled = false;
        if ($user->hasGitHubToken()) {
            $githubService = GitHubUserService::for($user);
            $webhookResult = $githubService->createWebhook(
                $owner,
                $repo,
                $plugin->getWebhookUrl(),
                $webhookSecret
            );
            $webhookInstalled = $webhookResult['success'];
        }

        $plugin->update(['webhook_installed' => $webhookInstalled]);

        if ($request->type === 'paid' && $request->price) {
            $plugin->prices()->create([
                'amount' => $request->price * 100,
                'currency' => 'usd',
                'is_active' => true,
            ]);
        }

        $syncService->sync($plugin);

        if (! $plugin->name) {
            $plugin->delete();

            return redirect()->route('customer.plugins.create')
                ->with('error', 'Could not find a valid composer.json in the repository. Please ensure your repository contains a composer.json with a valid package name.');
        }

        // Check if the vendor namespace is available for this user
        $namespace = $plugin->getVendorNamespace();
        if ($namespace && ! Plugin::isNamespaceAvailableForUser($namespace, $user->id)) {
            $plugin->delete();

            $errorMessage = Plugin::isReservedNamespace($namespace)
                ? "The namespace '{$namespace}' is reserved and cannot be used for plugin submissions."
                : "The namespace '{$namespace}' is already claimed by another user. You cannot submit plugins under this namespace.";

            return redirect()->route('customer.plugins.create')
                ->with('error', $errorMessage);
        }

        $successMessage = 'Your plugin has been submitted for review!';
        if (! $webhookInstalled) {
            $successMessage .= ' Please set up the webhook manually to enable automatic syncing.';
        }

        return redirect()->route('customer.plugins.show', $plugin)
            ->with('success', $successMessage);
    }

    public function show(Plugin $plugin): View
    {
        $user = Auth::user();

        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        return view('customer.plugins.show', compact('plugin'));
    }

    public function update(UpdatePluginDescriptionRequest $request, Plugin $plugin): RedirectResponse
    {
        $user = Auth::user();

        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        $plugin->updateDescription($request->description, $user->id);

        return redirect()->route('customer.plugins.show', $plugin)
            ->with('success', 'Plugin description updated successfully!');
    }

    public function resubmit(Plugin $plugin): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the plugin belongs to the current user
        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        // Only rejected plugins can be resubmitted
        if (! $plugin->isRejected()) {
            return redirect()->route('customer.plugins.index')
                ->with('error', 'Only rejected plugins can be resubmitted.');
        }

        $plugin->resubmit();

        return redirect()->route('customer.plugins.index')
            ->with('success', 'Your plugin has been resubmitted for review!');
    }

    public function updateLogo(UpdatePluginLogoRequest $request, Plugin $plugin): RedirectResponse
    {
        $user = Auth::user();

        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        if ($plugin->logo_path) {
            Storage::disk('public')->delete($plugin->logo_path);
        }

        $path = $request->file('logo')->store('plugin-logos', 'public');

        $plugin->update(['logo_path' => $path]);

        return redirect()->route('customer.plugins.show', $plugin)
            ->with('success', 'Plugin logo updated successfully!');
    }

    public function deleteLogo(Plugin $plugin): RedirectResponse
    {
        $user = Auth::user();

        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        if ($plugin->logo_path) {
            Storage::disk('public')->delete($plugin->logo_path);
            $plugin->update(['logo_path' => null]);
        }

        return redirect()->route('customer.plugins.show', $plugin)
            ->with('success', 'Plugin logo removed successfully!');
    }

    public function updateDisplayName(): RedirectResponse
    {
        $user = Auth::user();

        $validated = request()->validate([
            'display_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'display_name' => $validated['display_name'] ?: null,
        ]);

        return redirect()->route('customer.plugins.index')
            ->with('success', 'Display name updated successfully!');
    }

    public function updatePrice(UpdatePluginPriceRequest $request, Plugin $plugin): RedirectResponse
    {
        $user = Auth::user();

        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        if (! $plugin->isPaid()) {
            return redirect()->route('customer.plugins.show', $plugin)
                ->with('error', 'Only paid plugins can have pricing updated.');
        }

        // Deactivate existing prices
        $plugin->prices()->update(['is_active' => false]);

        // Create new active price
        $plugin->prices()->create([
            'amount' => $request->price * 100,
            'currency' => 'usd',
            'is_active' => true,
        ]);

        return redirect()->route('customer.plugins.show', $plugin)
            ->with('success', 'Plugin price updated successfully!');
    }
}
