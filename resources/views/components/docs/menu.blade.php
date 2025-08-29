@php
    $isMobile = request()->is('docs/mobile/*');
@endphp

<div class="lg:hidden">
    {{-- Docs menu button --}}
    <button
        type="button"
        x-on:click="showDocsMenu = !showDocsMenu"
        class="mb-2 flex w-full items-center gap-2.5 rounded-xl bg-gradient-to-tl from-transparent to-violet-100 px-3.5 py-3 focus:ring-0 focus:outline-none dark:from-slate-900/30 dark:to-indigo-400/30"
        :aria-expanded="showDocsMenu"
        aria-label="Toggle docs menu"
        aria-haspopup="true"
        title="Open docs navigation"
    >
        <x-icons.list-down class="size-6" />

        <div class="text-left">
            <div class="leading-6">Menu</div>
            <div class="text-xs capitalize opacity-50">
                for {{ $isMobile ? 'Mobile' : 'Desktop' }}
            </div>
        </div>
    </button>

    {{-- Docs mobile menu --}}
    <div
        x-show="showDocsMenu"
        x-collapse
        role="dialog"
        aria-modal="true"
        aria-label="Docs menu"
        class="rounded-xl bg-gray-100 dark:bg-mirage"
    >
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
