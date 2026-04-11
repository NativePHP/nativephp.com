<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Edit Your Submission</flux:heading>
            <flux:text class="mt-2">Update the details of your showcase submission.</flux:text>
        </div>
        <x-customer.status-badge :status="$showcase->isApproved() ? 'Approved' : 'Pending Review'" />
    </div>

    <flux:card>
        <livewire:showcase-submission-form :showcase="$showcase" />
    </flux:card>
</div>
