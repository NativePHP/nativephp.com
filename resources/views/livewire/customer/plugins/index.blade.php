<div>
    <div class="mb-6">
        <flux:heading size="xl">Plugins</flux:heading>
        <flux:text>Extend NativePHP Mobile with powerful native features</flux:text>
    </div>

    {{-- Action Cards --}}
    <div class="grid gap-6 md:grid-cols-3">
        {{-- Submit Plugin Card --}}
        <flux:card class="relative overflow-hidden border-2 border-indigo-500 dark:border-indigo-400">
            <div class="absolute -right-4 -top-4 size-24 rounded-full bg-indigo-500/10 dark:bg-indigo-400/10"></div>
            <div class="relative">
                <div class="flex size-12 items-center justify-center rounded-lg bg-indigo-500 text-white dark:bg-indigo-600">
                    <x-heroicon-o-plus class="size-6" />
                </div>
                <flux:heading size="lg" class="mt-4">Submit Your Plugin</flux:heading>
                <flux:text class="mt-2">
                    Built a plugin? Submit it to the NativePHP Plugin Marketplace and share it with the community.
                </flux:text>
                <flux:button variant="primary" href="{{ route('customer.plugins.create') }}" class="mt-4">
                    Submit a Plugin
                </flux:button>
            </div>
        </flux:card>

        {{-- Browse Plugins Card --}}
        <flux:card>
            <div class="flex size-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                <x-vaadin-plug class="size-6" />
            </div>
            <flux:heading size="lg" class="mt-4">Browse Plugins</flux:heading>
            <flux:text class="mt-2">
                Discover plugins built by the community to add native features to your mobile apps.
            </flux:text>
            <flux:button variant="ghost" href="{{ route('plugins') }}" class="mt-4">View Directory</flux:button>
        </flux:card>

        {{-- Learn to Build Card --}}
        <flux:card>
            <div class="flex size-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                <x-heroicon-o-book-open class="size-6" />
            </div>
            <flux:heading size="lg" class="mt-4">Learn to Build Plugins</flux:heading>
            <flux:text class="mt-2">
                Read the documentation to learn how to create your own NativePHP Mobile plugins.
            </flux:text>
            <flux:button variant="ghost" href="/docs/mobile/2/plugins" class="mt-4">Read the Docs</flux:button>
        </flux:card>
    </div>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mt-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mt-6">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Submitted Plugins List --}}
    <div class="mt-8">
        <flux:heading size="lg">Your Submitted Plugins</flux:heading>
        <flux:text class="mt-1">Track the status of your plugin submissions.</flux:text>

        <flux:radio.group wire:model.live="status" variant="segmented" class="mt-4">
            <flux:radio value="pending" label="Pending ({{ $this->pluginCounts['pending'] }})" />
            <flux:radio value="rejected" label="Rejected ({{ $this->pluginCounts['rejected'] }})" />
            <flux:radio value="approved" label="Approved ({{ $this->pluginCounts['approved'] }})" />
        </flux:radio.group>

        @if ($this->plugins->count() > 0)
            <flux:table class="mt-4">
                <flux:table.columns>
                    <flux:table.column>Plugin</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->plugins as $plugin)
                        <flux:table.row :key="$plugin->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0">
                                        @if ($plugin->isPending())
                                            <div class="size-3 animate-pulse rounded-full bg-yellow-400"></div>
                                        @elseif ($plugin->isApproved())
                                            <div class="size-3 rounded-full bg-green-400"></div>
                                        @else
                                            <div class="size-3 rounded-full bg-red-400"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-mono text-sm font-medium">{{ $plugin->name }}</span>
                                        <flux:text class="text-xs">
                                            {{ $plugin->type->label() }} plugin &bull; Submitted {{ $plugin->created_at->diffForHumans() }}
                                        </flux:text>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-customer.status-badge :status="$plugin->isPending() ? 'Pending Review' : ($plugin->isApproved() ? 'Approved' : 'Rejected')" />
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                <flux:button size="xs" variant="ghost" href="{{ route('customer.plugins.show', $plugin->routeParams()) }}">
                                    Edit
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>

                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="mt-4">
                <x-customer.empty-state
                    icon="puzzle-piece"
                    title="No {{ $status }} plugins"
                    description="No plugins match this filter."
                />
            </div>
        @endif
    </div>
</div>
