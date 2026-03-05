<x-layout title="Team Management">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Team Management</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage your team members and seats. Your plan includes {{ \App\Models\Team::INCLUDED_SEATS }} seats, with extra seats available for purchase.
                        </p>
                    </div>
                    <x-dashboard-menu />
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-12">
            <livewire:team-manager :team="$team" />
        </div>
    </div>
</x-layout>
