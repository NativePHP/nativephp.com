<div>
    <div @class(['max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => !$inline])>
        <div class="bg-gradient-to-r from-indigo-50 to-purple-100 dark:from-indigo-900 dark:to-purple-900 border border-indigo-300 dark:border-indigo-600 rounded-lg p-6 h-full">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-700 dark:text-indigo-300" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-medium text-gray-900 dark:text-white">
                            Discord Max Role
                        </h3>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @if(auth()->user()->discord_username)
                                <p>Connected as <span class="font-mono font-medium text-gray-900 dark:text-white">{{ auth()->user()->discord_username }}</span></p>

                                @if(!$isGuildMember)
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Not in Server
                                        </span>
                                    </p>
                                @elseif($hasMaxRole)
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Max Role Active
                                        </span>
                                    </p>
                                @elseif(auth()->user()->hasMaxAccess())
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Eligible
                                        </span>
                                    </p>
                                @endif
                            @else
                                <p>Connect your Discord account to receive the Max role.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 lg:flex-shrink-0">
                    @if(auth()->user()->discord_username)
                        @if($hasMaxRole)
                            <a href="https://discord.gg/nativephp" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Open Discord
                            </a>
                        @elseif(!$isGuildMember)
                            <a href="https://discord.gg/nativephp" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Join Discord Server
                            </a>
                            <button wire:click="refreshStatus" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <span wire:loading.remove wire:target="refreshStatus">Check Status</span>
                                <span wire:loading wire:target="refreshStatus">Checking...</span>
                            </button>
                        @elseif(auth()->user()->hasMaxAccess())
                            <button wire:click="requestMaxRole" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span wire:loading.remove wire:target="requestMaxRole">Request Max Role</span>
                                <span wire:loading wire:target="requestMaxRole">Requesting...</span>
                            </button>
                        @endif
                        <form action="{{ route('discord.disconnect') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Disconnect
                            </button>
                        </form>
                    @else
                        <a href="{{ route('discord.redirect') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Connect Discord
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
