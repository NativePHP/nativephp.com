@props([
    'items' => [],
    'label' => '',
    'unit' => '',
])

<div
    x-data="{
        animated: false,
        items: @js($items),
    }"
    x-init="
        () => {
            motion.inView($el, () => {
                if (!animated) {
                    animated = true
                    const bars = $el.querySelectorAll('[data-bar]')
                    const values = $el.querySelectorAll('[data-value]')

                    motion.animate(
                        Array.from(bars),
                        { scaleX: [0, 1] },
                        { duration: 1, ease: motion.backOut, delay: motion.stagger(0.15) }
                    )

                    motion.animate(
                        Array.from(values),
                        { opacity: [0, 1] },
                        { duration: 0.5, delay: motion.stagger(0.15, { start: 0.5 }) }
                    )
                }
            })
        }
    "
    class="w-full"
>
    @if ($label)
        <div class="mb-4 text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ $label }}
        </div>
    @endif

    <div class="space-y-4">
        @foreach ($items as $item)
            <div class="flex items-center gap-4">
                <div class="w-32 shrink-0 text-sm font-medium {{ $item['highlight'] ?? false ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-300' }}">
                    {{ $item['name'] }}
                </div>
                <div class="relative h-8 flex-1 overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-800">
                    <div
                        data-bar
                        class="absolute inset-y-0 left-0 origin-left rounded-lg {{ $item['highlight'] ?? false ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-gray-400 to-gray-300 dark:from-gray-600 dark:to-gray-500' }}"
                        style="width: {{ $item['percentage'] }}%;"
                    ></div>
                    <div
                        data-value
                        class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold opacity-0 {{ $item['highlight'] ?? false ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-400' }}"
                    >
                        {{ $item['value'] }}{{ $unit }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
