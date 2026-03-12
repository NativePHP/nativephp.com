<div>
    @if($team->is_suspended)
        <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Your team is currently suspended. Reactivate your Ultra subscription to restore team benefits.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Invite Form --}}
    @if(!$team->is_suspended)
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Invite a Team Member</h3>
            <form method="POST" action="{{ route('customer.team.invite') }}" class="mt-4 flex gap-4">
                @csrf
                <div class="flex-1">
                    <input
                        type="email"
                        name="email"
                        placeholder="email@example.com"
                        required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    >
                </div>
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Send Invite
                </button>
            </form>
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- Seat Management --}}
    <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Seats</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $team->occupiedSeatCount() }} of {{ $team->totalSeatCapacity() }} seats used
                    @if($team->extra_seats > 0)
                        <span class="text-indigo-600 dark:text-indigo-400">({{ $team->extra_seats }} extra)</span>
                    @endif
                </p>
            </div>
            @if(!$team->is_suspended)
                <div class="flex items-center gap-2" x-data="{ showAddModal: {{ session('show_add_seats') ? 'true' : 'false' }}, showRemoveModal: false, addQty: 1, removeQty: 1 }" @seats-updated.window="showAddModal = false; showRemoveModal = false; addQty = 1; removeQty = 1">
                    <button
                        type="button"
                        @click="showAddModal = true"
                        class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Seats
                    </button>
                    @if($removableSeats > 0)
                        <button
                            type="button"
                            @click="showRemoveModal = true"
                            class="inline-flex items-center gap-1.5 rounded-md bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                            Remove Seats
                        </button>
                    @endif

                    {{-- Add Seats Modal --}}
                    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500/60 backdrop-blur-sm dark:bg-gray-900/60" @click.self="showAddModal = false">
                        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800" @click.stop>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Extra Seats</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Extra seats cost ${{ $extraSeatPriceMonthly }}/mo or ${{ $extraSeatPriceYearly }}/yr per seat, matching your current billing interval.
                            </p>
                            <div class="mt-4 flex items-center gap-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input type="number" x-model.number="addQty" min="1" max="50" class="w-20 rounded-md border-gray-300 text-center shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" />
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="showAddModal = false" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    @click="$wire.addSeats(addQty)"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    <svg wire:loading wire:target="addSeats" class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span wire:loading.remove wire:target="addSeats">Confirm</span>
                                    <span wire:loading wire:target="addSeats">Processing...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Remove Seats Modal --}}
                    <div x-show="showRemoveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500/60 backdrop-blur-sm dark:bg-gray-900/60" @click.self="showRemoveModal = false">
                        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800" @click.stop>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Remove Extra Seats</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                You currently have {{ $team->extra_seats }} extra seat(s). Seats are removed immediately and you'll be credited for the unused time on your next bill.
                            </p>
                            <div class="mt-4 flex items-center gap-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input type="number" x-model.number="removeQty" min="1" :max="{{ $removableSeats }}" class="w-20 rounded-md border-gray-300 text-center shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" />
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="showRemoveModal = false" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    @click="$wire.removeSeats(removeQty)"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                                >
                                    <svg wire:loading wire:target="removeSeats" class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span wire:loading.remove wire:target="removeSeats">Remove</span>
                                    <span wire:loading wire:target="removeSeats">Processing...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Pending Invitations --}}
    @if($pendingInvitations->isNotEmpty())
        <div class="mb-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
                Pending Invitations
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                    ({{ $pendingInvitations->count() }})
                </span>
            </h3>
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pendingInvitations as $invitation)
                        <li class="px-4 py-4" wire:key="invitation-{{ $invitation->id }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $invitation->email }}
                                    </p>
                                    @if($invitation->invited_at)
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                            Invited {{ $invitation->invited_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <form method="POST" action="{{ route('customer.team.users.resend', $invitation) }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                            Resend
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('customer.team.users.remove', $invitation) }}" onsubmit="return confirm('Cancel this invitation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Team Members --}}
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Team Members
        </h3>
    </div>

    {{-- Active Members --}}
    @if($activeMembers->isNotEmpty())
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($activeMembers as $member)
                    <li class="px-4 py-4" wire:key="member-{{ $member->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $member->user?->display_name ?? $member->email }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $member->email }}
                                </p>
                                @if($member->accepted_at)
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        Joined {{ $member->accepted_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('customer.team.users.remove', $member) }}" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="rounded-lg bg-white p-8 text-center shadow dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">No active team members yet. Invite someone to get started.</p>
        </div>
    @endif
</div>
