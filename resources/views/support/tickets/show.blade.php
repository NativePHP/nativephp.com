<x-layout-three-columns>
    <div x-data="{ showReplyModal: false }">
    {{-- Desktop Buttons - Hidden on Mobile --}}
    <div class="hidden md:flex justify-between items-center mb-4">
        <a href="/support/tickets" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Tickets
        </a>
        <div class="flex space-x-3">
            @if($supportTicket->status !== \App\SupportTicket\Status::CLOSED)
                <button
                    type="button"
                    @click="showReplyModal = true"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-violet-700 bg-violet-100 border border-violet-200 rounded-lg hover:bg-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:border-violet-800 dark:hover:bg-violet-900/50"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Reply
                </button>

                <button type="button" onclick="document.getElementById('closeTicketForm').submit()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Close Ticket
                </button>
            @endif
        </div>
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
                            <div class="{{ $reply->is_from_user ? 'bg-blue-100/50 dark:bg-blue-900/20 border-blue-200/50 dark:border-blue-800/30 mr-10' : 'bg-gray-100/70 dark:bg-gray-700/20 border-gray-200/50 dark:border-gray-700/30 ml-10' }} p-4 rounded-lg border">
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $reply->user->name }}
                                    @if($reply->is_from_user)
                                        <span class="text-sm text-gray-500 dark:text-gray-400">(You)</span>
                                    @elseif($reply->is_from_admin)
                                        <span class="text-sm text-gray-500 dark:text-gray-400">(Staff)</span>
                                    @endif
                                </p>
                                <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $reply->message }}</p>
                            </div>
                        </div>
                        <div class="mt-1 {{ $reply->is_from_user ? 'text-right mr-10' : 'text-left ml-10' }}">
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
        <button
            type="button"
            @click="showReplyModal = true"
            class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-violet-700 bg-violet-100 border border-violet-200 rounded-lg hover:bg-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:border-violet-800 dark:hover:bg-violet-900/50 mx-1"
        >
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

    {{-- Close ticket form --}}
    <form action="{{ route('support.tickets.close', $supportTicket) }}" method="POST" id="closeTicketForm">
        @csrf
    </form>

    {{-- Reply Modal --}}
    <div
        x-show="showReplyModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div
            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            {{-- Modal backdrop --}}
            <div
                class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"
                aria-hidden="true"
                @click="showReplyModal = false"
            ></div>

            {{-- Modal positioning trick --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal content --}}
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        type="button"
                        class="bg-white dark:bg-gray-800 rounded-md text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none"
                        @click="showReplyModal = false"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Reply to Ticket #{{ $supportTicket->mask }}
                            </h3>
                            <div class="mt-4">
                                <form action="#" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Your message
                                        </label>
                                        <textarea
                                            id="message"
                                            name="message"
                                            rows="5"
                                            class="shadow-sm focus:ring-violet-500 focus:border-violet-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                            placeholder="Type your reply here..."
                                            required
                                        ></textarea>
                                    </div>
                                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                                        <button
                                            type="submit"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm dark:bg-violet-700 dark:hover:bg-violet-600"
                                        >
                                            Send Reply
                                        </button>
                                        <button
                                            type="button"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                            @click="showReplyModal = false"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Alpine.js x-cloak style to hide elements with x-cloak before Alpine initializes --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
    </div>
</x-layout-three-columns>
