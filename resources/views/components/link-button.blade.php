<a {{ $attributes->class(['
inline-block
w-fit
font-medium
shadow-sm dark:shadow-white/10
border border-gray-300 dark:border-transparent
bg-white dark:bg-gray-700
rounded-lg
px-4 py-2
text-black dark:text-gray-100
hover:text-[#00aaa6] hover:border-[#00aaa6]/25 dark:hover:border-transparent
flex items-center gap-2
text-sm
']) }}>
    {{ $slot }}
</a>
