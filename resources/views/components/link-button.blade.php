<a {{ $attributes->class(['
border border-gray-300 dark:border-gray-700 rounded
px-4 py-2
hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700
dark:hover:text-gray-300
flex items-center gap-2
text-sm
']) }}>
    {{ $slot }}
</a>
