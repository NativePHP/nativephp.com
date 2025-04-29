<a
    href="/mobile"
    onclick="fathom.trackEvent('alert_click');"
    class="group relative z-30 flex items-center justify-center gap-2 bg-gradient-to-r from-[#352F5B] to-[#6056AA] px-5 py-2.5 text-center"
>
    {{-- Text --}}
    <div
        class="transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-1"
    >
        <div
            class="bg-clip-text tracking-tight text-transparent"
            style="
                background-image: linear-gradient(
                    90deg,
                    #8d89b5 0%,
                    white 35%,
                    #8d89b5 70%
                );
                background-size: 200% 100%;
                animation: shine 2s linear infinite;
            "
        >
            Join our Mobile Early Access Program
        </div>
    </div>

    {{-- Arrow --}}
    <x-icons.right-arrow
        class="size-3 shrink-0 text-white transition duration-200 ease-in-out will-change-transform group-hover:translate-x-1"
    />
</a>
