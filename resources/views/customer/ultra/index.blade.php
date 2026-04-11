<x-layouts.dashboard title="Ultra">
    <div>
        <div class="mb-6 flex items-center gap-3">
            <div class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white">
                <x-heroicon-s-bolt class="size-6" />
            </div>
            <div>
                <flux:heading size="xl">Ultra</flux:heading>
                <flux:text>Your premium subscription benefits</flux:text>
            </div>
        </div>

        <div class="max-w-3xl space-y-6">
            {{-- Subscription Status --}}
            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">Subscription</flux:heading>
                        <flux:text class="mt-1">
                            @if($subscription->stripe_price === config('subscriptions.plans.max.stripe_price_id_monthly'))
                                ${{ config('subscriptions.plans.max.price_monthly') }}/month
                            @elseif($subscription->stripe_price === config('subscriptions.plans.max.stripe_price_id_eap'))
                                ${{ config('subscriptions.plans.max.eap_price_yearly') }}/year (Early Access)
                            @else
                                ${{ config('subscriptions.plans.max.price_yearly') }}/year
                            @endif
                        </flux:text>
                    </div>
                    <flux:button variant="ghost" href="{{ route('customer.billing-portal') }}" icon-trailing="arrow-top-right-on-square">
                        Manage
                    </flux:button>
                </div>
            </flux:card>

            {{-- Benefits --}}
            <flux:card>
                <flux:heading size="lg">Your Benefits</flux:heading>
                <flux:text class="mt-1 mb-5">Everything included with your Ultra subscription.</flux:text>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">All first-party plugins</div>
                            <flux:text class="text-sm">Every NativePHP-published plugin is included at no extra cost. New plugins are added automatically.</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">Claude Code Plugin Dev Kit</div>
                            <flux:text class="text-sm">Tools and resources to build NativePHP plugins using Claude Code.</flux:text>
                            <flux:button variant="primary" size="xs" class="mt-1" href="{{ route('customer.integrations') }}" icon-trailing="arrow-right">Set up access via Integrations</flux:button>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">Teams</div>
                            <flux:text class="text-sm">
                                Invite up to {{ config('subscriptions.plans.max.included_seats') - 1 }} members to share your Ultra benefits ({{ config('subscriptions.plans.max.included_seats') }} seats included).
                                Extra seats available at ${{ config('subscriptions.plans.max.extra_seat_price_monthly') }}/mo or ${{ config('subscriptions.plans.max.extra_seat_price_yearly') }}/mo on annual plans.
                            </flux:text>
                            <flux:button variant="primary" size="xs" class="mt-1" href="{{ route('customer.team.index') }}" icon-trailing="arrow-right">Manage team</flux:button>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">Premium support</div>
                            <flux:text class="text-sm">Private support channels with expedited turnaround on your issues.</flux:text>
                            <flux:button variant="primary" size="xs" class="mt-1" href="{{ route('customer.support.tickets') }}" icon-trailing="arrow-right">Support tickets</flux:button>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">Up to 90% Marketplace revenue</div>
                            <flux:text class="text-sm">Keep up to 90% of earnings on paid plugins you publish to the Marketplace.</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">Exclusive discounts</div>
                            <flux:text class="text-sm">Discounted pricing on NativePHP courses, apps, and future products.</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">Direct repo access on GitHub</div>
                            <flux:text class="text-sm">Access the NativePHP source code repositories directly.</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div>
                            <div class="font-medium">Shape the roadmap</div>
                            <flux:text class="text-sm">Help decide feature priority and influence what gets built next.</flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>

            {{-- Included Plugins --}}
            @if($plugins->isNotEmpty())
                <div>
                    <flux:heading class="mb-3">Included Plugins</flux:heading>
                    <flux:table>
                        <flux:table.rows>
                            @foreach($plugins as $plugin)
                                <flux:table.row :key="$plugin->id">
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
                                            <div class="min-w-0">
                                                <a href="{{ route('plugins.show', $plugin->routeParams()) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                    {{ $plugin->display_name ?? $plugin->name }}
                                                </a>
                                                @if ($plugin->display_name)
                                                    <flux:text class="font-mono text-xs">{{ $plugin->name }}</flux:text>
                                                @endif
                                                @if($plugin->description)
                                                    <flux:text class="text-xs line-clamp-1">{{ $plugin->description }}</flux:text>
                                                @endif
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
