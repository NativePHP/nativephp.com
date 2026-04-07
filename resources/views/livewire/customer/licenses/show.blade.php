<div>
    <div class="mb-6">
        <a href="{{ route('customer.licenses.list') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
            <x-heroicon-s-arrow-left class="size-4" />
            <span class="font-medium">Licenses</span>
        </a>
        <flux:heading size="xl" class="mt-4">{{ $license->name ?: $license->policy_name }}</flux:heading>
        @if($license->name)
            <flux:text>{{ $license->policy_name }}</flux:text>
        @endif
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(session('error'))
        <flux:callout variant="danger" icon="exclamation-circle" class="mb-6">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- License Information Card --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <flux:heading size="lg">License Information</flux:heading>
                <flux:text>Details about your NativePHP license.</flux:text>
            </div>
            <x-customer.status-badge :status="$license->is_suspended ? 'Suspended' : ($license->expires_at && $license->expires_at->isPast() ? 'Expired' : 'Active')" />
        </div>

        <flux:separator />

        <flux:table>
            <flux:table.rows>
                {{-- License Key --}}
                <flux:table.row>
                    <flux:table.cell class="font-medium text-zinc-500 dark:text-zinc-400">License Key</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center justify-between">
                            <x-customer.masked-key :key-value="$license->key" />
                            @if(! $license->is_suspended && ! ($license->expires_at && $license->expires_at->isPast()))
                                <flux:modal.trigger name="rotate-license-key">
                                    <flux:button size="sm" icon="arrow-path" tooltip="Rotate key" />
                                </flux:modal.trigger>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>

                {{-- License Name --}}
                <flux:table.row>
                    <flux:table.cell class="font-medium text-zinc-500 dark:text-zinc-400">License Name</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center justify-between">
                            <span class="{{ $license->name ? '' : 'italic text-zinc-500 dark:text-zinc-400' }}">
                                {{ $license->name ?: 'No name set' }}
                            </span>
                            <flux:button size="xs" variant="ghost" wire:click="$set('showEditNameModal', true)">
                                Edit
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>

                {{-- License Type --}}
                <flux:table.row>
                    <flux:table.cell class="font-medium text-zinc-500 dark:text-zinc-400">License Type</flux:table.cell>
                    <flux:table.cell>{{ $license->policy_name }}</flux:table.cell>
                </flux:table.row>

                {{-- Created --}}
                <flux:table.row>
                    <flux:table.cell class="font-medium text-zinc-500 dark:text-zinc-400">Created</flux:table.cell>
                    <flux:table.cell>
                        {{ $license->created_at->format('F j, Y \a\t g:i A') }}
                        <flux:text class="inline text-xs">({{ $license->created_at->diffForHumans() }})</flux:text>
                    </flux:table.cell>
                </flux:table.row>

                {{-- Expires --}}
                <flux:table.row>
                    <flux:table.cell class="font-medium text-zinc-500 dark:text-zinc-400">Expires</flux:table.cell>
                    <flux:table.cell>
                        @if($license->expires_at)
                            {{ $license->expires_at->format('F j, Y \a\t g:i A') }}
                            @if($license->expires_at->isPast())
                                <flux:text class="inline text-xs">(Expired {{ $license->expires_at->diffForHumans() }})</flux:text>
                            @else
                                <flux:text class="inline text-xs">({{ $license->expires_at->diffForHumans() }})</flux:text>
                            @endif
                        @else
                            Never
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Sub-license Manager --}}
    @if($license->supportsSubLicenses())
        @livewire('sub-license-manager', ['license' => $license])
    @endif

    {{-- Renewal CTA --}}
    @php
        $isLegacyLicense = $license->isLegacy();
        $daysUntilExpiry = $license->expires_at ? (int) now()->diffInDays($license->expires_at, false) : null;
        $needsRenewal = $isLegacyLicense && $daysUntilExpiry !== null;
    @endphp

    @if($needsRenewal && !$license->expires_at->isPast())
        <flux:callout variant="info" icon="information-circle" class="mt-6">
            <flux:callout.heading>Renewal Available with Early Access Pricing</flux:callout.heading>
            <flux:callout.text>
                Your license expires in {{ $daysUntilExpiry }} day{{ $daysUntilExpiry === 1 ? '' : 's' }}.
                Set up automatic renewal now to upgrade to Ultra with your Early Access Pricing!
            </flux:callout.text>
            <x-slot name="actions">
                <flux:button variant="primary" href="{{ route('license.renewal', $license->key) }}">Set Up Renewal</flux:button>
            </x-slot>
        </flux:callout>
    @endif

    @if($license->is_suspended || ($license->expires_at && $license->expires_at->isPast()))
        <flux:callout variant="warning" icon="exclamation-triangle" class="mt-6">
            <flux:callout.heading>
                {{ $license->is_suspended ? 'License Suspended' : 'License Expired' }}
            </flux:callout.heading>
            <flux:callout.text>
                @if($license->is_suspended)
                    This license has been suspended. Please contact support for assistance.
                @elseif($isLegacyLicense)
                    This license has expired. You can still renew it to restore access.
                    <a href="{{ route('license.renewal', $license->key) }}" class="font-medium underline hover:no-underline">Renew now</a>
                @else
                    This license has expired. Please renew your subscription to continue using NativePHP.
                @endif
            </flux:callout.text>
        </flux:callout>
    @endif

    {{-- Edit License Name Modal --}}
    <flux:modal wire:model="showEditNameModal">
        <form wire:submit="updateLicenseName">
            <flux:heading size="lg">Edit License Name</flux:heading>

            <div class="mt-4">
                <flux:input
                    wire:model="licenseName"
                    label="License Name (Optional)"
                    placeholder="e.g., Main License, Production Environment"
                />
                <flux:text class="mt-1 text-xs">Give your license a descriptive name to help organize your licenses.</flux:text>
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button type="submit" variant="primary">Update Name</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Rotate License Key Confirmation Modal --}}
    <flux:modal name="rotate-license-key" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Rotate License Key</flux:heading>
                <flux:text class="mt-2">
                    Are you sure you want to rotate this license key? This action cannot be undone.
                </flux:text>
            </div>

            <flux:callout variant="warning" icon="exclamation-triangle">
                <flux:callout.heading>After rotating your key, you will need to:</flux:callout.heading>
                <flux:callout.text>
                    <ul class="mt-1 list-disc pl-5 text-sm">
                        <li>Update the license key in all your NativePHP applications</li>
                        <li>Update any CI/CD pipelines or deployment scripts</li>
                        <li>Notify any team members using this key</li>
                    </ul>
                </flux:callout.text>
            </flux:callout>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="rotateLicenseKey">Rotate Key</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
