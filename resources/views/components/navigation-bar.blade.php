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
                <x-logo class="h-6" />
                <span class="sr-only">NativePHP</span>
            </a>
            <div
                class="rounded-full bg-gray-200/60 px-2 py-1 text-xs text-gray-600"
            >
                1.0.0-beta.2
            </div>
        </div>

        <div class="flex items-center gap-5">
            {{-- Doc search --}}
            <div class="">
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
                        'transition duration-200',
                        'font-medium' => request()->routeIs('welcome*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('welcome*'),
                    ])
                >
                    Home
                </a>
                <div class="size-[3px] rotate-45 rounded-sm bg-gray-400"></div>
                <a
                    href="{{ route('early-adopter') }}"
                    @class([
                        'transition duration-200',
                        'font-medium' => request()->routeIs('early-adopter*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('early-adopter*'),
                    ])
                >
                    Mobile
                </a>
                <div class="size-[3px] rotate-45 rounded-sm bg-gray-400"></div>
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
            </div>
        </div>

        <div class="flex justify-end pl-4 lg:hidden">
            @if ($hasMenu)
                <button
                    type="button"
                    @click="showDocsNavigation = !showDocsNavigation"
                >
                    <div x-show="!showDocsNavigation">
                        <x-icons.menu
                            class="h-6 w-6 text-teal-600 dark:text-teal-300"
                        />
                    </div>
                    <div x-show="showDocsNavigation">
                        <x-icons.close class="h-6 w-6 text-teal-600" />
                    </div>
                </button>
            @else
                <a
                    href="{{ route('docs') }}"
                    class="flex items-center gap-1 text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-200"
                >
                    <x-icons.book-text class="size-5" />
                    <span class="text-md hidden sm:inline">Documentation</span>
                </a>
            @endif
        </div>
    </div>
</nav>
