@props(['hasMenu' => false])
<x-eap-banner />
<nav
    class="sticky top-0 z-50 flex flex-col items-center justify-center px-3"
    aria-label="Main Navigation"
>
    <div
        :class="scrolled ? 'ring-gray-200/80 dark:ring-gray-800/50 translate-y-3 bg-white/50 dark:bg-white/5' : 'ring-transparent dark:bg-transparent'"
        class="2xl:max-w-8xl mx-auto flex w-full max-w-5xl items-center justify-between gap-5 rounded-2xl px-5 py-4 ring-1 backdrop-blur-md transition duration-200 ease-out xl:max-w-7xl"
    >
        {{-- Left side --}}
        <div class="flex items-center gap-3">
            {{-- Logo --}}
            <a
                href="/"
                aria-label="NativePHP Homepage"
            >
                <x-logo class="h-5 sm:h-6" />
                <span class="sr-only">NativePHP</span>
            </a>

            {{-- Version badge --}}
            <div
                class="hidden rounded-full bg-gray-200/60 px-2 py-1 text-xs text-gray-600 lg:block dark:bg-[#1f2032] dark:text-white/50"
                aria-label="Version information"
            >
                1.0.0-beta.2
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-3">
            {{-- Doc search --}}
            <div>
                <div
                    id="docsearch"
                    x-on:click="if (window.innerWidth < 640) window.scrollTo({ top: 0, behavior: 'instant' })"
                    aria-label="Search documentation"
                ></div>
            </div>

            {{-- Desktop menu --}}
            <div
                class="flex items-center gap-3.5 text-sm"
                aria-label="Primary navigation"
            >
                {{-- Theme toggle --}}
                <x-theme-toggle />

                {{-- Link --}}
                <a
                    href="/"
                    @class([
                        'hidden transition duration-200 lg:block',
                        'font-medium' => request()->routeIs('welcome*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('welcome*'),
                    ])
                    aria-current="{{ request()->routeIs('welcome*') ? 'page' : 'false' }}"
                >
                    Home
                </a>

                {{-- Decorative circle --}}
                <div
                    class="hidden size-[3px] rotate-45 rounded-sm bg-gray-400 lg:block"
                    aria-hidden="true"
                ></div>

                {{-- Link --}}
                <a
                    href="{{ route('early-adopter') }}"
                    @class([
                        'hidden transition duration-200 lg:block',
                        'font-medium' => request()->routeIs('early-adopter*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('early-adopter*'),
                    ])
                    aria-current="{{ request()->routeIs('early-adopter*') ? 'page' : 'false' }}"
                >
                    Mobile
                </a>
                {{-- Decorative circle --}}
                <div
                    class="hidden size-[3px] rotate-45 rounded-sm bg-gray-400 lg:block"
                    aria-hidden="true"
                ></div>
                @if ($hasMenu)
                    <button
                        type="button"
                        @click="showDocsNavigation = !showDocsNavigation"
                        class="block p-2 focus:outline-none focus:ring-0 lg:hidden"
                        aria-expanded="false"
                        aria-controls="docs-navigation"
                        aria-label="Toggle documentation menu"
                    >
                        <div x-show="!showDocsNavigation">
                            <x-icons.menu
                                class="h-6 w-6 text-gray-600 dark:text-gray-300"
                                aria-hidden="true"
                            />
                        </div>
                        <div x-show="showDocsNavigation">
                            <x-icons.close
                                class="h-6 w-6 text-gray-600"
                                aria-hidden="true"
                            />
                        </div>
                    </button>
                    <a
                        href="/docs/"
                        @class([
                            'hidden transition duration-200 lg:block',
                            'font-medium' => request()->is('docs*'),
                            'opacity-60 hover:opacity-100' => ! request()->is('docs*'),
                        ])
                        aria-current="{{ request()->is('docs*') ? 'page' : 'false' }}"
                    >
                        Docs
                    </a>
                @else
                    <a
                        href="/docs/"
                        @class([
                            'transition duration-200',
                            'font-medium' => request()->is('docs*'),
                            'opacity-60 hover:opacity-100' => ! request()->is('docs*'),
                        ])
                        aria-current="{{ request()->is('docs*') ? 'page' : 'false' }}"
                    >
                        Docs
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
