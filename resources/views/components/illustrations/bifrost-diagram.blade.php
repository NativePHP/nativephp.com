<div class="flex items-center justify-center py-10">
    {{-- NativePHP --}}
    <div
        class="hidden flex-col items-center gap-2 pr-2 text-center 2xs:flex sm:pr-0"
    >
        <x-mini-logo class="h-7 sm:h-9" />

        {{-- NativePHP --}}
        <div class="hidden whitespace-nowrap sm:block md:text-lg">
            <span class="font-semibold">NativePHP</span>
            app
        </div>
    </div>

    {{-- Left line --}}
    <div
        class="hidden h-0.5 w-10 bg-gradient-to-r from-transparent to-blue-500/50 2xs:block sm:w-20"
    ></div>

    {{-- Center --}}
    <div class="relative">
        {{-- Decorative blank box --}}
        <div
            class="absolute -top-26 left-0 -z-10 grid h-23 w-35 place-items-center rounded-2xl mask-t-from-0% ring-3 ring-gray-200 ring-inset xs:w-40 sm:-top-30 sm:h-27 sm:w-47 dark:ring-white/3"
        >
            <div
                class="h-1.5 w-15 rounded-full bg-gray-200 dark:bg-white/3"
            ></div>
        </div>

        <div
            class="grid h-23 w-35 place-items-center overflow-hidden rounded-2xl bg-gradient-to-tl from-transparent to-blue-500/50 p-[3px] xs:w-40 sm:h-27 sm:w-47"
        >
            <div
                class="grid h-full w-full place-items-center rounded-[calc(var(--radius-2xl)-2px)] bg-white dark:bg-slate-950"
            >
                <x-logos.bifrost class="h-3.5 xs:h-4 sm:h-5" />
            </div>
        </div>

        {{-- Decorative blank box --}}
        <div
            class="absolute -bottom-26 left-0 -z-10 grid h-23 w-35 place-items-center rounded-2xl mask-b-from-0% ring-3 ring-gray-200 ring-inset xs:w-40 sm:-bottom-30 sm:h-27 sm:w-47 dark:ring-white/3"
        >
            <div
                class="h-1.5 w-15 rounded-full bg-gray-200 dark:bg-white/3"
            ></div>
        </div>
    </div>

    {{-- Right --}}
    <div class="flex items-center sm:pr-14 md:pr-24">
        <div>
            <x-icons.home.arc-connector />
            <div
                class="-my-0.5 h-0.5 w-16 rounded-full bg-gradient-to-r from-blue-300/20 to-blue-500/80"
            ></div>
            <div class="-scale-y-100"><x-icons.home.arc-connector /></div>
        </div>

        {{-- Platforms --}}
        <div class="relative flex flex-col gap-4">
            {{-- Decorative blank box --}}
            <div
                class="absolute -top-15 left-0 -z-10 grid size-12 place-items-center rounded-xl mask-t-from-0% ring-2 ring-gray-200 ring-inset dark:ring-white/3"
            >
                <div
                    class="h-1 w-5 rounded-full bg-gray-200 dark:bg-white/3"
                ></div>
            </div>

            <div
                class="grid size-12 place-items-center rounded-xl bg-white dark:bg-slate-950/50"
            >
                <x-icons.home.apple class="h-8" />
            </div>
            <div
                class="grid size-12 place-items-center rounded-xl bg-white dark:bg-slate-950/50"
            >
                <x-icons.home.android class="h-7" />
            </div>
            <div
                class="grid size-12 place-items-center rounded-xl bg-white dark:bg-slate-950/50"
            >
                <x-icons.home.windows class="h-6" />
            </div>

            {{-- Decorative blank box --}}
            <div
                class="absolute -bottom-15 left-0 -z-10 grid size-12 place-items-center rounded-xl mask-b-from-0% ring-2 ring-gray-200 ring-inset dark:ring-white/3"
            >
                <div
                    class="h-1 w-5 rounded-full bg-gray-200 dark:bg-white/3"
                ></div>
            </div>
        </div>
    </div>
</div>
