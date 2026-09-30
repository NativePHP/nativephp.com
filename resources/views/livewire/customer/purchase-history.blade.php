<div>
    <div class="mb-6">
        <flux:heading size="xl">Purchase History</flux:heading>
        <flux:text>All your NativePHP purchases in one place. Bifrost subscriptions are managed separately and won't appear here.</flux:text>
    </div>

    @if($this->purchases->count() > 0)
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Purchase</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column class="hidden sm:table-cell">Date</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->purchases as $purchase)
                    @php
                        $dateNote = match (true) {
                            $purchase['refunded_at'] !== null => 'Refunded '.$purchase['refunded_at']->format('M j, Y'),
                            $purchase['expires_at']?->isPast() === true => 'Expired '.$purchase['expires_at']->format('M j, Y'),
                            $purchase['expires_at'] !== null => 'Expires '.$purchase['expires_at']->format('M j, Y'),
                            default => null,
                        };
                    @endphp
                    <flux:table.row :key="$loop->index">
                        <flux:table.cell class="whitespace-normal">
                            <div class="max-w-xs min-w-0">
                                @if($purchase['href'])
                                    <a href="{{ $purchase['href'] }}" class="font-medium text-blue-600 wrap-anywhere hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $purchase['name'] }}
                                    </a>
                                @else
                                    <span class="font-medium">{{ $purchase['name'] }}</span>
                                @endif
                                @if($purchase['description'])
                                    <flux:text class="truncate text-xs">{{ $purchase['description'] }}</flux:text>
                                @endif
                                <flux:text class="mt-1 text-xs sm:hidden">
                                    {{ $purchase['purchased_at']->format('M j, Y') }}
                                    @if($dateNote)
                                        &middot; {{ $dateNote }}
                                    @endif
                                </flux:text>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($purchase['refunded_at'])
                                <div class="flex flex-col items-start gap-1 sm:flex-row sm:items-center sm:gap-2">
                                    <span class="text-gray-500 line-through dark:text-gray-400">${{ number_format($purchase['price'] / 100, 2) }}</span>
                                    <flux:badge color="zinc" size="sm">Refunded</flux:badge>
                                </div>
                            @elseif($purchase['price'] !== null && $purchase['price'] > 0)
                                ${{ number_format($purchase['price'] / 100, 2) }}
                            @elseif($purchase['price'] === 0 || (isset($purchase['is_grandfathered']) && $purchase['is_grandfathered']))
                                <span class="text-green-600 dark:text-green-400">Free</span>
                            @else
                                &mdash;
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="hidden sm:table-cell">
                            <div>
                                {{ $purchase['purchased_at']->format('M j, Y') }}
                                @if($dateNote)
                                    <flux:text class="text-xs">{{ $dateNote }}</flux:text>
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
