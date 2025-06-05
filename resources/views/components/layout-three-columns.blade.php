<x-layout>
    {{-- Main container --}}
    <main
        class="mx-auto flex w-full max-w-5xl grow items-start px-4 pt-1 xl:max-w-7xl 2xl:max-w-360"
    >
        {{-- Left sidebar --}}
        @if (! empty($sidebarLeft))
            <x-sidebar-left-navigation>
                {{ $sidebarLeft }}
            </x-sidebar-left-navigation>
        @endif

        <div class="flex w-full min-w-0 grow items-start px-2 pt-2">
            {{-- Content --}}
            <article class="flex w-full min-w-0 grow flex-col lg:pl-5 xl:pr-5">
                {{-- Docs navigation --}}
                <div
                    class="dark:bg-mirage z-40 overflow-x-hidden overflow-y-auto bg-white lg:hidden"
                >
                    <div class="px-3">
                        <x-platform-switcher />

                        <nav class="docs-navigation">{{ $sidebarLeft }}</nav>
                    </div>
                </div>

                {{-- Main content --}}
                <div class="mt-5">{{ $slot }}</div>
            </article>

            {{-- Right sidebar --}}
            @if (! empty($sidebarRight))
                <x-sidebar-right>
                    {{ $sidebarRight }}
                </x-sidebar-right>
            @endif
        </div>
    </main>
</x-layout>
