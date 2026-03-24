<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Purchased Plugins</flux:heading>
            <flux:text>Your premium plugins and Composer configuration</flux:text>
        </div>
        <flux:button variant="primary" href="{{ route('plugins.marketplace') }}">Browse Plugins</flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <x-customer.plugin-credentials :pluginLicenseKey="$this->pluginLicenseKey" />

    @if($this->pluginLicenses->count() > 0)
        <flux:heading class="mb-3">Your Plugins</flux:heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Plugin</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Purchased</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->pluginLicenses as $pluginLicense)
                    <flux:table.row :key="$pluginLicense->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="shrink-0">
                                    @if($pluginLicense->plugin->hasLogo())
                                        <img src="{{ $pluginLicense->plugin->getLogoUrl() }}" alt="{{ $pluginLicense->plugin->name }}" class="size-10 rounded-lg object-cover">
                                    @elseif($pluginLicense->plugin->hasGradientIcon())
                                        <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br {{ $pluginLicense->plugin->getGradientClasses() }} text-white">
                                            <x-dynamic-component :component="'heroicon-o-' . $pluginLicense->plugin->icon_name" class="size-5" />
                                        </div>
                                    @else
                                        <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                            <x-vaadin-plug class="size-5" />
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('plugins.show', $pluginLicense->plugin->routeParams()) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $pluginLicense->plugin->name }}
                                    </a>
                                    @if($pluginLicense->plugin->description)
                                        <flux:text class="text-xs line-clamp-1">{{ $pluginLicense->plugin->description }}</flux:text>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div>
                                <x-customer.status-badge status="Licensed" />
                                @if($pluginLicense->wasPurchasedAsBundle() && $pluginLicense->pluginBundle)
                                    <flux:text class="text-xs mt-1">Part of {{ $pluginLicense->pluginBundle->name }}</flux:text>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $pluginLicense->purchased_at->format('M j, Y') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <x-customer.empty-state
            icon="puzzle-piece"
            title="No plugins yet"
            description="Browse the plugin marketplace to find premium plugins for your NativePHP app."
        >
            <flux:button variant="primary" href="{{ route('plugins.marketplace') }}">Browse Plugins</flux:button>
        </x-customer.empty-state>
    @endif

    {{-- Team Plugins --}}
    @if($this->teamPlugins->isNotEmpty())
        <flux:heading class="mb-3 mt-8">
            Team Plugins
            <span class="text-sm font-normal text-zinc-500 dark:text-white/70">— shared by {{ $this->teamOwnerName }}</span>
        </flux:heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Plugin</flux:table.column>
                <flux:table.column>Access</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->teamPlugins as $plugin)
                    <flux:table.row :key="'team-' . $plugin->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="shrink-0">
                                    @if($plugin->hasLogo())
                                        <img src="{{ $plugin->getLogoUrl() }}" alt="{{ $plugin->name }}" class="size-10 rounded-lg object-cover">
                                    @elseif($plugin->hasGradientIcon())
                                        <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white">
                                            <x-dynamic-component :component="'heroicon-o-' . $plugin->icon_name" class="size-5" />
                                        </div>
                                    @else
                                        <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                            <x-vaadin-plug class="size-5" />
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('plugins.show', $plugin->routeParams()) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $plugin->name }}
                                    </a>
                                    @if($plugin->description)
                                        <flux:text class="text-xs line-clamp-1">{{ $plugin->description }}</flux:text>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <x-customer.status-badge status="Team" />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
