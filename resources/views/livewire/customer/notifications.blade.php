<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Notifications</flux:heading>
            <flux:text>Stay up to date with your account activity.</flux:text>
        </div>

        <div class="flex items-center gap-2">
            @if (auth()->user()->unreadNotifications->count() > 0)
                <flux:button wire:click="markAllAsRead" variant="ghost" size="sm">
                    Mark all as read
                </flux:button>
            @endif

            <flux:button href="{{ route('customer.settings', ['tab' => 'notifications']) }}" variant="ghost" size="sm" icon="cog-6-tooth">
                Settings
            </flux:button>
        </div>
    </div>

    @forelse ($this->notifications as $notification)
        <flux:card wire:key="notification-{{ $notification->id }}" class="mb-3">
            <div class="flex items-start gap-3">
                {{-- Unread indicator --}}
                <div class="mt-1.5 shrink-0">
                    @if (is_null($notification->read_at))
                        <div class="size-2.5 rounded-full bg-blue-500"></div>
                    @else
                        <div class="size-2.5"></div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <flux:heading size="sm" class="{{ is_null($notification->read_at) ? 'font-semibold' : 'font-normal' }}">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </flux:heading>
                        <flux:text class="shrink-0 text-xs">
                            {{ $notification->created_at->diffForHumans() }}
                        </flux:text>
                    </div>

                    @if (! empty($notification->data['body']))
                        <flux:text class="mt-1">{{ $notification->data['body'] }}</flux:text>
                    @endif

                    @if (is_null($notification->read_at))
                        <div class="mt-2">
                            <flux:button wire:click="markAsRead('{{ $notification->id }}')" variant="ghost" size="xs">
                                Mark as read
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>
        </flux:card>
    @empty
        <flux:card>
            <div class="py-8 text-center">
                <flux:icon.bell class="mx-auto mb-3 size-8 text-zinc-400" />
                <flux:heading size="sm">No notifications</flux:heading>
                <flux:text>You're all caught up!</flux:text>
            </div>
        </flux:card>
    @endforelse

    <div class="mt-4">
        {{ $this->notifications->links() }}
    </div>
</div>
