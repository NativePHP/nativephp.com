<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Developer Dashboard</flux:heading>
            <flux:text>Manage your plugins and track your earnings</flux:text>
        </div>
        <flux:button variant="primary" href="{{ route('customer.plugins.create') }}" icon="plus">
            Submit Plugin
        </flux:button>
    </div>

    {{-- Session Messages --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('message'))
        <flux:callout variant="secondary" icon="information-circle" class="mb-6">
            <flux:callout.text>{{ session('message') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <flux:card class="!p-6">
            <flux:text class="text-sm">Total Earnings</flux:text>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                ${{ number_format($this->totalEarnings / 100, 2) }}
            </p>
        </flux:card>

        <flux:card class="!p-6">
            <flux:text class="text-sm">Pending Payouts</flux:text>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                ${{ number_format($this->pendingEarnings / 100, 2) }}
            </p>
        </flux:card>

        <flux:card class="!p-6">
            <flux:text class="text-sm">Published Plugins</flux:text>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $this->plugins->where('status', \App\Enums\PluginStatus::Approved)->count() }}
            </p>
        </flux:card>

        <flux:card class="!p-6">
            <flux:text class="text-sm">Total Sales</flux:text>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $this->plugins->sum('licenses_count') }}
            </p>
        </flux:card>
    </div>

    {{-- Two Column Layout --}}
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        {{-- Plugins --}}
        <flux:card>
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Your Premium Plugins</flux:heading>
                <flux:button variant="ghost" size="sm" href="{{ route('customer.plugins.index') }}">View all</flux:button>
            </div>
            <flux:separator class="my-4" />

            @if ($this->plugins->isNotEmpty())
                <flux:table>
                    <flux:table.rows>
                        @foreach ($this->plugins->take(5) as $plugin)
                            <flux:table.row :key="'plugin-'.$plugin->id">
                                <flux:table.cell class="whitespace-normal">
                                    <a href="{{ route('customer.plugins.show', $plugin->routeParams()) }}" class="text-sm font-medium text-blue-600 wrap-anywhere hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $plugin->display_name ?? $plugin->name }}
                                    </a>
                                    @if ($plugin->display_name)
                                        <flux:text class="font-mono text-xs wrap-anywhere">{{ $plugin->name }}</flux:text>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    {{ trans_choice(':count sale|:count sales', $plugin->licenses_count) }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <x-customer.empty-state
                    icon="puzzle-piece"
                    title="No premium plugins yet"
                    description="Submit a paid plugin to start selling."
                >
                    <flux:button variant="primary" size="sm" href="{{ route('customer.plugins.create') }}">Submit a plugin</flux:button>
                </x-customer.empty-state>
            @endif
        </flux:card>

        {{-- Recent Payouts --}}
        <flux:card>
            <flux:heading size="lg">Recent Payouts</flux:heading>
            <flux:separator class="my-4" />

            @if ($this->payouts->isNotEmpty())
                <flux:table>
                    <flux:table.rows>
                        @foreach ($this->payouts as $payout)
                            <flux:table.row :key="'payout-'.$payout->id">
                                <flux:table.cell class="whitespace-normal">
                                    <span class="text-sm font-medium text-zinc-800 wrap-anywhere dark:text-white">
                                        {{ $payout->pluginLicense->plugin->name ?? 'Unknown Plugin' }}
                                    </span>
                                    <flux:text class="text-xs">{{ $payout->created_at->format('M j, Y') }}</flux:text>
                                    @if ($expectedAt = $payout->expectedTransferDate())
                                        <flux:text class="text-xs">
                                            @if ($expectedAt->isFuture())
                                                Expected in your Stripe account around {{ $expectedAt->format('M j, Y') }}
                                            @else
                                                Expected in your Stripe account soon
                                            @endif
                                        </flux:text>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex flex-col items-end gap-1">
                                        @if ($payout->wasCancelledByRefund())
                                            <span class="line-through">${{ number_format($payout->developer_amount / 100, 2) }}</span>
                                        @else
                                            <span class="font-medium text-zinc-800 dark:text-white">${{ number_format($payout->developer_amount / 100, 2) }}</span>
                                        @endif

                                        @if ($payout->status === \App\Enums\PayoutStatus::Transferred)
                                            <flux:badge color="green" size="sm">Paid</flux:badge>
                                        @elseif ($payout->status === \App\Enums\PayoutStatus::Pending)
                                            <flux:badge color="yellow" size="sm">Pending</flux:badge>
                                        @elseif ($payout->wasCancelledByRefund())
                                            <flux:badge color="zinc" size="sm">Refunded</flux:badge>
                                        @else
                                            <flux:badge color="red" size="sm">Failed</flux:badge>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <x-customer.empty-state
                    icon="banknotes"
                    title="No payouts yet"
                    description="Payouts will appear here after you make your first sale."
                />
            @endif
        </flux:card>
    </div>
</div>
