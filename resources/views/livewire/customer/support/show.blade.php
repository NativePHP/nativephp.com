<div>
    <div class="mb-6 flex items-center justify-between">
        <flux:button href="{{ route('customer.support.tickets') }}" icon="arrow-left">Back to Tickets</flux:button>

        @if($supportTicket->status !== \App\SupportTicket\Status::CLOSED)
            <flux:button wire:click="closeTicket" wire:confirm="Are you sure you want to close this ticket?" icon="x-mark">
                Close Ticket
            </flux:button>
        @endif
    </div>

    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Ticket Header --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">#{{ $supportTicket->mask }} &raquo; {{ $supportTicket->subject }}</flux:heading>
        </div>
        <div class="space-y-1">
            <flux:text>Ticket ID: <strong>#{{ $supportTicket->mask }}</strong></flux:text>
            <flux:text>Status: <x-customer.status-badge :status="$supportTicket->status->translated()" /></flux:text>
            <flux:text>Created: <strong>{{ $supportTicket->created_at->format('d M Y, H:i') }}</strong></flux:text>
            <flux:text>Updated: <strong>{{ $supportTicket->updated_at->format('d M Y, H:i') }}</strong></flux:text>
        </div>
    </flux:card>

    {{-- Submission Details (collapsible) --}}
    <flux:card class="mb-6" x-data="{ open: false }">
        <button
            type="button"
            @click="open = !open"
            class="flex w-full items-center justify-between text-left"
        >
            <flux:heading size="lg">Submission Details</flux:heading>
            <svg
                class="h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-400"
                :class="{ 'rotate-180': open }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-collapse x-cloak>
            <div class="mt-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Product</dt>
                        <dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ ucfirst($supportTicket->product) }}</dd>
                    </div>
                    @if($supportTicket->issue_type)
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Issue Type</dt>
                            <dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ str_replace('_', ' ', ucfirst($supportTicket->issue_type)) }}</dd>
                        </div>
                    @endif
                </dl>

                @if(! in_array($supportTicket->product, ['mobile', 'desktop']))
                    <div class="mt-4">
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Original Message</dt>
                        <dd class="mt-1 whitespace-pre-line text-zinc-900 dark:text-zinc-100">{{ $supportTicket->message }}</dd>
                    </div>
                @endif

                @if($supportTicket->metadata)
                    <div class="mt-4">
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Additional Details</dt>
                        <dd class="mt-1">
                            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($supportTicket->metadata as $key => $value)
                                    <div>
                                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ str_replace('_', ' ', ucfirst($key)) }}</dt>
                                        <dd class="mt-0.5 whitespace-pre-line text-sm text-zinc-900 dark:text-zinc-100">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </dd>
                    </div>
                @endif
            </div>
        </div>
    </flux:card>

    {{-- Messages --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">Messages</flux:heading>

        {{-- Reply Form --}}
        @if($supportTicket->status !== \App\SupportTicket\Status::CLOSED)
            <div class="mb-6 rounded-lg border border-zinc-300 bg-zinc-100 p-4 dark:border-zinc-600 dark:bg-zinc-800">
                <form wire:submit="reply">
                    <label for="replyMessage" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Add a reply
                    </label>
                    <textarea
                        id="replyMessage"
                        wire:model="replyMessage"
                        rows="3"
                        placeholder="Type your reply here..."
                        class="block w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-zinc-400 focus:border-violet-500 focus:ring-violet-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white dark:placeholder:text-zinc-500"
                    ></textarea>
                    @error('replyMessage')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-3 flex justify-end">
                        <flux:button type="submit" variant="primary" icon="arrow-uturn-left">
                            Send Reply
                        </flux:button>
                    </div>
                </form>
            </div>
        @endif

        @foreach($supportTicket->replies->where('note', false) as $reply)
            <div class="flex flex-col w-full mb-6" wire:key="reply-{{ $reply->id }}">
                <div class="relative w-full">
                    <div class="{{ $reply->is_from_user ? 'bg-blue-100 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700 mr-10' : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600 ml-10' }} p-4 rounded-lg border">
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $reply->user->name }}
                            @if($reply->is_from_user)
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">(You)</span>
                            @elseif($reply->is_from_admin)
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">(Staff)</span>
                            @endif
                        </p>
                        <p class="mt-1 text-zinc-800 dark:text-zinc-200">{{ $reply->message }}</p>
                    </div>
                </div>
                <div class="mt-1 {{ $reply->is_from_user ? 'text-right mr-10' : 'text-left ml-10' }}">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $reply->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        @endforeach
    </flux:card>
</div>
