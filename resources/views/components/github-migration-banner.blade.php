@props(['blocking' => false])

@php
    $user = auth()->user();
    $needsMigration = $user->needsGitHubAppMigration();
    $missingAccess = $needsMigration ? collect() : $user->pluginsMissingGitHubAppAccess();
    $pluginRepos = $needsMigration
        ? $user->pluginRepositoryNames()
        : $missingAccess
            ->map(fn ($plugin) => $plugin->getRepositoryOwnerAndName())
            ->filter()
            ->map(fn (array $repo) => "{$repo['owner']}/{$repo['repo']}")
            ->unique()
            ->values();
    $appService = app(\App\Services\GitHubAppService::class);
    $installUrl = $appService->installationUrl();
    $cutoffDate = $appService->legacyOAuthCutoffDate();
    $deadline = $cutoffDate ? $cutoffDate->format('j F Y') : 'we switch off the old connection';
    $urgent = ! $needsMigration || $blocking || $pluginRepos->isNotEmpty();
@endphp

@if($needsMigration || $missingAccess->isNotEmpty())
    <div @class([
        'mb-6 rounded-xl border p-6',
        'border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/20' => $urgent,
        'border-blue-200 bg-blue-50 dark:border-blue-900/50 dark:bg-blue-900/20' => ! $urgent,
    ])>
        <div class="flex">
            <div class="shrink-0">
                <svg @class(['size-5', 'text-amber-400' => $urgent, 'text-blue-400' => ! $urgent]) viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 @class(['text-sm font-medium', 'text-amber-800 dark:text-amber-200' => $urgent, 'text-blue-800 dark:text-blue-200' => ! $urgent])>
                    @if(! $needsMigration)
                        GitHub App Needs Access to Your Plugins
                    @elseif($urgent)
                        GitHub Connection Upgrade Required
                    @else
                        We've Improved Our GitHub Connection
                    @endif
                </h3>
                <div @class(['mt-2 text-sm', 'text-amber-700 dark:text-amber-300' => $urgent, 'text-blue-700 dark:text-blue-300' => ! $urgent])>
                    @if($needsMigration)
                        <p>
                            We're moving away from GitHub OAuth, which gave us broad access to your repositories,
                            to a GitHub App that gives you fine-grained control over what we can see.
                        </p>

                        @if($pluginRepos->isNotEmpty())
                            <p class="mt-2 font-medium">
                                As a plugin author, you need to connect our new GitHub App. If you don't do it before {{ $deadline }},
                                we won't be able to keep your plugins up to date and may remove them from the Marketplace.
                            </p>
                            <p class="mt-2">If you choose "Only select repositories", include each of these:</p>
                        @elseif($blocking)
                            <p class="mt-2 font-medium">You need to connect the GitHub App before you can create a plugin.</p>
                        @else
                            <p class="mt-2">
                                You don't need to do anything. "Login with GitHub" keeps working, and we only use it to confirm your identity.
                                You can reconnect now if you'd like to switch straight away.
                            </p>
                        @endif
                    @else
                        <p>
                            We can't reach some of your plugin repositories, so they won't sync new releases.
                            Install the NativePHP GitHub App on the accounts that own them, or add them to an existing installation.
                        </p>
                        <p class="mt-2 font-medium">Grant access to:</p>
                    @endif

                    @if($pluginRepos->isNotEmpty())
                        <ul class="mt-1 list-inside list-disc font-mono">
                            @foreach($pluginRepos as $repo)
                                <li>{{ $repo }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @if($needsMigration || $installUrl)
                <div class="mt-4">
                    <a href="{{ $needsMigration ? route('github.redirect') : $installUrl }}" @class([
                        'inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2',
                        'bg-amber-600 hover:bg-amber-500 focus:ring-amber-500' => $urgent,
                        'bg-blue-600 hover:bg-blue-500 focus:ring-blue-500' => ! $urgent,
                    ])>
                        <svg class="mr-2 size-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                        </svg>
                        @if(! $needsMigration)
                            Grant Repository Access
                        @elseif($urgent)
                            Connect the GitHub App
                        @else
                            Reconnect GitHub
                        @endif
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endif
