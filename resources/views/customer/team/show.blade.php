<x-layouts.dashboard title="{{ $team->name }} - Team">
    <div>
        <div class="mb-6">
            <flux:heading size="xl">{{ $team->name }}</flux:heading>
            <flux:text>Your team membership benefits</flux:text>
        </div>

        <div class="max-w-3xl">
            {{-- Membership Info --}}
            <flux:card class="mb-6">
                <flux:heading size="lg">Team Membership</flux:heading>
                <flux:text class="mt-2">
                    You are a member of <strong>{{ $team->name }}</strong>, managed by {{ $team->owner->display_name }}.
                </flux:text>
                @if($membership->accepted_at)
                    <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Joined {{ $membership->accepted_at->diffForHumans() }}
                    </flux:text>
                @endif
            </flux:card>

            {{-- Benefits --}}
            <flux:card class="mb-6">
                <flux:heading size="lg">Your Benefits</flux:heading>
                <ul class="mt-3 list-inside list-disc space-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                    <li>Free access to all first-party NativePHP plugins</li>
                    <li>Access to the Plugin Dev Kit GitHub repository</li>
                    <li>Access to plugins purchased by your team owner</li>
                </ul>
            </flux:card>

            {{-- Official Plugins --}}
            @if($officialPlugins->isNotEmpty())
                <flux:heading class="mb-3">
                    First-Party Plugins
                    <span class="text-sm font-normal text-zinc-500 dark:text-white/70">— included with your team membership</span>
                </flux:heading>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Plugin</flux:table.column>
                        <flux:table.column>Access</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($officialPlugins as $plugin)
                            <flux:table.row :key="'official-' . $plugin->id">
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
                                    <flux:badge color="green">Included</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif

            {{-- Owner's Purchased Plugins --}}
            @if($ownerPlugins->isNotEmpty())
                <flux:heading class="mb-3 mt-8">
                    Shared Plugins
                    <span class="text-sm font-normal text-zinc-500 dark:text-white/70">— purchased by {{ $team->owner->display_name }}</span>
                </flux:heading>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Plugin</flux:table.column>
                        <flux:table.column>Access</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($ownerPlugins as $plugin)
                            <flux:table.row :key="'shared-' . $plugin->id">
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

            {{-- Empty state --}}
            @if($officialPlugins->isEmpty() && $ownerPlugins->isEmpty())
                <x-customer.empty-state
                    icon="puzzle-piece"
                    title="No plugins available yet"
                    description="There are no plugins available through this team at the moment."
                />
            @endif
        </div>
    </div>
</x-layouts.dashboard>
