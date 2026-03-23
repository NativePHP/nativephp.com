<x-layout title="Ticket - NativePHP">
    <section class="mx-auto mt-10 max-w-5xl px-5 md:mt-14">
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

        {{-- Submission Details (collapsible) --}}
        <div class="mt-6 rounded-lg bg-white shadow dark:bg-gray-800" x-data="{ open: false }">
            <button
                type="button"
                @click="open = !open"
                class="flex w-full items-center justify-between p-6 text-left"
            >
                <h2 class="text-xl font-medium">Submission Details</h2>
                <svg
                    class="h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-400"
                    :class="{ 'rotate-180': open }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse x-cloak>
                <div class="border-t border-gray-200 p-6 pt-4 dark:border-gray-700">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Product</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ ucfirst($supportTicket->product) }}</dd>
                        </div>
                        @if($supportTicket->issue_type)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Issue Type</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ str_replace('_', ' ', ucfirst($supportTicket->issue_type)) }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if(! in_array($supportTicket->product, ['mobile', 'desktop']))
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Original Message</dt>
                            <dd class="mt-1 whitespace-pre-line text-gray-900 dark:text-gray-100">{{ $supportTicket->message }}</dd>
                        </div>
                    @endif

                    @if($supportTicket->metadata)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Additional Details</dt>
                            <dd class="mt-1">
                                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @foreach($supportTicket->metadata as $key => $value)
                                        <div>
                                            <dt class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ str_replace('_', ' ', ucfirst($key)) }}</dt>
                                            <dd class="mt-0.5 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ $value }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ticket Messages --}}
        <div class="mt-6 rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="p-6">
                <h2 class="mb-4 text-xl font-medium">Messages</h2>

                {{-- Inline Reply Form --}}
                @if($supportTicket->status !== \App\SupportTicket\Status::CLOSED)
                    <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700/30">
                        <form action="{{ route('support.tickets.reply', $supportTicket) }}" method="POST">
                            @csrf
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Add a reply
                            </label>
                            <textarea
                                id="message"
                                name="message"
                                rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="Type your reply here..."
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex justify-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-lg border border-transparent bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 dark:bg-violet-700 dark:hover:bg-violet-600"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                @foreach($supportTicket->replies->where('note', false) as $reply)
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
        @if($supportTicket->status !== \App\SupportTicket\Status::CLOSED)
            <button type="button" onclick="document.getElementById('closeTicketForm').submit()" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 mx-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Close
            </button>
        @endif
    </div>

    {{-- Add padding at the bottom to prevent content from being hidden behind the mobile footer --}}
    <div class="md:hidden h-16"></div>

    {{-- Close ticket form --}}
    <form action="{{ route('support.tickets.close', $supportTicket) }}" method="POST" id="closeTicketForm">
        @csrf
    </form>
    </section>
</x-layout>
