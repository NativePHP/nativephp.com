@props([
    'deadline',
    'expired' => false,
    'heading' => 'Price increases to $299 in',
])

<div
    x-data="{
        deadline: new Date('{{ $deadline }}').getTime(),
        expired: {{ $expired ? 'true' : 'false' }},
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        tick() {
            const diff = this.deadline - Date.now()

            if (diff <= 0) {
                this.expired = true
                return
            }

            this.days = Math.floor(diff / 86400000)
            this.hours = Math.floor((diff % 86400000) / 3600000)
            this.minutes = Math.floor((diff % 3600000) / 60000)
            this.seconds = Math.floor((diff % 60000) / 1000)
        },
        init() {
            this.tick()
            setInterval(() => this.tick(), 1000)
        },
    }"
    x-show="!expired"
    x-cloak
    {{ $attributes->merge(['class' => 'rounded-xl bg-white/60 p-4 dark:bg-white/5']) }}
>
    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ $heading }}</p>
    <div class="mt-2 grid grid-cols-4 gap-2 text-center">
        <div>
            <span class="block text-2xl font-bold text-gray-900 tabular-nums dark:text-white" x-text="days">0</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Days</span>
        </div>
        <div>
            <span class="block text-2xl font-bold text-gray-900 tabular-nums dark:text-white" x-text="hours">0</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Hours</span>
        </div>
        <div>
            <span class="block text-2xl font-bold text-gray-900 tabular-nums dark:text-white" x-text="minutes">0</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Mins</span>
        </div>
        <div>
            <span class="block text-2xl font-bold text-gray-900 tabular-nums dark:text-white" x-text="seconds">0</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Secs</span>
        </div>
    </div>
</div>
