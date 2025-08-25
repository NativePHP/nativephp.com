@props([
    'align' => 'left',
])
<div {{ $attributes->class([
    'flex sm:flex-row items-center gap-x-6 gap-y-4',
    'justify-start flex-col-reverse' => $align === 'left',
    'justify-center flex-col' => $align === 'center',
    'justify-between flex-col-reverse' => $align === 'between',
    'justify-end flex-col-reverse' => $align === 'right'
    ]) }}>
    {{ $slot }}
</div>
