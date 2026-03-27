<div>
    @if($team->is_suspended)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
            <flux:callout.text>Your team is currently suspended. Reactivate your Ultra subscription to restore team benefits.</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Invite Form --}}
    @if(!$team->is_suspended)
        <flux:card class="mb-6">
            <flux:heading size="lg">Invite a Team Member</flux:heading>
            <form method="POST" action="{{ route('customer.team.invite') }}" class="mt-4 flex gap-4">
                @csrf
                <div class="flex-1">
                    <flux:input type="email" name="email" placeholder="email@example.com" required />
                </div>
                <flux:button type="submit" variant="primary">Send Invite</flux:button>
            </form>
            @error('email')
                <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
            @enderror
        </flux:card>
    @endif

    {{-- Seat Management --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Seats</flux:heading>
                <flux:text class="mt-1">
                    {{ $team->occupiedSeatCount() }} of {{ $team->totalSeatCapacity() }} seats used
                    @if($team->extra_seats > 0)
                        <span class="text-indigo-600 dark:text-indigo-400">({{ $team->extra_seats }} extra)</span>
                    @endif
                </flux:text>
            </div>
            @if(!$team->is_suspended)
                <div class="flex items-center gap-2">
                    <flux:modal.trigger name="add-seats">
                        <flux:button variant="primary" size="sm" icon="plus">Add Seats</flux:button>
                    </flux:modal.trigger>
                    @if($removableSeats > 0)
                        <flux:modal.trigger name="remove-seats">
                            <flux:button variant="ghost" size="sm" icon="minus">Remove Seats</flux:button>
                        </flux:modal.trigger>
                    @endif
                </div>
            @endif
        </div>
    </flux:card>

    {{-- Add Seats Modal --}}
    <flux:modal name="add-seats" class="max-w-md" x-init="{{ session('show_add_seats') ? '$flux.modal(\'add-seats\').show()' : '' }}">
        <div x-data="{
            addQty: 1,
            unitPrice: {{ $extraSeatPrice }},
            proRataFraction: {{ $proRataFraction }},
            get totalPrice() { return (this.addQty * this.unitPrice).toFixed(2) },
            get proRataAmount() { return (this.addQty * this.unitPrice * this.proRataFraction).toFixed(2) },
        }">
            <flux:heading size="lg">Add Extra Seats</flux:heading>
            <flux:text class="mt-2">
                Extra seats cost ${{ $extraSeatPrice }}/{{ $extraSeatInterval }} per seat.
            </flux:text>
            <div class="mt-4">
                <flux:input type="number" label="Quantity" x-model.number="addQty" min="1" max="50" class="w-24" />
            </div>
            <div class="mt-4 rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800">
                <div class="flex items-center justify-between text-sm">
                    <span><span x-text="addQty"></span> &times; ${{ $extraSeatPrice }}/{{ $extraSeatInterval }}</span>
                    <span class="font-medium">${{ $extraSeatPrice }}<span x-show="addQty > 1" x-text="' &rarr; $' + totalPrice"></span>/{{ $extraSeatInterval }}</span>
                </div>
                @if($proRataFraction < 1)
                    <div class="mt-2 border-t border-zinc-200 pt-2 dark:border-zinc-700">
                        <div class="flex items-center justify-between text-sm">
                            <span>Charged today (pro-rated)</span>
                            <span class="font-semibold">$<span x-text="proRataAmount"></span></span>
                        </div>
                        @if($renewalDate)
                            <flux:text class="mt-1 text-xs">
                                Full price applies from {{ $renewalDate }}.
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button
                    variant="primary"
                    x-on:click="$wire.addSeats(addQty)"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="addSeats">Confirm</span>
                    <span wire:loading wire:target="addSeats">Processing...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Remove Seats Modal --}}
    @if($removableSeats > 0)
        <flux:modal name="remove-seats" class="max-w-md">
            <div x-data="{ removeQty: 1 }">
                <flux:heading size="lg">Remove Extra Seats</flux:heading>
                <flux:text class="mt-2">
                    You currently have {{ $team->extra_seats }} extra seat(s). Seats are removed immediately and you'll be credited for the unused time on your next bill.
                </flux:text>
                <div class="mt-4">
                    <flux:input type="number" label="Quantity" x-model.number="removeQty" min="1" max="{{ $removableSeats }}" class="w-24" />
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button
                        variant="danger"
                        x-on:click="$wire.removeSeats(removeQty)"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="removeSeats">Remove</span>
                        <span wire:loading wire:target="removeSeats">Processing...</span>
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    {{-- Pending Invitations --}}
    @if($pendingInvitations->isNotEmpty())
        <div class="mb-6">
            <flux:heading size="lg" class="mb-3">
                Pending Invitations
                <flux:badge size="sm" class="ml-2">{{ $pendingInvitations->count() }}</flux:badge>
            </flux:heading>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Invited</flux:table.column>
                    <flux:table.column class="text-right">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($pendingInvitations as $invitation)
                        <flux:table.row wire:key="invitation-{{ $invitation->id }}">
                            <flux:table.cell>{{ $invitation->email }}</flux:table.cell>
                            <flux:table.cell>
                                @if($invitation->invited_at)
                                    {{ $invitation->invited_at->diffForHumans() }}
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('customer.team.users.resend', $invitation) }}">
                                        @csrf
                                        <flux:button type="submit" variant="ghost" size="sm">Resend</flux:button>
                                    </form>
                                    <form method="POST" action="{{ route('customer.team.users.remove', $invitation) }}" onsubmit="return confirm('Cancel this invitation?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="ghost" size="sm" class="text-red-600 hover:text-red-500 dark:text-red-400">Cancel</flux:button>
                                    </form>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    {{-- Team Members --}}
    <flux:heading size="lg" class="mb-3">Team Members</flux:heading>

    @if($activeMembers->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Member</flux:table.column>
                <flux:table.column>Joined</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($activeMembers as $member)
                    <flux:table.row wire:key="member-{{ $member->id }}">
                        <flux:table.cell>
                            <div>
                                <span class="font-medium">{{ $member->user?->display_name ?? $member->email }}</span>
                                <flux:text class="text-xs">{{ $member->email }}</flux:text>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($member->accepted_at)
                                {{ $member->accepted_at->diffForHumans() }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end">
                                <form method="POST" action="{{ route('customer.team.users.remove', $member) }}" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="ghost" size="sm" class="text-red-600 hover:text-red-500 dark:text-red-400">Remove</flux:button>
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <x-customer.empty-state
            icon="user-group"
            title="No active team members yet"
            description="Invite someone to get started."
        />
    @endif
</div>
