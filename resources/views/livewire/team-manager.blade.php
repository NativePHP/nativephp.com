<div>
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Team Seats
                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                            ({{ $team->occupiedSeatCount() }} / {{ $team->totalSeatCapacity() }})
                        </span>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Manage your team's seat capacity. {{ $team->availableSeats() }} seat(s) available.
                    </p>
                </div>
            </div>
        </div>

        {{-- Seat Management Controls --}}
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-4">
            <div class="flex items-center gap-4 flex-wrap">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-gray-900 dark:text-white">{{ \App\Models\Team::INCLUDED_SEATS }}</span> included
                    @if($team->extra_seats > 0)
                        + <span class="font-medium text-gray-900 dark:text-white">{{ $team->extra_seats }}</span> extra
                    @endif
                </div>

                @if($extraSeatPrice && $billingInterval)
                    <div class="flex items-center gap-2" x-data="{ showConfirm: false, qty: 1 }" @seats-updated.window="showConfirm = false; qty = 1">
                        <button
                            @click="showConfirm = true"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            Add seats (${{ $extraSeatPrice }}/{{ $billingInterval === 'year' ? 'yr' : 'mo' }})
                        </button>

                        @if($team->extra_seats > 0 && $team->availableSeats() > 0)
                            <div x-data="{ showRemoveConfirm: false, removeQty: 1 }" @seats-updated.window="showRemoveConfirm = false; removeQty = 1">
                                <button
                                    @click="showRemoveConfirm = true"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                >
                                    Remove seats
                                </button>

                                {{-- Remove Seats Confirmation Modal --}}
                                <div
                                    x-show="showRemoveConfirm"
                                    x-transition.opacity
                                    x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                                    @keydown.escape.window="showRemoveConfirm = false"
                                >
                                    <div
                                        @click.outside="showRemoveConfirm = false"
                                        x-transition
                                        class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800"
                                    >
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            Remove extra seats
                                        </h3>
                                        <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-400">
                                            <div>
                                                <label for="remove-seat-qty" class="block font-medium text-gray-700 dark:text-gray-300">Number of seats to remove</label>
                                                <div class="mt-1.5 flex items-center gap-2">
                                                    <button
                                                        @click="removeQty = Math.max(1, removeQty - 1)"
                                                        type="button"
                                                        class="grid size-8 place-items-center rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                                    >
                                                        &minus;
                                                    </button>
                                                    <input
                                                        id="remove-seat-qty"
                                                        type="number"
                                                        min="1"
                                                        max="{{ min($team->extra_seats, $team->availableSeats()) }}"
                                                        x-model.number="removeQty"
                                                        @input="removeQty = Math.max(1, Math.min({{ min($team->extra_seats, $team->availableSeats()) }}, parseInt($event.target.value) || 1))"
                                                        class="w-16 rounded-md border-gray-300 text-center text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                                    >
                                                    <button
                                                        @click="removeQty = Math.min({{ min($team->extra_seats, $team->availableSeats()) }}, removeQty + 1)"
                                                        type="button"
                                                        class="grid size-8 place-items-center rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                                    >
                                                        +
                                                    </button>
                                                    <span class="text-gray-500 dark:text-gray-400">
                                                        of {{ min($team->extra_seats, $team->availableSeats()) }} removable
                                                    </span>
                                                </div>
                                            </div>
                                            <p>
                                                Any unused time on removed seats will be credited to your account and applied to future invoices.
                                            </p>
                                        </div>
                                        <div class="mt-5 flex items-center justify-end gap-3">
                                            <button
                                                @click="showRemoveConfirm = false; removeQty = 1"
                                                type="button"
                                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                                wire:loading.attr="disabled"
                                                wire:target="removeSeats"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                @click="$wire.removeSeats(removeQty)"
                                                wire:loading.attr="disabled"
                                                wire:target="removeSeats"
                                                type="button"
                                                class="inline-flex items-center gap-2 rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50"
                                            >
                                                <svg wire:loading wire:target="removeSeats" class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span wire:loading.remove wire:target="removeSeats">Remove &amp; Credit</span>
                                                <span wire:loading wire:target="removeSeats">Removing...</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Add Seat Confirmation Modal --}}
                        <div
                            x-show="showConfirm"
                            x-transition.opacity
                            x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                            @keydown.escape.window="showConfirm = false"
                        >
                            <div
                                @click.outside="showConfirm = false"
                                x-transition
                                class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800"
                            >
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Add extra seats
                                </h3>
                                <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div>
                                        <label for="seat-qty" class="block font-medium text-gray-700 dark:text-gray-300">Number of seats</label>
                                        <div class="mt-1.5 flex items-center gap-2">
                                            <button
                                                @click="qty = Math.max(1, qty - 1)"
                                                type="button"
                                                class="grid size-8 place-items-center rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                            >
                                                &minus;
                                            </button>
                                            <input
                                                id="seat-qty"
                                                type="number"
                                                min="1"
                                                x-model.number="qty"
                                                @input="qty = Math.max(1, parseInt($event.target.value) || 1)"
                                                class="w-16 rounded-md border-gray-300 text-center text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                            >
                                            <button
                                                @click="qty++"
                                                type="button"
                                                class="grid size-8 place-items-center rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                            >
                                                +
                                            </button>
                                            <span class="text-gray-500 dark:text-gray-400">
                                                &times; ${{ $extraSeatPrice }}/{{ $billingInterval === 'year' ? 'yr' : 'mo' }}
                                            </span>
                                        </div>
                                    </div>
                                    <p>
                                        Total:
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="'$' + (qty * {{ $extraSeatPrice }}) + '/{{ $billingInterval === 'year' ? 'year' : 'month' }}'"></span>
                                        charged to your payment method on file.
                                    </p>
                                    <p>
                                        Payment will be taken immediately and the seats will be added to your subscription's recurring billing.
                                    </p>
                                </div>
                                <div class="mt-5 flex items-center justify-end gap-3">
                                    <button
                                        @click="showConfirm = false; qty = 1"
                                        type="button"
                                        class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                        wire:loading.attr="disabled"
                                        wire:target="addSeats"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="$wire.addSeats(qty)"
                                        wire:loading.attr="disabled"
                                        wire:target="addSeats"
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                    >
                                        <svg wire:loading wire:target="addSeats" class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="addSeats">Confirm &amp; Pay</span>
                                        <span wire:loading wire:target="addSeats">Processing payment...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @error('seats')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Members List --}}
        @if($team->members->isNotEmpty())
            <div class="border-t border-gray-200 dark:border-gray-700">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($team->members as $member)
                        <li class="px-4 py-4" wire:key="member-{{ $member->id }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $member->email }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ ucfirst($member->role) }}
                                        &middot;
                                        @if($member->isActive())
                                            <span class="text-green-600 dark:text-green-400">Active</span>
                                            @if($member->accepted_at)
                                                &middot; Joined {{ $member->accepted_at->diffForHumans() }}
                                            @endif
                                        @elseif($member->isPending())
                                            <span class="text-yellow-600 dark:text-yellow-400">Pending invitation</span>
                                            @if($member->invited_at)
                                                &middot; Invited {{ $member->invited_at->diffForHumans() }}
                                            @endif
                                        @else
                                            <span class="text-gray-500">{{ ucfirst($member->status) }}</span>
                                        @endif
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('customer.team.members.destroy', [$team, $member]) }}" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500 text-sm">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No team members</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Invite your first team member below.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Invite Form --}}
    @if($team->hasAvailableSeats())
        <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Invite a team member
                </h3>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-4">
                <form method="POST" action="{{ route('customer.team.members.store', $team) }}" class="flex items-end gap-4">
                    @csrf
                    <div class="flex-1">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email address</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="colleague@example.com"
                        >
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Invite
                    </button>
                </form>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endif
</div>
