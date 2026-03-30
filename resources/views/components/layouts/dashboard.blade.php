<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        // Persisted theme preference: 'light' | 'dark' | 'system'
        themePreference: $persist('system').as('themeMode'),
        // Effective dark-mode flag derived from preference + OS
        isDark: false,
        prefersDarkQuery: window.matchMedia('(prefers-color-scheme: dark)'),
        applyTheme() {
            this.isDark =
                this.themePreference === 'dark' ||
                (this.themePreference === 'system' && this.prefersDarkQuery.matches)
        },
        init() {
            const valid = ['light', 'dark', 'system']

            // Initial compute
            this.applyTheme()

            // React to OS preference changes while in 'system' mode
            this.prefersDarkQuery.addEventListener('change', () => {
                if (this.themePreference === 'system') {
                    this.applyTheme()
                }
            })

            // React to user-selected preference changes
            this.$watch('themePreference', () => this.applyTheme())
        },
    }"
    x-bind:class="{ 'dark': isDark === true }"
>
    <head>
        <meta
            http-equiv="Content-Security-Policy"
            content="upgrade-insecure-requests"
        />
        <meta charset="utf-8" />
        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        />
        <meta
            name="csrf-token"
            content="{{ csrf_token() }}"
        />

        <title>{{ isset($title) ? $title . ' - ' : '' }}NativePHP</title>

        {{-- Favicon --}}
        <link
            rel="icon"
            href="{{ asset('favicon.svg') }}"
            type="image/svg+xml"
        />

        <!-- Fathom - beautiful, simple website analytics -->
        @production
            <script
                src="https://cdn.usefathom.com/script.js"
                data-site="HALHTNZU"
                defer
            ></script>
        @endproduction

        {{-- Styles --}}
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @livewireStyles
        @vite('resources/css/app.css')
        @stack('head')
    </head>
    <body
        x-cloak
        class="min-h-screen bg-white font-poppins antialiased dark:bg-zinc-900 dark:text-white"
    >
        <flux:sidebar sticky collapsible="mobile" class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    href="{{ route('welcome') }}"
                    logo="{{ asset('favicon.svg') }}"
                    name="NativePHP"
                />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
                    Dashboard
                </flux:sidebar.item>

                <flux:sidebar.item icon="key" href="{{ route('customer.licenses.list') }}" :current="request()->routeIs('customer.licenses.*')">
                    Licenses
                </flux:sidebar.item>

                @feature(App\Features\ShowPlugins::class)
                    <flux:sidebar.item icon="shopping-bag" href="{{ route('customer.purchased-plugins.index') }}" :current="request()->routeIs('customer.purchased-plugins.*')">
                        Purchased Plugins
                    </flux:sidebar.item>
                @endfeature

                <flux:sidebar.item icon="clock" href="{{ route('customer.purchase-history.index') }}" :current="request()->routeIs('customer.purchase-history.*')">
                    Purchase History
                </flux:sidebar.item>

                @if(auth()->user()->hasUltraAccess())
                    <flux:sidebar.item icon="chat-bubble-left-right" href="{{ route('customer.support.tickets') }}" :current="request()->routeIs('customer.support.*')">
                        Support Tickets
                    </flux:sidebar.item>
                @endif

                <flux:sidebar.group expandable :expanded="false" heading="Community" class="mt-4 grid">
                    <flux:sidebar.item href="{{ route('customer.showcase.index') }}" :current="request()->routeIs('customer.showcase.*')">
                        Showcase
                    </flux:sidebar.item>
                    @if(auth()->user()->licenses()->where('created_at', '<', '2025-06-01')->exists())
                        @php
                            $wallOfLoveSubmission = auth()->user()->wallOfLoveSubmissions()->first();
                            $wallOfLoveUrl = $wallOfLoveSubmission
                                ? route('customer.wall-of-love.edit', $wallOfLoveSubmission)
                                : route('customer.wall-of-love.create');
                        @endphp
                        <flux:sidebar.item href="{{ $wallOfLoveUrl }}" :current="request()->routeIs('customer.wall-of-love.*')">
                            Wall of Love
                        </flux:sidebar.item>
                    @endif
                    <flux:sidebar.item href="https://discord.gg/nativephp" target="_blank">
                        Discord
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if(auth()->user()->hasActiveUltraSubscription() || auth()->user()->isUltraTeamMember())
                    @php
                        $ownedTeam = auth()->user()->ownedTeam;
                        $teamMemberships = auth()->user()->activeTeamMemberships();
                    @endphp
                    <flux:sidebar.group expandable :expanded="false" heading="Teams" class="mt-4 grid">
                        @if($ownedTeam)
                            <flux:sidebar.item href="{{ route('customer.team.index') }}" :current="request()->routeIs('customer.team.index')">
                                {{ $ownedTeam->name }}
                            </flux:sidebar.item>
                        @endif

                        @foreach($teamMemberships as $membership)
                            <flux:sidebar.item href="{{ route('customer.team.show', $membership->team) }}" :current="request()->routeIs('customer.team.show') && request()->route('team')?->id === $membership->team->id">
                                {{ $membership->team->name }}
                            </flux:sidebar.item>
                        @endforeach

                        @if(! $ownedTeam && $teamMemberships->isEmpty() && auth()->user()->hasActiveUltraSubscription())
                            <flux:sidebar.item href="{{ route('customer.team.index') }}" :current="request()->routeIs('customer.team.index')">
                                Create Team
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif

                @feature(App\Features\ShowPlugins::class)
                    <flux:sidebar.group expandable :expanded="false" heading="Developer" class="mt-4 grid">
                        <flux:sidebar.item href="{{ route('customer.developer.onboarding') }}" :current="request()->routeIs('customer.developer.onboarding', 'customer.developer.dashboard')">
                            Hub
                        </flux:sidebar.item>
                        <flux:sidebar.item href="{{ route('customer.plugins.index') }}" :current="request()->routeIs('customer.plugins.*')">
                            My Plugins
                        </flux:sidebar.item>
                        <flux:sidebar.item href="{{ route('customer.developer.settings') }}" :current="request()->routeIs('customer.developer.settings')">
                            Settings
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endfeature
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp

            <flux:sidebar.nav>
                <flux:sidebar.item icon="bell" href="{{ route('customer.notifications') }}" :current="request()->routeIs('customer.notifications')" :badge="$unreadCount > 0 ? $unreadCount : null">
                    Notifications
                </flux:sidebar.item>

                <flux:sidebar.item icon="link" href="{{ route('customer.integrations') }}" :current="request()->routeIs('customer.integrations')">
                    Integrations
                </flux:sidebar.item>

                <flux:sidebar.item icon="credit-card" href="{{ route('customer.billing-portal') }}">
                    Manage Subscription
                </flux:sidebar.item>

            </flux:sidebar.nav>

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile :name="auth()->user()->name ?? auth()->user()->email" />

                <flux:menu>
                    <flux:menu.item icon="cog-6-tooth" href="{{ route('customer.settings') }}">Settings</flux:menu.item>
                    <flux:menu.item icon="arrow-right-start-on-rectangle" x-on:click.prevent="$refs.logoutForm.submit()">
                        Log out
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <form x-ref="logoutForm" method="POST" action="{{ route('customer.logout') }}" class="hidden">
                @csrf
            </form>
        </flux:sidebar>

        {{-- Mobile header with sidebar toggle --}}
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <a href="{{ route('customer.notifications') }}" class="relative p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                <x-heroicon-o-bell class="size-5" />
                @if ($unreadCount > 0)
                    <span class="absolute right-1 top-1 size-2 rounded-full bg-blue-500"></span>
                @endif
            </a>

            <flux:dropdown position="top" align="start">
                <flux:profile :name="auth()->user()->name ?? auth()->user()->email" />

                <flux:menu>
                    <flux:menu.item icon="cog-6-tooth" href="{{ route('customer.settings') }}">Settings</flux:menu.item>
                    <flux:menu.item icon="arrow-right-start-on-rectangle" x-on:click.prevent="document.querySelector('form[x-ref=logoutForm]').submit()">
                        Log out
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main container>
            {{ $slot }}
        </flux:main>

        <x-impersonate::banner/>

        @livewireScriptConfig
        @fluxScripts
        @vite('resources/js/app.js')
    </body>
</html>
