@props([
    'link' => '#',
    'name' => '',
])
<a
    href="{{ $link }}"
    class="dark:bg-cloud/30 dark:hover:bg-cloud/50 inline-flex items-center gap-2 rounded-full bg-white/50 py-2 pr-4 pl-3.5 text-sm text-slate-500 ring-1 ring-slate-200 transition duration-200 will-change-transform hover:scale-105 hover:bg-white/70 hover:text-black hover:ring-slate-200 2xl:py-2.5 2xl:pr-5 2xl:pl-4 2xl:text-base dark:text-gray-400 dark:ring-slate-700/80 dark:hover:text-white dark:hover:ring-slate-700"
>
    <div class="*:size-5 2xl:*:size-6">{{ $slot }}</div>

    <div>{{ $name }}</div>
</a>
