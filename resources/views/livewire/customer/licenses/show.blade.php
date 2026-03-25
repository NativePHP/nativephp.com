<div>
    <div class="mb-6">
        <flux:heading size="xl">{{ $license->name ?: $license->policy_name }}</flux:heading>
        @if($license->name)
            <flux:text>{{ $license->policy_name }}</flux:text>
        @endif
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
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

        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
            {{-- License Key --}}
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">License Key</dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    <div class="flex items-center justify-between">
                        <code class="rounded bg-gray-100 px-2 py-1 font-mono text-sm dark:bg-gray-700">{{ $license->key }}</code>
                        <flux:button size="xs" variant="ghost" x-on:click="navigator.clipboard.writeText('{{ $license->key }}')">
                            Copy
                        </flux:button>
                    </div>
                </dd>
            </div>

            {{-- License Name --}}
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">License Name</dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    <div class="flex items-center justify-between">
                        <span class="{{ $license->name ? '' : 'italic text-gray-500 dark:text-gray-400' }}">
                            {{ $license->name ?: 'No name set' }}
                        </span>
                        <flux:button size="xs" variant="ghost" wire:click="$set('showEditNameModal', true)">
                            Edit
                        </flux:button>
                    </div>
                </dd>
            </div>

            {{-- License Type --}}
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">License Type</dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">{{ $license->policy_name }}</dd>
            </div>

            {{-- Created --}}
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    {{ $license->created_at->format('F j, Y \a\t g:i A') }}
                    <span class="ml-1 text-gray-500 dark:text-gray-400">
                        ({{ $license->created_at->diffForHumans() }})
                    </span>
                </dd>
            </div>

            {{-- Expires --}}
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expires</dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    @if($license->expires_at)
                        {{ $license->expires_at->format('F j, Y \a\t g:i A') }}
                        <span class="ml-1 text-gray-500 dark:text-gray-400">
                            @if($license->expires_at->isPast())
                                (Expired {{ $license->expires_at->diffForHumans() }})
                            @else
                                ({{ $license->expires_at->diffForHumans() }})
                            @endif
                        </span>
                    @else
                        Never
                    @endif
                </dd>
            </div>
        </dl>
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
                Set up automatic renewal now to avoid interruption and lock in your Early Access Pricing!
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

            <div class="mt-6 flex justify-end gap-3">
                <flux:button variant="ghost" x-on:click="$flux.close()">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update Name</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
