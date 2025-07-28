<a
    href="https://bifrost.nativephp.com/"
    onclick="fathom.trackEvent('alert_click');"
    class="group relative z-30 flex flex-col items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-slate-200/60 px-5 py-3 select-none sm:flex-row dark:bg-gray-950/50"
>
    {{-- Left side decorations --}}
    <div class="absolute top-1/2 left-2 -z-5 hidden -translate-y-1/2 md:block">
        <div class="flex items-center gap-2">
            {{-- Lines --}}
            <div
                class="flex items-center *:-mr-1 *:h-0.5 *:w-4 *:-rotate-50 *:rounded-full *:bg-slate-300 *:dark:bg-gray-600"
            >
                <div class="opacity-10"></div>
                <div class="opacity-20"></div>
                <div class="opacity-30"></div>
                <div class="opacity-40"></div>
                <div class="opacity-50"></div>
                <div class="opacity-60"></div>
                <div class="opacity-70"></div>
                <div class="opacity-80"></div>
            </div>

            {{-- Arrow --}}
            <x-icons.modern-arrow
                class="h-3.5 text-slate-300 dark:text-gray-600"
            />
        </div>
    </div>

    {{-- Right side decorations --}}
    <div class="absolute top-1/2 right-2 -z-5 hidden -translate-y-1/2 md:block">
        <div class="flex items-center gap-2">
            {{-- Arrow --}}
            <x-icons.modern-arrow
                class="h-3.5 -scale-x-100 -scale-y-100 text-slate-300 dark:text-gray-600"
            />

            {{-- Lines --}}
            <div
                class="flex items-center *:-mr-1 *:h-0.5 *:w-4 *:rotate-50 *:rounded-full *:bg-slate-300 *:dark:bg-gray-600"
            >
                <div class="opacity-80"></div>
                <div class="opacity-70"></div>
                <div class="opacity-60"></div>
                <div class="opacity-50"></div>
                <div class="opacity-40"></div>
                <div class="opacity-30"></div>
                <div class="opacity-20"></div>
                <div class="opacity-10"></div>
            </div>
        </div>
    </div>

    {{-- Bifrost --}}
    <div
        class="flex items-center gap-2.5 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        <x-logos.bifrost class="h-4" />
    </div>

    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        {{-- Text --}}
        <div>
            <style>
                .gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        #000 0%,
                        #5e657b 35%,
                        #000 70%
                    );
                    background-size: 200% 100%;
                    animation: shine 2s linear infinite;
                }
                .dark .gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        #fff 0%,
                        #c4e4ff 35%,
                        #fff 70%
                    );
                }
                @keyframes shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="gradient-text bg-clip-text text-center tracking-tight text-pretty text-transparent"
            >
                The fastest way to compile, sign and distribute your apps for every platform
            </div>
        </div>

        {{-- Arrow --}}
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>

    {{-- Left blur --}}
    <div class="absolute right-1/2 -bottom-11 -z-10 translate-x-1/2">
        <div
            class="h-10 w-36 -translate-x-10 -rotate-15 rounded-full bg-sky-300 blur-xl dark:bg-sky-500/60"
        ></div>
    </div>

    {{-- Right blur --}}
    <div class="absolute right-1/2 -bottom-11 -z-10 translate-x-1/2">
        <div
            class="h-10 w-36 translate-x-10 -rotate-15 rounded-full bg-pink-300 blur-xl dark:bg-slate-400/60"
        ></div>
    </div>
</a>
