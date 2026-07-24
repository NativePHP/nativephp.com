@props(['path'])

@php
    // Points at this page on nativephp.com with a marker query param. Jump claims
    // /docs/* deep links there, so a device with the app opens it natively; a
    // device without it falls back to the browser, where the marker triggers the
    // <x-docs.jump-app-overlay> "get the app" prompt.
    $deepLink = \App\Support\JumpApp::docsDeepLink($path);
@endphp

{{-- Lives in the right sidebar, which is itself desktop-only (hidden xl:flex) --}}
<div
    x-data="{ open: false }"
    class="mt-4 max-w-52 rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-slate-800/50"
>
    <button
        type="button"
        x-on:click="open = !open"
        :aria-expanded="open"
        class="flex w-full items-center justify-between gap-2"
    >
        <span class="flex items-center gap-1.5 text-xs font-semibold text-gray-900 dark:text-white">
            <x-heroicon-o-bolt class="size-3.5 shrink-0 text-indigo-500" aria-hidden="true" />
            Preview in Jump
        </span>
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="size-3.5 shrink-0 text-gray-400 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div x-show="open" x-collapse x-cloak>
        {{-- Dark QR modules need a light background in both colour schemes --}}
        <div
            role="img"
            aria-label="Scan to open {{ $deepLink }} in the Jump app"
            class="mt-3 rounded-lg bg-white p-1.5"
        >
            {!! \App\Support\QrCode::svg($deepLink) !!}
        </div>

        <p class="mt-2 text-[11px] leading-snug text-gray-500 dark:text-gray-400">
            Scan with your phone to preview this component live.
        </p>
    </div>
</div>
