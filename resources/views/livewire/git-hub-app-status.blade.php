<div>
    <flux:card>
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading>GitHub App Installations</flux:heading>
                <flux:text class="mt-1">Manage which accounts and repositories the NativePHP app can access.</flux:text>
            </div>
            @if($installUrl)
                <flux:button href="{{ $installUrl }}" target="_blank" icon:trailing="plus">Add Account</flux:button>
            @endif
        </div>

        @if($installations->isEmpty())
            <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    No GitHub App installations found. Install the app on your GitHub account to grant repository access.
                </p>
                @if($installUrl)
                    <a href="{{ $installUrl }}" class="mt-3 inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500">
                        <svg class="mr-2 size-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                        </svg>
                        Install GitHub App
                    </a>
                @endif
            </div>
        @else
            <div class="mt-4 space-y-3">
                @foreach($installations as $installation)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-600">
                        <div class="flex items-center gap-3">
                            <div class="flex size-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                @if($installation->account_type === 'Organization')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-600 dark:text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-600 dark:text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $installation->account_login }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $installation->account_type }} &bull;
                                    {{ $installation->selection_type === 'all' ? 'All repositories' : 'Selected repositories' }}
                                    @if($installation->isSuspended())
                                        &bull; <span class="text-red-500">Suspended</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="https://github.com/settings/installations/{{ $installation->installation_id }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Manage
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Plugin repo coverage --}}
        @if(count($pluginCoverage) > 0)
            <div class="mt-6 border-t border-gray-200 pt-4 dark:border-gray-700">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Plugin Repository Access</h4>
                <div class="mt-2 space-y-2">
                    @foreach($pluginCoverage as $item)
                        <div class="flex items-center gap-2 text-sm">
                            @if($item['covered'])
                                <svg class="size-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="size-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span class="font-mono text-gray-700 dark:text-gray-300">{{ $item['owner'] }}/{{ $item['repo'] }}</span>
                            @if(!$item['covered'])
                                <span class="text-xs text-red-600 dark:text-red-400">Not accessible - update your installation</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </flux:card>
</div>
