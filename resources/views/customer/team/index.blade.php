<x-layout title="Team">
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
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Team</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage your team and share your Ultra benefits
                        </p>
                    </div>
                    <x-dashboard-menu />
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if(session()->has('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700 dark:bg-green-900/20">
                    <div class="flex items-center">
                        <svg class="mr-2 h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700 dark:bg-red-900/20">
                    <div class="flex items-center">
                        <svg class="mr-2 h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($team)
                {{-- User owns a team --}}
                <livewire:team-manager :team="$team" />
            @elseif($membership)
                {{-- User is a member of another team --}}
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Team Membership</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        You are a member of <strong class="text-gray-900 dark:text-white">{{ $membership->team->name }}</strong>.
                    </p>
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Your benefits include:</h4>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600 dark:text-gray-400">
                            <li>Free access to all first-party NativePHP plugins</li>
                            <li>Subscriber-tier pricing on third-party plugins</li>
                            <li>Access to the Plugin Dev Kit GitHub repository</li>
                        </ul>
                    </div>
                </div>
            @elseif(auth()->user()->hasActiveUltraSubscription())
                {{-- User has Ultra but no team --}}
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create a Team</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        As an Ultra subscriber, you can create a team and invite up to 10 members who will share your benefits.
                    </p>
                    <form method="POST" action="{{ route('customer.team.store') }}" class="mt-4">
                        @csrf
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label for="team-name" class="sr-only">Team Name</label>
                                <input
                                    id="team-name"
                                    type="text"
                                    name="name"
                                    placeholder="Team name"
                                    required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                >
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Create Team
                            </button>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            @else
                {{-- User doesn't have Ultra --}}
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Teams</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Teams are available to Ultra subscribers. Upgrade to Ultra to create a team and share benefits with up to 10 members.
                    </p>
                    <div class="mt-4">
                        <a
                            href="{{ route('pricing') }}"
                            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            View Plans
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
