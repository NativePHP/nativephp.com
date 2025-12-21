<div>
@if(auth()->user()->hasActiveMaxLicense())
    <div @class(['max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => !$inline])>
        <div class="bg-gradient-to-r from-gray-50 to-slate-100 dark:from-gray-800 dark:to-slate-900 border border-gray-300 dark:border-gray-600 rounded-lg p-6 h-full">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="font-medium text-gray-900 dark:text-white">
                        <a href="https://github.com/nativephp/mobile" target="_blank" rel="noopener noreferrer" class="hover:underline"><code>nativephp/mobile</code></a> Repo Access
                    </h3>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        @if(auth()->user()->github_username)
                            <p>Connected as <span class="font-mono font-medium text-gray-900 dark:text-white">{{ '@' . auth()->user()->github_username }}</span></p>

                            @if($collaboratorStatus === 'active')
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Access Granted
                                    </span>
                                </p>
                                <p class="mt-1 text-xs">You have access to the nativephp/mobile repository.</p>
                            @elseif($collaboratorStatus === 'pending')
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Invitation Pending
                                    </span>
                                </p>
                                <p class="mt-1 text-xs">Check your GitHub notifications to accept the invitation.</p>
                            @else
                                <p>Request access to the nativephp/mobile repository.</p>
                            @endif
                        @else
                            <p>Connect your GitHub account to access the nativephp/mobile repository.</p>
                        @endif
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if(auth()->user()->github_username)
                            @if($collaboratorStatus === 'active')
                                <a href="https://github.com/nativephp/mobile" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View Repo
                                </a>
                            @elseif($collaboratorStatus === 'pending')
                                <button wire:click="refreshStatus" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <span wire:loading.remove wire:target="refreshStatus">Check Status</span>
                                    <span wire:loading wire:target="refreshStatus">Checking...</span>
                                </button>
                            @else
                                <form action="{{ route('github.request-access') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Request Access
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('github.disconnect') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Disconnect
                                </button>
                            </form>
                        @else
                            <a href="{{ route('github.redirect') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Connect GitHub
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
