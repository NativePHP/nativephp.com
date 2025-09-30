@props([
    'name' => '',
])
<div
    class="dark:bg-cloud/30 xs:text-base xs:pr-5 xs:pl-4 inline-flex items-center gap-2 rounded-xl bg-white/50 py-2.5 pr-4.5 pl-3.5 text-sm text-slate-600 transition duration-200 will-change-transform dark:text-gray-400"
>
    <div
        class="xs:*:size-5.5 *:size-5"
        aria-hidden="true"
    >
        {{ $slot }}
    </div>

    <div>{{ $name }}</div>
</div>
