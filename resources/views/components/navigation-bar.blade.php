@props(['hasMenu' => false])
<x-eap-banner />
<nav class="sticky top-0 z-50 flex flex-col items-center justify-center px-3">
    <div
        x-data="{ scrolled: false }"
        x-init="
            window.addEventListener('scroll', () => {
                scrolled = window.scrollY > 20
            })
        "
        :class="scrolled ? 'ring-gray-200/80 translate-y-3' : 'ring-transparent'"
        class="mx-auto flex w-full max-w-5xl items-center justify-between gap-5 rounded-2xl bg-white/50 px-5 py-4 ring-1 backdrop-blur transition duration-200 ease-out dark:bg-gray-800/85"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <a href="/">
                <x-logo class="h-5 sm:h-6" />
                <span class="sr-only">NativePHP</span>
            </a>
            <div
                class="hidden rounded-full bg-gray-200/60 px-2 py-1 text-xs text-gray-600 lg:block"
            >
                1.0.0-beta.2
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Doc search --}}
            <div>
                <div
                    id="docsearch"
                    x-on:click="if (window.innerWidth < 640) window.scrollTo({ top: 0, behavior: 'instant' })"
                ></div>
            </div>
            {{-- Desktop menu --}}
            <div class="flex items-center gap-3.5 text-sm">
                <a
                    href="/"
                    @class([
                        'hidden transition duration-200 lg:block',
                        'font-medium' => request()->routeIs('welcome*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('welcome*'),
                    ])
                >
                    Home
                </a>
                <div
                    class="hidden size-[3px] rotate-45 rounded-sm bg-gray-400 lg:block"
                ></div>
                <a
                    href="{{ route('early-adopter') }}"
                    @class([
                        'hidden transition duration-200 lg:block',
                        'font-medium' => request()->routeIs('early-adopter*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('early-adopter*'),
                    ])
                >
                    Mobile
                </a>
                <div
                    class="hidden size-[3px] rotate-45 rounded-sm bg-gray-400 lg:block"
                ></div>
                @if ($hasMenu)
                    <button
                        type="button"
                        @click="showDocsNavigation = !showDocsNavigation"
                        class="p-2 focus:outline-none focus:ring-0"
                    >
                        <div x-show="!showDocsNavigation">
                            <x-icons.menu
                                class="h-6 w-6 text-gray-600 dark:text-gray-300"
                            />
                        </div>
                        <div x-show="showDocsNavigation">
                            <x-icons.close class="h-6 w-6 text-gray-600" />
                        </div>
                    </button>
                @else
                    <a
                        href="/docs/"
                        @class([
                            'transition duration-200',
                            'font-medium' => request()->is('docs*'),
                            'opacity-60 hover:opacity-100' => ! request()->is('docs*'),
                        ])
                    >
                        Docs
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
