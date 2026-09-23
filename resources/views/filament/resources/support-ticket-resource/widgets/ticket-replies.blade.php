<x-filament-widgets::widget>
    <style>
        .ticket-reply-message code {
            background-color: #1f2937;
            color: #e5e7eb;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.8125rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .ticket-reply-message pre {
            background-color: #1f2937;
            color: #e5e7eb;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 0.5rem 0;
        }
        .ticket-reply-message pre code {
            background-color: transparent;
            padding: 0;
            border-radius: 0;
            font-size: 0.8125rem;
        }
    </style>
    <x-filament::section heading="Conversation">
        {{-- Pinned Note --}}
        @php
            $pinnedNote = $record->replies()->with('user')->where('note', true)->where('pinned', true)->first();
        @endphp
        @if ($pinnedNote)
            <div style="border-radius: 0.5rem; border: 2px solid #f59e0b; padding: 0.75rem; background-color: #fffbeb; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <x-filament::badge color="warning" size="sm">Pinned Note</x-filament::badge>
                        <span style="font-size: 0.875rem; font-weight: 600; color: #111827;">
                            {{ $pinnedNote->user?->name ?? 'System' }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.75rem; color: #6b7280;">
                            {{ $pinnedNote->created_at->diffForHumans() }}
                        </span>
                        <x-filament::button size="xs" color="gray" wire:click="togglePin({{ $pinnedNote->id }})">
                            Unpin
                        </x-filament::button>
                    </div>
                </div>
                <div class="fi-prose ticket-reply-message" style="margin-top: 0.25rem; font-size: 0.875rem; color: #374151;">{!! App\Support\CommonMark\CommonMark::convertToHtml($pinnedNote->message) !!}</div>
            </div>
        @endif

        {{-- Reply form --}}
        <form wire:submit="sendReply" style="margin-bottom: 1.5rem;">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <textarea
                    wire:model="newMessage"
                    rows="3"
                    placeholder="Type your reply..."
                    style="display: block; width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; color: #111827; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                    onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 1px #7c3aed'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='0 1px 2px rgba(0,0,0,0.05)'"
                    x-on:keydown.meta.enter="$wire.sendReply()"
                    x-on:keydown.ctrl.enter="$wire.sendReply()"
                ></textarea>
                @error('newMessage')
                    <p style="font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
                <div>
                    <input
                        type="file"
                        wire:model="replyAttachments"
                        multiple
                        style="display: block; width: 100%; font-size: 0.875rem; color: #6b7280;"
                    />
                    @error('replyAttachments')
                        <p style="font-size: 0.875rem; color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                    @error('replyAttachments.*')
                        <p style="font-size: 0.875rem; color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                    @if(count($replyAttachments))
                        <div style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.25rem;">
                            @foreach($replyAttachments as $index => $file)
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f9fafb; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8125rem;">
                                    <span style="color: #374151;">{{ $file->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeReplyAttachment({{ $index }})" style="color: #dc2626; cursor: pointer; background: none; border: none; font-size: 0.75rem;">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #6b7280; cursor: pointer;">
                        <input type="checkbox" wire:model="isNote" style="border-radius: 0.25rem;" />
                        Internal note (not visible to user)
                    </label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <x-filament::button type="submit" wire:loading.attr="disabled" size="sm">
                            <span wire:loading.remove wire:target="sendReply">Send Reply</span>
                            <span wire:loading wire:target="sendReply">Sending...</span>
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Messages list (reverse chronological) --}}
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @forelse ($record->replies()->with('user')->orderBy('created_at', 'desc')->get() as $reply)
                @php
                    $isAdmin = $reply->user?->isAdmin();
                    $isNote = $reply->note;

                    if ($isNote) {
                        $bgColor = '#fefce8';
                        $borderColor = '#facc15';
                    } elseif ($isAdmin) {
                        $bgColor = '#ede9fe';
                        $borderColor = '#c4b5fd';
                    } else {
                        $bgColor = '#f3f4f6';
                        $borderColor = '#d1d5db';
                    }
                @endphp
                <div style="border-radius: 0.5rem; border: 1px solid {{ $borderColor }}; padding: 0.75rem; background-color: {{ $bgColor }};">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            @if ($reply->user_id === null)
                                <x-filament::badge color="gray" size="sm">System</x-filament::badge>
                            @else
                                <span style="font-size: 0.875rem; font-weight: 600; color: #111827;">
                                    {{ $reply->user->name }}
                                </span>
                                @if ($isNote)
                                    <x-filament::badge color="warning" size="sm">Note</x-filament::badge>
                                    @if ($reply->pinned)
                                        <x-filament::badge color="success" size="sm">Pinned</x-filament::badge>
                                    @endif
                                @elseif ($isAdmin)
                                    <x-filament::badge color="primary" size="sm">Staff</x-filament::badge>
                                @endif
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.75rem; color: #6b7280;">
                                {{ $reply->created_at->diffForHumans() }}
                            </span>
                            @if ($isNote)
                                <x-filament::button size="xs" color="gray" wire:click="togglePin({{ $reply->id }})">
                                    {{ $reply->pinned ? 'Unpin' : 'Pin' }}
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                    <div class="fi-prose ticket-reply-message" style="margin-top: 0.25rem; font-size: 0.875rem; color: #374151;">{!! App\Support\CommonMark\CommonMark::convertToHtml($reply->message) !!}</div>
                    @if($reply->attachments)
                        <div style="margin-top: 0.5rem; border-top: 1px solid {{ $borderColor }}; padding-top: 0.5rem;">
                            @foreach($reply->attachments as $index => $attachment)
                                <a href="{{ route('customer.support.tickets.reply.attachment', [$record, $reply, $index]) }}" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.8125rem; color: #2563eb; text-decoration: none; margin-right: 0.75rem;" target="_blank">
                                    &#128206; {{ $attachment['name'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p style="padding: 1rem 0; text-align: center; font-size: 0.875rem; color: #6b7280;">No replies yet.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
