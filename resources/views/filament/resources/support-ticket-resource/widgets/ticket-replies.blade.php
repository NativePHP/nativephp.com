<x-filament-widgets::widget>
    <x-filament::section heading="Conversation">
        {{-- Reply form at top --}}
        <form wire:submit="sendReply" class="mb-6">
            <div class="space-y-3">
                <textarea
                    wire:model="newMessage"
                    rows="3"
                    placeholder="Type your reply..."
                    style="border-color: #d1d5db; background: white;"
                    class="fi-textarea block w-full rounded-lg shadow-sm transition duration-75"
                    onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 1px #7c3aed inset'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                ></textarea>
                @error('newMessage')
                    <p style="color: #dc2626;" class="text-sm">{{ $message }}</p>
                @enderror
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm" style="color: #6b7280;">
                        <input type="checkbox" wire:model="isNote" class="fi-checkbox-input rounded" />
                        Internal note (not visible to user)
                    </label>
                    <button type="submit" wire:loading.attr="disabled" style="background-color: #7c3aed; color: white;" class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold shadow-sm disabled:opacity-50"
                        onmouseover="this.style.backgroundColor='#6d28d9'"
                        onmouseout="this.style.backgroundColor='#7c3aed'"
                    >
                        <span wire:loading.remove wire:target="sendReply">Send Reply</span>
                        <span wire:loading wire:target="sendReply">Sending...</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Messages list (reverse chronological) --}}
        <div class="space-y-3">
            @forelse ($record->replies()->with('user')->orderBy('created_at', 'desc')->get() as $reply)
                @php
                    $isAdmin = $reply->user?->isAdmin();
                    $isNote = $reply->note;
                @endphp
                <div
                    class="rounded-lg border p-3"
                    @if ($isNote)
                        style="background-color: #fefce8; border-color: #fbbf24;"
                    @elseif ($isAdmin)
                        style="background-color: #ede9fe; border-color: #c4b5fd;"
                    @else
                        style="background-color: #f9fafb; border-color: #e5e7eb;"
                    @endif
                >
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold" style="color: #111827;">
                                {{ $reply->user?->name ?? 'Unknown' }}
                            </span>
                            @if ($isNote)
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" style="background-color: #fef3c7; color: #92400e;">Note</span>
                            @elseif ($isAdmin)
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" style="background-color: #ddd6fe; color: #5b21b6;">Staff</span>
                            @endif
                        </div>
                        <span class="text-xs" style="color: #6b7280;">
                            {{ $reply->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div class="mt-1 whitespace-pre-line text-sm" style="color: #374151;">{{ $reply->message }}</div>
                </div>
            @empty
                <p class="py-4 text-center text-sm" style="color: #6b7280;">No replies yet.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
