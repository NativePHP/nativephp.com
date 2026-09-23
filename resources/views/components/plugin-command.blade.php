@props(['command'])

<div class="flex items-center gap-2 rounded-lg bg-zinc-900 dark:bg-zinc-800">
    <div class="min-w-0 flex-1 overflow-x-auto p-3">
        <code class="block whitespace-pre font-mono text-xs text-zinc-100">{{ $slot->isEmpty() ? $command : $slot }}</code>
    </div>
    <button
        type="button"
        x-data="{ copied: false }"
        x-on:click="navigator.clipboard.writeText(@js($command)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
        class="flex shrink-0 items-center self-stretch px-3 text-zinc-400 hover:text-zinc-200"
        title="Copy command"
    >
        <x-heroicon-o-clipboard x-show="!copied" class="size-4" />
        <x-heroicon-o-check-circle x-show="copied" x-cloak class="size-4 text-green-400" />
    </button>
</div>
