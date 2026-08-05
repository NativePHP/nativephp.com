{{--
    How a NativePHP for Desktop app is put together: a bundled PHP binary
    talking to an Electron shell over authenticated HTTP. Framed and
    proportioned like an app window rather than a phone.
--}}
<div
    class="mx-auto w-full max-w-125 overflow-hidden rounded-xl ring-1 ring-[#333333] dark:ring-gray-500"
>
    {{-- Title bar --}}
    <div
        class="relative flex h-8 items-center gap-1.5 bg-gray-300/70 px-3 dark:bg-gray-700"
    >
        <div
            class="size-2 rounded-full bg-red-400"
            aria-hidden="true"
        ></div>
        <div
            class="size-2 rounded-full bg-amber-400"
            aria-hidden="true"
        ></div>
        <div
            class="size-2 rounded-full bg-green-400"
            aria-hidden="true"
        ></div>

        <div
            class="absolute inset-x-0 text-center text-xs text-gray-600 dark:text-zinc-300"
        >
            Electron shell
        </div>
    </div>

    {{-- Schema --}}
    <div
        class="flex flex-col gap-3 bg-white/50 p-4 text-xs dark:bg-slate-950/80"
    >
        {{-- PHP runtime --}}
        <div
            class="php-dashed-border grid place-items-center gap-2 rounded-lg px-3 pt-3.5 pb-4.5"
        >
            <div class="text-sm font-medium text-gray-800 dark:text-white">
                PHP Binary
            </div>
            <div
                class="grid w-full place-items-center rounded-lg bg-gray-200 px-2 py-6 text-center dark:bg-gray-800"
            >
                <div class="font-medium text-gray-700 dark:text-white">
                    Static PHP Runtime
                </div>
            </div>
        </div>

        {{-- Bridge --}}
        <div class="flex items-stretch gap-3">
            <div
                class="grid w-1/2 place-items-center rounded-lg bg-purple-200 px-3 py-6 text-center dark:bg-violet-400/60"
            >
                <div
                    class="font-medium text-gray-700 capitalize dark:text-white"
                >
                    Native desktop functions
                </div>
            </div>
            <div
                class="grid w-1/2 place-items-center rounded-lg bg-[#d7f7a0] px-3 py-6 text-center dark:bg-teal-300/70"
            >
                <div class="font-medium text-gray-700 dark:text-white">
                    Authenticated HTTP bridge
                </div>
            </div>
        </div>

        {{-- Chromium window --}}
        <div
            class="grid place-items-center gap-1.5 rounded-lg bg-violet-100 p-5 text-center ring-1 ring-violet-300 dark:bg-violet-400/20 dark:ring-violet-300/40"
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
