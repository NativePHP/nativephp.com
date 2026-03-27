<?php

namespace App\Livewire\Customer\Plugins;

use App\Enums\PluginStatus;
use App\Features\AllowPaidPlugins;
use App\Jobs\ReviewPluginRepository;
use App\Models\Plugin;
use App\Notifications\PluginSubmitted;
use App\Services\GitHubUserService;
use App\Services\PluginSyncService;
use Laravel\Pennant\Feature;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Submit Your Plugin')]
class Create extends Component
{
    public string $pluginType = 'free';

    public string $repository = '';

    public string $notes = '';

    public string $supportChannel = '';

    /** @var array<int, array{id: int, full_name: string, private: bool}> */
    public array $repositories = [];

    public bool $loadingRepos = false;

    public bool $reposLoaded = false;

    public function mount(): void
    {
        if (auth()->user()->github_id) {
            $this->loadRepositories();
        }
    }

    public function loadRepositories(): void
    {
        if ($this->loadingRepos || $this->reposLoaded) {
            return;
        }

        $this->loadingRepos = true;

        try {
            $user = auth()->user();

            if ($user->hasGitHubToken()) {
                $githubService = GitHubUserService::for($user);
                $this->repositories = $githubService->getRepositories()
                    ->map(fn ($repo) => [
                        'id' => $repo['id'],
                        'full_name' => $repo['full_name'],
                        'private' => $repo['private'] ?? false,
                    ])
                    ->toArray();
            }

            $this->reposLoaded = true;
        } catch (\Exception $e) {
            report($e);
        }

        $this->loadingRepos = false;
    }

    public function submitPlugin(PluginSyncService $syncService): void
    {
        $user = auth()->user();

        if (! $user->github_id) {
            $this->addError('repository', 'You must connect your GitHub account to submit a plugin.');

            return;
        }

        $this->validate([
            'repository' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+\/[a-zA-Z0-9_.-]+$/',
                function ($attribute, $value, $fail): void {
                    $url = 'https://github.com/'.trim($value, '/');
                    if (Plugin::where('repository_url', $url)->exists()) {
                        $fail('This repository has already been submitted.');
                    }
                },
            ],
            'pluginType' => ['required', 'string', 'in:free,paid'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'supportChannel' => ['nullable', 'string', 'max:255'],
        ], [
            'repository.required' => 'Please select a repository for your plugin.',
            'repository.regex' => 'Please enter a valid repository in the format vendor/repo-name.',
        ]);

        if ($this->pluginType === 'paid' && ! Feature::active(AllowPaidPlugins::class)) {
            session()->flash('error', 'Paid plugin submissions are not currently available.');

            return;
        }

        $developerAccountId = null;
        if ($this->pluginType === 'paid' && $user->developerAccount) {
            $developerAccountId = $user->developerAccount->id;
        }

        $repository = trim($this->repository, '/');
        $repositoryUrl = 'https://github.com/'.$repository;
        [$owner, $repo] = explode('/', $repository);

        $plugin = $user->plugins()->create([
            'repository_url' => $repositoryUrl,
            'type' => $this->pluginType,
            'status' => PluginStatus::Pending,
            'developer_account_id' => $developerAccountId,
            'notes' => $this->notes ?: null,
            'support_channel' => $this->supportChannel ?: null,
        ]);

        $webhookSecret = $plugin->generateWebhookSecret();

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

        $syncService->sync($plugin);

        (new ReviewPluginRepository($plugin))->handle();

        if (! $plugin->name) {
            $plugin->delete();

            session()->flash('error', 'Could not find a valid composer.json in the repository. Please ensure your repository contains a composer.json with a valid package name.');

            return;
        }

        $namespace = $plugin->getVendorNamespace();
        if ($namespace && ! Plugin::isNamespaceAvailableForUser($namespace, $user->id)) {
            $plugin->delete();

            $errorMessage = Plugin::isReservedNamespace($namespace)
                ? "The namespace '{$namespace}' is reserved and cannot be used for plugin submissions."
                : "The namespace '{$namespace}' is already claimed by another user. You cannot submit plugins under this namespace.";

            session()->flash('error', $errorMessage);

            return;
        }

        $user->notify(new PluginSubmitted($plugin));

        $successMessage = 'Your plugin has been submitted for review!';
        if (! $webhookInstalled) {
            $successMessage .= ' Please set up the webhook manually to enable automatic syncing.';
        }

        [$vendor, $package] = explode('/', $plugin->name);

        $this->redirect(
            route('customer.plugins.show', ['vendor' => $vendor, 'package' => $package]),
            navigate: true
        );

        session()->flash('success', $successMessage);
    }

    public function render()
    {
        return view('livewire.customer.plugins.create');
    }
}
