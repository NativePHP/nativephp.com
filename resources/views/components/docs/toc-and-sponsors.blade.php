{{-- Copy as Markdown Button --}}
<x-docs.copy-markdown-button />

<div
    class="mt-3 max-w-52 border-t border-t-black/20 pt-5 dark:border-t-white/15"
>
    {{ $slot }}

    {{-- Partners --}}
    <div class="mt-4 space-y-3">
        <x-sponsors.lists.docs.featured-sponsors />
    </div>

    <a href="/partners" class="mt-3 block text-center text-xs text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Become a Partner</a>

    {{-- Sponsors --}}
    <div class="mt-5 border-t border-t-black/20 pt-5 dark:border-t-white/15">
        <x-sponsors.lists.docs.sponsors />

        <a href="/sponsor" class="mt-3 block text-center text-xs text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Become a sponsor</a>
    </div>
</div>
