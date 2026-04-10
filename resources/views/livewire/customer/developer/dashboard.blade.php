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

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($this->plugins->take(5) as $plugin)
                    <a href="{{ route('customer.plugins.show', $plugin->routeParams()) }}" class="block py-4 first:pt-0 last:pb-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-2 px-2 rounded-lg" wire:key="plugin-{{ $plugin->id }}">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-900 dark:text-white">{{ $plugin->display_name ?? $plugin->name }}</p>
                                @if ($plugin->display_name)
                                    <p class="truncate font-mono text-xs text-gray-500 dark:text-gray-400">{{ $plugin->name }}</p>
                                @endif
                            </div>
                            <div class="ml-4 text-right">
                                <flux:text class="font-medium">{{ $plugin->licenses_count }} sales</flux:text>
                            </div>
                        </div>
                    </a>
                @empty
                    <x-customer.empty-state
                        icon="puzzle-piece"
                        title="No premium plugins yet"
                        description="Submit a paid plugin to start selling."
                    >
                        <flux:button variant="primary" size="sm" href="{{ route('customer.plugins.create') }}">Submit a plugin</flux:button>
                    </x-customer.empty-state>
                @endforelse
            </div>
        </flux:card>

        {{-- Recent Payouts --}}
        <flux:card>
            <flux:heading size="lg">Recent Payouts</flux:heading>
            <flux:separator class="my-4" />

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($this->payouts as $payout)
                    <div class="py-4 first:pt-0 last:pb-0" wire:key="payout-{{ $payout->id }}">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-900 dark:text-white">
                                    {{ $payout->pluginLicense->plugin->name ?? 'Unknown Plugin' }}
                                </p>
                                <flux:text class="text-sm">{{ $payout->created_at->format('M j, Y') }}</flux:text>
                            </div>
                            <div class="ml-4 text-right">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($payout->developer_amount / 100, 2) }}
                                </p>
                                @if ($payout->status === \App\Enums\PayoutStatus::Transferred)
                                    <flux:badge color="green" size="sm">Paid</flux:badge>
                                @elseif ($payout->status === \App\Enums\PayoutStatus::Pending)
                                    <flux:badge color="yellow" size="sm">Pending</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">Failed</flux:badge>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <x-customer.empty-state
                        icon="banknotes"
                        title="No payouts yet"
                        description="Payouts will appear here after you make your first sale."
                    />
                @endforelse
            </div>
        </flux:card>
    </div>
</div>
