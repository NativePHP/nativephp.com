<div>
    <div class="mb-6">
        <flux:heading size="xl">Purchase History</flux:heading>
        <flux:text>All your NativePHP purchases in one place</flux:text>
    </div>

    @if($this->purchases->count() > 0)
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Purchase</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->purchases as $purchase)
                    <flux:table.row :key="$loop->index">
                        <flux:table.cell>
                            <div>
                                @if($purchase['href'])
                                    <a href="{{ $purchase['href'] }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $purchase['name'] }}
                                    </a>
                                @else
                                    <span class="font-medium">{{ $purchase['name'] }}</span>
                                @endif
                                @if($purchase['description'])
                                    <flux:text class="text-xs">{{ $purchase['description'] }}</flux:text>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                                $typeColor = match($purchase['type']) {
                                    'subscription' => 'blue',
                                    'product' => 'amber',
                                    'plugin' => 'purple',
                                    default => 'zinc',
                                };
                                $typeLabel = ucfirst($purchase['type'] === 'subscription' ? 'license' : $purchase['type']);
                            @endphp
                            <flux:badge color="{{ $typeColor }}">{{ $typeLabel }}</flux:badge>
                            @if(isset($purchase['is_grandfathered']) && $purchase['is_grandfathered'])
                                <flux:badge color="yellow" class="ml-1">Free</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($purchase['price'] !== null && $purchase['price'] > 0)
                                ${{ number_format($purchase['price'] / 100, 2) }}
                            @elseif($purchase['price'] === 0 || (isset($purchase['is_grandfathered']) && $purchase['is_grandfathered']))
                                <span class="text-green-600 dark:text-green-400">Free</span>
                            @else
                                &mdash;
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <x-customer.status-badge :status="$purchase['is_active'] ? 'Active' : 'Expired'" />
                        </flux:table.cell>

                        <flux:table.cell>
                            <div>
                                {{ $purchase['purchased_at']->format('M j, Y') }}
                                @if($purchase['expires_at'])
                                    <flux:text class="text-xs">
                                        @if($purchase['expires_at']->isPast())
                                            Expired {{ $purchase['expires_at']->format('M j, Y') }}
                                        @else
                                            Expires {{ $purchase['expires_at']->format('M j, Y') }}
                                        @endif
                                    </flux:text>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <x-customer.empty-state
            icon="receipt-refund"
            title="No purchases yet"
            description="Your purchase history will appear here once you make your first purchase."
        />
    @endif
</div>
