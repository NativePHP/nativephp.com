{{-- How a NativePHP for Desktop app is put together: a bundled PHP binary talking to an Electron shell over authenticated HTTP. --}}
<div
    class="w-58 overflow-hidden rounded-xl ring-1 ring-[#333333] dark:ring-gray-500"
>
    {{-- Title bar --}}
    <div
        class="flex h-7 items-center gap-1.5 bg-gray-300/70 px-3 dark:bg-gray-700"
        aria-hidden="true"
    >
        <div class="size-1.5 rounded-full bg-red-400"></div>
        <div class="size-1.5 rounded-full bg-amber-400"></div>
        <div class="size-1.5 rounded-full bg-green-400"></div>
    </div>
    {{-- Schema --}}
    <div
        class="flex flex-col gap-3 bg-white/50 p-3 text-xs whitespace-nowrap dark:bg-slate-950/80"
    >
        {{-- Header --}}
        <div>
            <div class="text-sm font-medium text-gray-800 dark:text-white">
                Electron
            </div>
            <div class="text-gray-600 dark:text-zinc-400">Shell app</div>
        </div>
        {{-- Php runtime --}}
        <div
            class="php-dashed-border grid place-items-center gap-2 rounded-lg px-2 pt-3.5 pb-4.5"
        >
            <div class="text-sm font-medium text-gray-800 dark:text-white">
                PHP Binary
            </div>
            <div
                class="grid w-full place-items-center rounded-lg bg-gray-200 px-2 py-6 dark:bg-gray-800"
            >
                <div class="font-medium text-gray-700 dark:text-white">
                    Static PHP Runtime
                </div>
            </div>
        </div>
        {{-- Core --}}
        <div class="flex items-center gap-2">
            {{-- Left --}}
            <div
                class="relative grid w-1/2 place-items-center rounded-lg bg-purple-200 px-3 py-7 dark:bg-violet-400/60"
            >
                <div
                    class="flex flex-col gap-0.5 text-center font-medium text-gray-700 capitalize dark:text-white"
                >
                    <div>Native</div>
                    <div>desktop</div>
                    <div>functions</div>
                </div>
                {{-- Bottom arrow --}}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute -top-12 right-1/2 w-2.5 translate-x-1/2"
                    viewBox="0 0 8 60"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M4 0.113249L1.11325 3L4 5.88675L6.88675 3L4 0.113249ZM3.64645 53.3536C3.84171 53.5488 4.15829 53.5488 4.35356 53.3536L7.53554 50.1716C7.7308 49.9763 7.7308 49.6597 7.53554 49.4645C7.34027 49.2692 7.02369 49.2692 6.82843 49.4645L4 52.2929L1.17157 49.4645C0.976313 49.2692 0.65973 49.2692 0.464468 49.4645C0.269206 49.6597 0.269206 49.9763 0.464468 50.1716L3.64645 53.3536ZM4 3L3.5 3L3.5 53L4 53L4.5 53L4.5 3L4 3Z"
                        fill="currentColor"
                    />
                </svg>
            </div>
            {{-- Right --}}
            <div
                class="relative grid w-1/2 place-items-center rounded-lg bg-[#d7f7a0] px-3 py-7 dark:bg-teal-300/70"
            >
                <div
                    class="flex flex-col gap-0.5 text-center font-medium text-gray-700 dark:text-white"
                >
                    <div>Authenticated</div>
                    <div>HTTP</div>
                    <div>bridge</div>
                </div>
                {{-- Top arrow --}}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute -top-6 right-1/2 w-2.5 translate-x-1/2"
                    viewBox="0 0 8 36"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M3.875 35.8868L6.76175 33L3.875 30.1132L0.988249 33L3.875 35.8868ZM4.22855 0.646446C4.03329 0.451183 3.71671 0.451183 3.52145 0.646446L0.339465 3.82843C0.144203 4.02369 0.144203 4.34027 0.339465 4.53553C0.534727 4.7308 0.851309 4.7308 1.04657 4.53553L3.875 1.70711L6.70343 4.53553C6.89869 4.7308 7.21527 4.7308 7.41053 4.53553C7.60579 4.34027 7.60579 4.02369 7.41053 3.82843L4.22855 0.646446ZM3.875 33L4.375 33L4.375 1L3.875 1L3.375 1L3.375 33L3.875 33Z"
                        fill="currentColor"
                    />
                </svg>
                {{-- Bottom arrow --}}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute right-1/2 -bottom-6 w-2.5 translate-x-1/2 -scale-y-100"
                    viewBox="0 0 8 36"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M3.875 35.8868L6.76175 33L3.875 30.1132L0.988249 33L3.875 35.8868ZM4.22855 0.646446C4.03329 0.451183 3.71671 0.451183 3.52145 0.646446L0.339465 3.82843C0.144203 4.02369 0.144203 4.34027 0.339465 4.53553C0.534727 4.7308 0.851309 4.7308 1.04657 4.53553L3.875 1.70711L6.70343 4.53553C6.89869 4.7308 7.21527 4.7308 7.41053 4.53553C7.60579 4.34027 7.60579 4.02369 7.41053 3.82843L4.22855 0.646446ZM3.875 33L4.375 33L4.375 1L3.875 1L3.375 1L3.375 33L3.875 33Z"
                        fill="currentColor"
                    />
                </svg>
            </div>
        </div>
        {{-- Chromium window --}}
        <div
            class="grid place-items-center gap-1.5 rounded-lg bg-violet-100 p-4 ring-1 ring-violet-300 dark:bg-violet-400/20 dark:ring-violet-300/40"
        >
            <div class="text-sm font-medium text-gray-800 dark:text-white">
                Chromium Window
            </div>
            <div class="text-gray-600 dark:text-zinc-400">
                HTML/CSS + JavaScript
            </div>
        </div>
    </div>
</div>
