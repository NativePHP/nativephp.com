@props([
    'name' => '',
])
<div
    class="dark:bg-cloud/30 inline-flex items-center gap-2 rounded-xl bg-white/50 py-2.5 pr-5 pl-4 text-slate-600 transition duration-200 will-change-transform dark:text-gray-400"
>
    <div class="*:size-5.5">{{ $slot }}</div>

    <div>{{ $name }}</div>
</div>
