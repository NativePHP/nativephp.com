<x-layout title="Tickets - NativePHP">
    <section class="mx-auto mt-10 max-w-5xl px-5 md:mt-14">
        {{-- Header --}}
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-bold md:text-5xl dark:text-white/90">Support Tickets</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-white/60">
                Manage your support tickets.<br />
            </p>
        </header>

        {{-- Support ticket table --}}
        <div class="flex justify-center md:justify-end mb-4">
            <a href="#" class="w-full md:w-auto inline-flex items-center justify-center rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-violet-700 dark:bg-violet-700 dark:hover:bg-violet-600 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Submit a new request
            </a>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 mb-10">
            <!-- Desktop Table View (hidden on mobile, visible md and up) -->
            <table class="hidden md:table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Ticket ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Subject
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($supportTickets as $ticket)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                <a href="{{ route('support.tickets.show', $ticket) }}" class="text-violet-600">#{{ $ticket->mask }}</a>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $ticket->subject }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                {{ $ticket->status->translated() }}
                            </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('support.tickets.show', $ticket) }}" class="rounded-md bg-violet-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-violet-700 dark:bg-violet-700 dark:hover:bg-violet-600 transition duration-200">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                No tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Mobile Card View (visible on mobile, hidden md and up) -->
            <div class="md:hidden">
                @forelse($supportTickets as $ticket)
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                        <div class="flex flex-col space-y-3">
                            <!-- Subject (Priority on mobile) -->
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $ticket->subject }}
                            </div>
                            
                            <!-- Status (Priority on mobile) -->
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">Status:</span>
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    {{ $ticket->status->translated() }}
                                </span>
                            </div>
                            
                            <!-- Ticket ID (Less priority on mobile) -->
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">Ticket ID:</span>
                                <a href="{{ route('support.tickets.show', $ticket) }}" class="text-violet-600 text-sm">#{{ $ticket->mask }}</a>
                            </div>
                            
                            <!-- Actions -->
                            <div class="pt-2">
                                <a href="{{ route('support.tickets.show', $ticket) }}" class="inline-block rounded-md bg-violet-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-violet-700 dark:bg-violet-700 dark:hover:bg-violet-600 transition duration-200">
                                    View ticket
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 bg-white dark:bg-gray-900 text-sm font-medium text-gray-900 dark:text-white">
                        No tickets found.
                    </div>
                @endforelse
            </div>

            @if ($supportTickets->hasPages())
                <div class="px-3 py-4 md:p-5">
                    {{ $supportTickets->links() }}
                </div>
            @endif
        </div>
        {{-- Additional Support Information --}}
        <div class="mt-12 md:mt-20 rounded-xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] p-4 md:p-8 dark:from-blue-900/10 dark:to-[#4c407f]/25">
            <h2 class="mb-3 md:mb-4 text-xl md:text-2xl font-medium">Need more help?</h2>
            <p class="text-base md:text-lg text-gray-700 dark:text-gray-300">
                Check out our <a href="/docs" class="font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">documentation</a> for comprehensive guides and tutorials to help you get the most out of NativePHP.
            </p>
        </div>
    </section>
</x-layout>
