<x-layout title="Your Licenses">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-2 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Your Licenses</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage your NativePHP licenses
                        </p>
                    </div>
                    <x-dashboard-menu />
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if($licenses->count() > 0)
                <div class="overflow-hidden rounded-md bg-white shadow dark:bg-gray-800">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($licenses as $license)
                            @php
                                $isLegacyLicense = $license->isLegacy();
                                $daysUntilExpiry = $license->expires_at ? $license->expires_at->diffInDays(now()) : null;
                                $needsRenewal = $isLegacyLicense && $daysUntilExpiry !== null && !$license->expires_at->isPast();
                            @endphp
                            <li class="{{ $needsRenewal ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                <a href="{{ route('customer.licenses.show', $license->key) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 {{ $needsRenewal ? 'hover:bg-blue-100 dark:hover:bg-blue-900/30' : '' }}">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="shrink-0">
                                                    @if($license->is_suspended)
                                                        <div class="size-3 rounded-full bg-red-400"></div>
                                                    @elseif($license->expires_at && $license->expires_at->isPast())
                                                        <div class="size-3 rounded-full bg-yellow-400"></div>
                                                    @elseif($needsRenewal)
                                                        <div class="size-3 animate-pulse rounded-full bg-blue-400"></div>
                                                    @else
                                                        <div class="size-3 rounded-full bg-green-400"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="flex items-start">
                                                        <div class="flex flex-col">
                                                            @if($license->name)
                                                                <p class="truncate text-sm font-medium text-blue-600 dark:text-blue-400">
                                                                    {{ $license->name }}
                                                                </p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $license->policy_name }}
                                                                </p>
                                                            @else
                                                                <p class="truncate text-sm font-medium text-blue-600 dark:text-blue-400">
                                                                    {{ $license->policy_name }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        @if($license->is_suspended)
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                Suspended
                                                            </span>
                                                        @elseif($license->expires_at && $license->expires_at->isPast())
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Expired
                                                            </span>
                                                        @elseif($needsRenewal)
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                Needs Renewal
                                                            </span>
                                                        @else
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                Active
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-1 flex items-center gap-1" x-data="{ show: false }">
                                                        <p class="font-mono text-sm text-gray-500 dark:text-gray-400">
                                                            <span x-show="!show">{{ Str::mask($license->key, '*', 8, -4) }}</span>
                                                            <span x-show="show" x-cloak>{{ $license->key }}</span>
                                                        </p>
                                                        <button type="button" @click.prevent="show = !show" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg x-show="!show" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                            <svg x-show="show" x-cloak class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                @if($needsRenewal)
                                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                        Expires in {{ $daysUntilExpiry }} day{{ $daysUntilExpiry === 1 ? '' : 's' }}
                                                    </p>

                                                    @if($isLegacyLicense)
                                                        <p class="text-xs text-blue-500 dark:text-blue-300">
                                                            Lock in Early Access Pricing
                                                        </p>
                                                    @endif
                                                @else
                                                    <p class="text-sm text-gray-900 dark:text-white">
                                                        @if($license->expires_at)
                                                            Expires {{ $license->expires_at->format('M j, Y') }}
                                                        @else
                                                            No expiration
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        Created {{ $license->created_at->format('M j, Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Assigned Sub-Licenses --}}
            @if($assignedSubLicenses->count() > 0)
                <div class="mt-8">
                    <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Assigned Sub-Licenses</h2>
                    <div class="overflow-hidden rounded-md bg-white shadow dark:bg-gray-800">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($assignedSubLicenses as $subLicense)
                                <li>
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="shrink-0">
                                                    @if($subLicense->is_suspended)
                                                        <div class="size-3 rounded-full bg-red-400"></div>
                                                    @elseif($subLicense->expires_at && $subLicense->expires_at->isPast())
                                                        <div class="size-3 rounded-full bg-yellow-400"></div>
                                                    @else
                                                        <div class="size-3 rounded-full bg-green-400"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="flex items-start">
                                                        <div class="flex flex-col">
                                                            <p class="truncate text-sm font-medium text-blue-600 dark:text-blue-400">
                                                                {{ $subLicense->parentLicense->policy_name ?? 'Sub-License' }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                Sub-license
                                                            </p>
                                                        </div>
                                                        @if($subLicense->is_suspended)
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                Suspended
                                                            </span>
                                                        @elseif($subLicense->expires_at && $subLicense->expires_at->isPast())
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Expired
                                                            </span>
                                                        @else
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                Active
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-1 flex items-center gap-1" x-data="{ show: false }">
                                                        <p class="font-mono text-sm text-gray-500 dark:text-gray-400">
                                                            <span x-show="!show">{{ Str::mask($subLicense->key, '*', 8, -4) }}</span>
                                                            <span x-show="show" x-cloak>{{ $subLicense->key }}</span>
                                                        </p>
                                                        <button type="button" @click.prevent="show = !show" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg x-show="!show" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                            <svg x-show="show" x-cloak class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    @if($subLicense->expires_at)
                                                        Expires {{ $subLicense->expires_at->format('M j, Y') }}
                                                    @else
                                                        No expiration
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Assigned {{ $subLicense->created_at->format('M j, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($licenses->count() === 0 && $assignedSubLicenses->count() === 0)
                <div class="text-center">
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <x-heroicon-o-key class="mx-auto size-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No licenses found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            You don't have any licenses yet. If you believe this is an error, please contact support.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
