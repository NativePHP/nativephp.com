<x-layout>
    {{-- Main container --}}
    <main class="flex grow items-start pt-1">
        {{-- Left sidebar --}}
        @if (! empty($sidebarLeft))
            <x-docs.sidebar-left-navigation>
                {{ $sidebarLeft }}
            </x-docs.sidebar-left-navigation>
        @endif

        <div class="flex w-full min-w-0 grow items-start px-2 pt-2">
            {{-- Content --}}
            <article class="flex w-full min-w-0 grow flex-col lg:pl-5 xl:pr-5">
                {{-- Docs mobile menu --}}
                <x-docs.menu>
                    <nav class="docs-navigation">{{ $sidebarLeft }}</nav>
                </x-docs.menu>

                {{-- Main content --}}
                <div class="mt-3">{{ $slot }}</div>

                {{-- Mobile partner card --}}
                <div class="mt-5 xl:hidden">
                    <x-sponsors.lists.docs.featured-sponsors />
                    <a href="/partners" class="mt-3 block text-center text-xs text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Become a Partner</a>
                </div>
            </article>

            {{-- Right sidebar --}}
            @if (! empty($sidebarRight))
                <x-docs.sidebar-right>
                    {{ $sidebarRight }}
                </x-docs.sidebar-right>
            @endif
        </div>
    </main>
</x-layout>
