<div @if($isPolling) wire:poll.5s @endif>
    <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Keys
                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                            ({{ $activeSubLicenses->count() }}{{ $license->subLicenseLimit ? '/' . $license->subLicenseLimit : '' }})
                        </span>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Manage license keys for team members or additional devices.
                    </p>
                </div>
                @if($license->canCreateSubLicense())
                    <button
                        type="button"
                        onclick="showCreateKeyModal()"
                        wire:click="startPolling"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Create Key
                    </button>
                @endif
            </div>
        </div>

        @if($license->subLicenses->isEmpty())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No keys</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first key.</p>
                </div>
            </div>
        @else
            {{-- Active Sub-Licenses --}}
            @if($activeSubLicenses->isNotEmpty())
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($activeSubLicenses as $subLicense)
                        <li class="px-4 py-4" wire:key="sublicense-{{ $subLicense->id }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            @if($subLicense->name)
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $subLicense->name }}
                                                    </p>
                                                </div>
                                            @endif
                                            <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-mono">
                                                    {{ $subLicense->key }}
                                                </code>
                                                <button
                                                    type="button"
                                                    onclick="copyToClipboard('{{ $subLicense->key }}')"
                                                    class="ml-2 text-xs text-blue-600 hover:text-blue-500"
                                                >
                                                    Copy
                                                </button>
                                            </div>
                                            @if($subLicense->assigned_email)
                                                <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="text-xs">Assigned to: {{ $subLicense->assigned_email }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex items-center space-x-2">
                                            @if($subLicense->assigned_email)
                                                <form method="POST" action="{{ route('customer.licenses.sub-licenses.send-email', [$license->key, $subLicense]) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-500 text-sm">
                                                        Send License
                                                    </button>
                                                </form>
                                            @endif
                                            <button
                                                type="button"
                                                onclick="showEditSubLicenseModal({{ $subLicense->id }}, '{{ $subLicense->name }}', '{{ $subLicense->assigned_email }}')"
                                                class="text-blue-600 hover:text-blue-500 text-sm"
                                            >
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('customer.licenses.sub-licenses.suspend', [$license->key, $subLicense]) }}" class="inline" onsubmit="return confirmSuspension(event)">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-500 text-sm">
                                                    Suspend
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Suspended Sub-Licenses --}}
            @if($suspendedSubLicenses->isNotEmpty())
                <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Suspended Keys
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        ({{ $suspendedSubLicenses->count() }})
                                    </span>
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                    These keys are permanently suspended and cannot be used or reactivated.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($suspendedSubLicenses as $subLicense)
                            <li class="px-4 py-4 bg-red-50 dark:bg-red-900/20" wire:key="suspended-sublicense-{{ $subLicense->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                @if($subLicense->name)
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $subLicense->name }}
                                                        </p>
                                                    </div>
                                                @endif
                                                <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                    <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-mono">
                                                        {{ $subLicense->key }}
                                                    </code>
                                                </div>
                                                @if($subLicense->assigned_email)
                                                    <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="text-xs">Assigned to: {{ $subLicense->assigned_email }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endif

        @if(!$license->canCreateSubLicense())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            @if($license->remainingSubLicenses === 0)
                                You have reached the maximum number of keys for this plan.
                            @elseif($license->is_suspended)
                                Keys cannot be created for suspended licenses.
                            @elseif($license->expires_at && $license->expires_at->isPast())
                                Keys cannot be created for expired licenses.
                            @else
                                Keys cannot be created at this time.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
