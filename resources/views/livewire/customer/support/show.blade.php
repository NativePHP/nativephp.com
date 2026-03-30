<div>
    <div class="mb-6">
        <a href="{{ route('customer.support.tickets') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
            <x-heroicon-s-arrow-left class="size-4" />
            <span class="font-medium">Support Tickets</span>
        </a>
        <div class="mt-4 flex items-center justify-between">
            <div>
                <flux:heading size="xl">#{{ $supportTicket->mask }} &raquo; {{ $supportTicket->subject }}</flux:heading>
                <div class="mt-1 flex items-center gap-3">
                    <x-customer.status-badge :status="$supportTicket->status->translated()" />
                    <flux:text>Created {{ $supportTicket->created_at->format('d M Y, H:i') }}</flux:text>
                </div>
            </div>
            @if($supportTicket->status !== \App\SupportTicket\Status::CLOSED)
                <flux:button wire:click="closeTicket" wire:confirm="Are you sure you want to close this ticket?" icon="x-mark" variant="ghost">
                    Close Ticket
                </flux:button>
            @endif
        </div>
    </div>

    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Submission Details --}}
    <flux:accordion transition class="mb-6">
        <flux:accordion.item>
            <flux:accordion.heading>Submission Details</flux:accordion.heading>
            <flux:accordion.content>
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
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>

    {{-- Messages --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">Messages</flux:heading>

        {{-- Reply Form --}}
        @if($supportTicket->status !== \App\SupportTicket\Status::CLOSED)
            <div class="mb-6 rounded-lg border border-zinc-300 bg-zinc-100 p-4 dark:border-zinc-600 dark:bg-zinc-800">
                <form wire:submit="reply">
                    <flux:textarea
                        wire:model="replyMessage"
                        label="Add a reply"
                        rows="3"
                        placeholder="Type your reply here..."
                        @keydown.meta.enter="$wire.reply()"
                        @keydown.ctrl.enter="$wire.reply()"
                    />
                    <div class="mt-3 flex items-center justify-end gap-3">
                        <flux:text class="text-xs">&#8984;/Ctrl + Enter to send</flux:text>
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
                        <div class="prose prose-sm mt-1 max-w-none text-zinc-800 dark:prose-invert dark:text-zinc-200">{!! App\Support\CommonMark\CommonMark::convertToHtml($reply->message) !!}</div>
                    </div>
                </div>
                <div class="mt-1 {{ $reply->is_from_user ? 'text-right mr-10' : 'text-left ml-10' }}">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $reply->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        @endforeach
    </flux:card>
</div>
