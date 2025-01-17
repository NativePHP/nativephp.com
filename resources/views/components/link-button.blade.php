<a {{ $attributes->class(['
font-medium
border border-gray-300 dark:border-gray-700 rounded-lg
px-4 py-2
hover:text-[#00aaa6] hover:border-[#00aaa6]/25
flex items-center gap-2
text-sm
']) }}>
    {{ $slot }}
</a>
