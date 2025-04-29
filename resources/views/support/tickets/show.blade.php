<x-layout-three-columns>
    {{-- Desktop Buttons - Hidden on Mobile --}}
    <div class="hidden md:flex justify-end mb-4 space-x-3">
        <a href="/support/tickets" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Tickets
        </a>
        <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-violet-700 bg-violet-100 border border-violet-200 rounded-lg hover:bg-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:border-violet-800 dark:hover:bg-violet-900/50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
            Reply
        </button>
        <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Close Ticket
        </button>
    </div>
    <section class="mt-6">
        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-medium">#{{ $supportTicket->mask }} &raquo; {{ $supportTicket->subject }}</h2>
                </div>
                <p class="text-gray-700 dark:text-gray-300">
                    Ticket ID: <strong>#{{ $supportTicket->mask }}</strong><br>
                    Status: <strong>{{ $supportTicket->status->translated() }}</strong><br>
                    Created At: <strong>{{ $supportTicket->created_at->format('d M Y, H:i') }}</strong><br>
                    Updated At: <strong>{{ $supportTicket->updated_at->format('d M Y, H:i') }}</strong>
                </p>
            </div>
        </div>

        {{-- Ticket Messages --}}
        <div class="mt-6 rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="p-6">
                <h2 class="mb-4 text-xl font-medium">Messages</h2>
                @foreach($supportTicket->replies as $reply)
                    <div class="flex flex-col w-full mb-6">
                        <div class="relative w-full">
                            <div class="{{ $reply->is_from_user ? 'bg-blue-100/50 dark:bg-blue-900/20' : 'bg-gray-100/70 dark:bg-gray-700/20' }} p-4 rounded-lg border {{ $reply->is_from_user ? 'border-blue-200/50 dark:border-blue-800/30' : 'border-gray-200/50 dark:border-gray-700/30' }}">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $reply->user->name }}</p>
                                <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $reply->message }}</p>
                            </div>
                        </div>
                        <div class="mt-1 {{ $reply->is_from_user ? 'text-right' : 'text-left' }}">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Additional Support Information --}}
        <div class="mt-20 rounded-xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] p-8 dark:from-blue-900/10 dark:to-[#4c407f]/25">
            <h2 class="mb-4 text-2xl font-medium">Need more help?</h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                Check out our <a href="/docs" class="font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">documentation</a> for comprehensive guides and tutorials to help you get the most out of NativePHP.
            </p>
        </div>
    </section>
    
    {{-- Mobile Footer - Visible only on Mobile --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-3 flex justify-between z-50">
        <a href="/support/tickets" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 mx-1">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
        <button type="button" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-violet-700 bg-violet-100 border border-violet-200 rounded-lg hover:bg-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:border-violet-800 dark:hover:bg-violet-900/50 mx-1">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
            Reply
        </button>
        <button type="button" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 mx-1">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Close
        </button>
    </div>
    
    {{-- Add padding at the bottom to prevent content from being hidden behind the mobile footer --}}
    <div class="md:hidden h-16"></div>
</x-layout-three-columns>
