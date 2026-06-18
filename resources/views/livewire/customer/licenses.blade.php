<div>
    <div class="mb-6">
        <flux:heading size="xl">Your Licenses</flux:heading>
        <flux:text>Manage your NativePHP licenses</flux:text>
    </div>

    @if($this->licenses->count() > 0)
        <flux:table>
            <flux:table.columns>
                <flux:table.column>License</flux:table.column>
                <flux:table.column>Key</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->licenses as $license)
                    <flux:table.row :key="$license->id">
                        <flux:table.cell>
                            <div>
                                <a href="{{ route('customer.licenses.show', $license->key) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $license->name ?: $license->policy_name }}
                                </a>
                                @if($license->name)
                                    <flux:text class="text-xs">{{ $license->policy_name }}</flux:text>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <x-customer.masked-key :key-value="$license->key" />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif

    {{-- Assigned Sub-Licenses --}}
    @if($this->assignedSubLicenses->count() > 0)
        <div class="mt-8">
            <flux:heading size="lg" class="mb-4">Assigned Sub-Licenses</flux:heading>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>License</flux:table.column>
                    <flux:table.column>Key</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($this->assignedSubLicenses as $subLicense)
                        <flux:table.row :key="$subLicense->id">
                            <flux:table.cell>
                                <div>
                                    <span class="font-medium">{{ $subLicense->parentLicense->policy_name ?? 'Sub-License' }}</span>
                                    <flux:text class="text-xs">Sub-license</flux:text>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-customer.masked-key :key-value="$subLicense->key" />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    @if($this->licenses->count() === 0 && $this->assignedSubLicenses->count() === 0)
        <x-customer.empty-state
            icon="key"
            title="No licenses found"
            description="You don't have any licenses yet. If you believe this is an error, please contact support."
        />
    @endif
</div>
