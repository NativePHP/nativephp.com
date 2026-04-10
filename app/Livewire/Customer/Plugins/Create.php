<?php

namespace App\Livewire\Customer\Plugins;

use App\Enums\PluginStatus;
use App\Features\AllowPaidPlugins;
use App\Models\Plugin;
use App\Services\GitHubUserService;
use App\Services\PluginSyncService;
use Illuminate\Support\Facades\Cache;
use Laravel\Pennant\Feature;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Create Your Plugin')]
class Create extends Component
{
    public string $pluginType = 'free';

    public string $selectedOwner = '';

    public string $repository = '';

    /** @var array<int, array{id: int, full_name: string, name: string, owner: string, private: bool}> */
    public array $repositories = [];

    public bool $loadingRepos = false;

    public bool $reposLoaded = false;

    #[Computed]
    public function hasCompletedDeveloperOnboarding(): bool
    {
        return auth()->user()->developerAccount?->hasCompletedOnboarding() ?? false;
    }

    #[Computed]
    public function owners(): array
    {
        return collect($this->repositories)
            ->pluck('owner')
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->toArray();
    }

    #[Computed]
    public function ownerRepositories(): array
    {
        if ($this->selectedOwner === '') {
            return [];
        }

        return collect($this->repositories)
            ->where('owner', $this->selectedOwner)
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->toArray();
    }

    public function updatedSelectedOwner(): void
    {
        $this->repository = '';
    }

    public function mount(): void
    {
        if (auth()->user()->github_id) {
            $this->loadingRepos = true;
        }
    }

    public function loadRepositories(): void
    {
        if ($this->reposLoaded) {
            return;
        }

        $this->loadingRepos = true;

        try {
            $user = auth()->user();

            if ($user->hasGitHubToken()) {
                $cacheKey = "github_repos_{$user->id}";

                $repos = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
                    $githubService = GitHubUserService::for($user);

                    return $githubService->getRepositories()
                        ->map(fn ($repo) => [
                            'id' => $repo['id'],
                            'full_name' => $repo['full_name'],
                            'name' => $repo['name'],
                            'owner' => explode('/', $repo['full_name'])[0],
                            'private' => $repo['private'] ?? false,
                        ])
                        ->all();
                });

                $this->repositories = collect($repos)->values()->all();
            }

            $this->reposLoaded = true;
        } catch (\Exception $e) {
            report($e);
        }

        $this->loadingRepos = false;
    }

    public function createPlugin(PluginSyncService $syncService): void
    {
        $user = auth()->user();

        if (! $user->github_id) {
            $this->addError('repository', 'You must connect your GitHub account to create a plugin.');

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
                        $fail('A plugin for this repository already exists.');
                    }
                },
            ],
            'pluginType' => ['required', 'string', 'in:free,paid'],
        ], [
            'repository.required' => 'Please select a repository for your plugin.',
            'repository.regex' => 'Please enter a valid repository in the format vendor/repo-name.',
        ]);

        if ($this->pluginType === 'paid' && ! Feature::active(AllowPaidPlugins::class)) {
            session()->flash('error', 'Paid plugin submissions are not currently available.');

            return;
        }

        if ($this->pluginType === 'paid' && ! $this->hasCompletedDeveloperOnboarding) {
            session()->flash('error', 'You must complete developer onboarding before creating a paid plugin.');

            return;
        }

        $repository = trim($this->repository, '/');
        $repositoryUrl = 'https://github.com/'.$repository;
        [$owner, $repo] = explode('/', $repository);

        // Check composer.json and namespace availability before creating the plugin
        $githubService = GitHubUserService::for($user);
        $composerJson = $githubService->getComposerJson($owner, $repo);

        if (! $composerJson || empty($composerJson['name'])) {
            session()->flash('error', 'Could not find a valid composer.json in the repository. Please ensure your repository contains a composer.json with a valid package name.');

            return;
        }

        $packageName = $composerJson['name'];
        $namespace = explode('/', $packageName)[0] ?? null;

        if ($namespace && ! Plugin::isNamespaceAvailableForUser($namespace, $user->id)) {
            $errorMessage = Plugin::isReservedNamespace($namespace)
                ? "The namespace '{$namespace}' is reserved and cannot be used for plugin submissions."
                : "The namespace '{$namespace}' is already claimed by another user. You cannot submit plugins under this namespace.";

            session()->flash('error', $errorMessage);

            return;
        }

        $developerAccountId = null;
        if ($this->pluginType === 'paid' && $user->developerAccount) {
            $developerAccountId = $user->developerAccount->id;
        }

        $plugin = $user->plugins()->create([
            'repository_url' => $repositoryUrl,
            'type' => $this->pluginType,
            'status' => PluginStatus::Draft,
            'developer_account_id' => $developerAccountId,
        ]);

        $syncService->sync($plugin);

        if (! $plugin->name) {
            $plugin->delete();

            session()->flash('error', 'Could not find a valid composer.json in the repository. Please ensure your repository contains a composer.json with a valid package name.');

            return;
        }

        [$vendor, $package] = explode('/', $plugin->name);

        $this->redirect(
            route('customer.plugins.show', ['vendor' => $vendor, 'package' => $package]),
            navigate: true
        );

        session()->flash('success', 'Your plugin has been created as a draft. You can edit it and submit for review when ready.');
    }

    public function render()
    {
        return view('livewire.customer.plugins.create');
    }
}
