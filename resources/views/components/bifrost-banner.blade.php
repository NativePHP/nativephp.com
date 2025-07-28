<a
    href="https://bifrost.nativephp.com/"
    onclick="fathom.trackEvent('alert_click');"
    class="group relative z-30 flex items-center justify-center gap-x-2 gap-y-2.5 overflow-hidden bg-slate-100 px-5 py-3 select-none dark:bg-[#050714]"
>
    {{-- Laracon --}}
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
                class="gradient-text bg-clip-text tracking-tight text-transparent"
            >
                Build for anything. From anywhere. With PHP
            </div>
        </div>
        {{-- Arrow --}}
        <x-icons.right-arrow class="size-3 shrink-0 text-white" />
    </div>

    {{-- Left blur --}}
    <div class="absolute top-12 right-1/2 -z-10 translate-x-1/2">
        <div
            class="h-10 w-36 -translate-x-10 -rotate-15 rounded-full bg-sky-300 blur-xl dark:bg-sky-500/60"
        ></div>
    </div>

    {{-- Right blur --}}
    <div class="absolute top-12 right-1/2 -z-10 translate-x-1/2">
        <div
            class="h-10 w-36 translate-x-10 -rotate-15 rounded-full bg-pink-300 blur-xl dark:bg-slate-400/60"
        ></div>
    </div>
</a>
