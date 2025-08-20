@props([
    'href' => '#',
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'github' => null,
    'tooltip' => null,
    'iconClass' => 'size-5',
])

<a
    href="{{ $href }}"
    @if ($title) aria-label="{{ $title }}" @endif
    class="group hover:bg-snow-flurry-50/70 hover:ring-snow-flurry-100 flex items-center gap-3 rounded-lg py-2 pr-3 pl-2 ring-1 ring-transparent transition"
    role="menuitem"
    tabindex="-1"
    {{ $attributes }}
>
    <div
        class="group-hover:bg-snow-flurry-200/30 group-hover:ring-snow-flurry-200/50 grid size-10 shrink-0 place-items-center rounded-lg bg-zinc-100 ring-1 ring-transparent transition ring-inset"
    >
        @if ($icon)
            <x-dynamic-component
                :component="'icons.' . $icon"
                class="{{ $iconClass }} transition will-change-transform group-hover:scale-95"
            />
        @endif
    </div>

    <div class="relative grow truncate">
        @if ($title)
            <div class="font-medium">{{ $title }}</div>
        @endif

        @if ($subtitle)
            <div class="mt-0.5 text-xs opacity-70 group-hover:mask-r-from-0%">
                {{ $subtitle }}
            </div>
        @endif

        <x-icons.right-arrow
            class="absolute top-1/2 right-1.5 size-3 -translate-y-1/2 opacity-0 transition will-change-transform group-hover:translate-x-1 group-hover:opacity-100"
        />
    </div>
</a>
