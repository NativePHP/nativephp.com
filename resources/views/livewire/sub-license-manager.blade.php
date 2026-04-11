<div @if($isPolling) wire:poll.5s @endif>
    <flux:card class="mt-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">
                    Keys
                    <span class="ml-2 text-sm text-zinc-500 dark:text-zinc-400">
                        ({{ $activeSubLicenses->count() }}{{ $license->subLicenseLimit ? '/' . $license->subLicenseLimit : '' }})
                    </span>
                </flux:heading>
                <flux:text>Manage license keys for team members or additional devices.</flux:text>
            </div>
            @if($license->canCreateSubLicense())
                <flux:button variant="primary" wire:click="openCreateModal">
                    Create Key
                </flux:button>
            @endif
        </div>

        @if($license->subLicenses->isEmpty())
            <div class="py-8 text-center">
                <flux:heading size="sm">No keys</flux:heading>
                <flux:text>Get started by creating your first key.</flux:text>
            </div>
        @else
            {{-- Active Sub-Licenses --}}
            @if($activeSubLicenses->isNotEmpty())
                <flux:table class="mt-4">
                    <flux:table.columns>
                        <flux:table.column>Key</flux:table.column>
                        <flux:table.column>Assigned To</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($activeSubLicenses as $subLicense)
                            <flux:table.row wire:key="sublicense-{{ $subLicense->id }}">
                                <flux:table.cell>
                                    <div>
                                        @if($subLicense->name)
                                            <span class="font-medium">{{ $subLicense->name }}</span>
                                        @endif
                                        <x-customer.masked-key :key-value="$subLicense->key" />
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if($subLicense->assigned_email)
                                        <flux:text class="text-sm">{{ $subLicense->assigned_email }}</flux:text>
                                    @else
                                        <flux:text class="text-sm italic">Unassigned</flux:text>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <flux:button size="xs" variant="ghost" wire:click="editSubLicense({{ $subLicense->id }})">
                                            Edit
                                        </flux:button>
                                        @if($subLicense->assigned_email)
                                            <form method="POST" action="{{ route('customer.licenses.sub-licenses.send-email', [$license->key, $subLicense]) }}" class="inline">
                                                @csrf
                                                <flux:button type="submit" size="xs" variant="ghost">Send License</flux:button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('customer.licenses.sub-licenses.suspend', [$license->key, $subLicense]) }}" class="inline" onsubmit="return confirmSuspension(event)">
                                            @csrf
                                            @method('PATCH')
                                            <flux:button type="submit" size="xs" variant="ghost" class="!text-amber-600 hover:!text-amber-500">Suspend</flux:button>
                                        </form>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif

            {{-- Suspended Sub-Licenses --}}
            @if($suspendedSubLicenses->isNotEmpty())
                <div class="mt-6">
                    <flux:heading size="base">
                        Suspended Keys
                        <span class="ml-2 text-sm text-zinc-500 dark:text-zinc-400">
                            ({{ $suspendedSubLicenses->count() }})
                        </span>
                    </flux:heading>
                    <flux:text>These keys are permanently suspended and cannot be used or reactivated.</flux:text>

                    <flux:table class="mt-4">
                        <flux:table.columns>
                            <flux:table.column>Key</flux:table.column>
                            <flux:table.column>Assigned To</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach($suspendedSubLicenses as $subLicense)
                                <flux:table.row wire:key="suspended-sublicense-{{ $subLicense->id }}">
                                    <flux:table.cell>
                                        <div>
                                            @if($subLicense->name)
                                                <span class="font-medium">{{ $subLicense->name }}</span>
                                            @endif
                                            <x-customer.masked-key :key-value="$subLicense->key" />
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        @if($subLicense->assigned_email)
                                            <flux:text class="text-sm">{{ $subLicense->assigned_email }}</flux:text>
                                        @else
                                            <flux:text class="text-sm italic">Unassigned</flux:text>
                                        @endif
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            @endif
        @endif

        @if(!$license->canCreateSubLicense())
            <flux:callout variant="warning" icon="exclamation-triangle" class="mt-4">
                <flux:callout.text>
                    @if($license->remainingSubLicenses === 0)
                        You have reached the maximum number of keys for this plan.
                    @elseif($license->is_suspended)
                        Keys cannot be created for suspended licenses.
                    @elseif($license->expires_at && $license->expires_at->isPast())
                        Keys cannot be created for expired licenses.
                    @else
                        Keys cannot be created at this time.
                    @endif
                </flux:callout.text>
            </flux:callout>
        @endif
    </flux:card>

    {{-- Create Sub-License Modal --}}
    <flux:modal name="create-sub-license">
        <form wire:submit="createSubLicense">
            <flux:heading size="lg">Create Key</flux:heading>

            <div class="mt-4 space-y-4">
                <div>
                    <flux:input
                        wire:model="createName"
                        label="Name (Optional)"
                        placeholder="e.g., Development Team, John's Machine"
                    />
                    <flux:text class="mt-1 text-xs">Give your key a descriptive name to help identify its purpose.</flux:text>
                </div>

                <div>
                    <flux:input
                        wire:model="createAssignedEmail"
                        type="email"
                        label="Assign to Email (Optional)"
                        placeholder="e.g., john@company.com"
                    />
                    <flux:text class="mt-1 text-xs">Assign this license to a team member.</flux:text>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button type="submit" variant="primary">Create Key</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit Sub-License Modal --}}
    <flux:modal name="edit-sub-license">
        <form wire:submit="updateSubLicense">
            <flux:heading size="lg">Edit Key</flux:heading>

            <div class="mt-4 space-y-4">
                <div>
                    <flux:input
                        wire:model="editName"
                        label="Name (Optional)"
                        placeholder="e.g., Development Team, John's Machine"
                    />
                    <flux:text class="mt-1 text-xs">Give your key a descriptive name to help identify its purpose.</flux:text>
                </div>

                <div>
                    <flux:input
                        wire:model="editAssignedEmail"
                        type="email"
                        label="Assign to Email (Optional)"
                        placeholder="e.g., john@company.com"
                    />
                    <flux:text class="mt-1 text-xs">Assign this license to a team member.</flux:text>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button type="submit" variant="primary">Update Key</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
