{{-- Copy as Markdown Button --}}
<div {{ $attributes->merge(['class' => 'mb-4']) }} x-data="copyMarkdown()" x-init="$watch('$el', () => {})">
    <button
        @click="copyMarkdownToClipboard()"
        class="flex items-center gap-1.5 text-sm opacity-60 hover:opacity-100 transition-opacity duration-200"
        title="Copy page as Markdown"
    >
        {{-- Icon --}}
        <x-icons.copy x-show="!showMessage" class="size-[18px]" />
        <x-icons.checkmark x-show="showMessage" x-cloak class="size-[18px]" />

        {{-- Label --}}
        <div x-show="!showMessage">Copy as Markdown</div>
        <div x-show="showMessage" x-cloak>Copied!</div>
    </button>
</div>