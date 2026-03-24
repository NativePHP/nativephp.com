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
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->licenses as $license)
                    @php
                        $isLegacyLicense = $license->isLegacy();
                        $daysUntilExpiry = $license->expires_at ? (int) round(abs(now()->diffInDays($license->expires_at))) : null;
                        $needsRenewal = $isLegacyLicense && $daysUntilExpiry !== null && !$license->expires_at->isPast();

                        $status = match(true) {
                            $license->is_suspended => 'Suspended',
                            $license->expires_at && $license->expires_at->isPast() => 'Expired',
                            $needsRenewal => 'Needs Renewal',
                            default => 'Active',
                        };
                    @endphp
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

                        <flux:table.cell>
                            <x-customer.status-badge :status="$status" />
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($needsRenewal)
                                <div>
                                    <span class="font-medium text-blue-600 dark:text-blue-400">
                                        {{ $daysUntilExpiry }} day{{ $daysUntilExpiry === 1 ? '' : 's' }}
                                    </span>
                                    @if($isLegacyLicense)
                                        <flux:text class="text-xs text-blue-500 dark:text-blue-300">Lock in Early Access Pricing</flux:text>
                                    @endif
                                </div>
                            @elseif($license->expires_at)
                                <div>
                                    {{ $license->expires_at->format('M j, Y') }}
                                    @if($license->expires_at->isPast())
                                        <flux:text class="text-xs">Expired {{ $license->expires_at->diffForHumans() }}</flux:text>
                                    @endif
                                </div>
                            @else
                                No expiration
                            @endif
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
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Expires</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($this->assignedSubLicenses as $subLicense)
                        @php
                            $subStatus = match(true) {
                                $subLicense->is_suspended => 'Suspended',
                                $subLicense->expires_at && $subLicense->expires_at->isPast() => 'Expired',
                                default => 'Active',
                            };
                        @endphp
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

                            <flux:table.cell>
                                <x-customer.status-badge :status="$subStatus" />
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($subLicense->expires_at)
                                    <div>
                                        {{ $subLicense->expires_at->format('M j, Y') }}
                                        @if($subLicense->expires_at->isPast())
                                            <flux:text class="text-xs">Expired {{ $subLicense->expires_at->diffForHumans() }}</flux:text>
                                        @endif
                                    </div>
                                @else
                                    No expiration
                                @endif
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
