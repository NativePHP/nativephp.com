@props(['key-value'])

<span
    x-data="{ copied: false }"
    class="inline-flex items-center gap-2"
>
    <code class="font-mono text-sm text-zinc-500 dark:text-zinc-400 select-none">{{ Str::substr($keyValue, 0, 4) }}****{{ Str::substr($keyValue, -4) }}</code>
    <flux:button
        size="xs"
        variant="ghost"
        x-on:click="navigator.clipboard.writeText('{{ $keyValue }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
    >
        <span x-show="!copied">Copy</span>
        <span x-show="copied" x-cloak>Copied!</span>
    </flux:button>
</span>
