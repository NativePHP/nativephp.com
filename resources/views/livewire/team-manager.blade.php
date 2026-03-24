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
        <div x-data="{ addQty: 1 }">
            <flux:heading size="lg">Add Extra Seats</flux:heading>
            <flux:text class="mt-2">
                Extra seats cost ${{ $extraSeatPriceMonthly }}/mo or ${{ $extraSeatPriceYearly }}/yr per seat, matching your current billing interval.
            </flux:text>
            <div class="mt-4">
                <flux:input type="number" label="Quantity" x-model.number="addQty" min="1" max="50" class="w-24" />
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
            <flux:heading size="lg" class="mb-4">
                Pending Invitations
                <flux:badge size="sm" class="ml-2">{{ $pendingInvitations->count() }}</flux:badge>
            </flux:heading>
            <flux:card class="!p-0">
                <flux:table>
                    <flux:table.rows>
                        @foreach($pendingInvitations as $invitation)
                            <flux:table.row wire:key="invitation-{{ $invitation->id }}">
                                <flux:table.cell class="flex-1">
                                    <flux:text class="font-medium">{{ $invitation->email }}</flux:text>
                                    @if($invitation->invited_at)
                                        <flux:text class="text-xs">Invited {{ $invitation->invited_at->diffForHumans() }}</flux:text>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="flex justify-end gap-3">
                                    <form method="POST" action="{{ route('customer.team.users.resend', $invitation) }}">
                                        @csrf
                                        <flux:button type="submit" variant="ghost" size="sm">Resend</flux:button>
                                    </form>
                                    <form method="POST" action="{{ route('customer.team.users.remove', $invitation) }}" onsubmit="return confirm('Cancel this invitation?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="ghost" size="sm" class="text-red-600 hover:text-red-500 dark:text-red-400">Cancel</flux:button>
                                    </form>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    @endif

    {{-- Team Members --}}
    <div class="mb-4">
        <flux:heading size="lg">Team Members</flux:heading>
    </div>

    @if($activeMembers->isNotEmpty())
        <flux:card class="!p-0">
            <flux:table>
                <flux:table.rows>
                    @foreach($activeMembers as $member)
                        <flux:table.row wire:key="member-{{ $member->id }}">
                            <flux:table.cell class="flex-1">
                                <flux:text class="font-medium">{{ $member->user?->display_name ?? $member->email }}</flux:text>
                                <flux:text class="text-sm">{{ $member->email }}</flux:text>
                                @if($member->accepted_at)
                                    <flux:text class="text-xs">Joined {{ $member->accepted_at->diffForHumans() }}</flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="flex justify-end">
                                <form method="POST" action="{{ route('customer.team.users.remove', $member) }}" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="ghost" size="sm" class="text-red-600 hover:text-red-500 dark:text-red-400">Remove</flux:button>
                                </form>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @else
        <flux:card>
            <flux:text class="text-center">No active team members yet. Invite someone to get started.</flux:text>
        </flux:card>
    @endif
</div>
