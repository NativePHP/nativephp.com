<a
    href="/laracon-us-2025-competition"
    onclick="fathom.trackEvent('alert_click');"
    class="group relative z-30 flex items-center justify-center gap-x-2 gap-y-2.5 overflow-hidden bg-[#050714] px-5 py-3 select-none"
>
    {{-- Laracon --}}
    <div
        class="flex items-center gap-2.5 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        <div
            x-init="
                () => {
                    gsap.timeline({
                        repeat: -1,
                    })
                        .fromTo(
                            $refs.evanYou,
                            {
                                autoAlpha: 0,
                            },
                            {
                                autoAlpha: 1,
                                delay: 1,
                            },
                        )
                        .to($refs.evanYou, {
                            autoAlpha: 0,
                            delay: 1,
                        })
                        .fromTo(
                            $refs.jeffreyWay,
                            {
                                autoAlpha: 0,
                            },
                            {
                                autoAlpha: 1,
                            },
                            '<',
                        )
                        .to($refs.jeffreyWay, {
                            autoAlpha: 0,
                            delay: 1,
                        })
                        .fromTo(
                            $refs.joeTannenbaum,
                            {
                                autoAlpha: 0,
                            },
                            {
                                autoAlpha: 1,
                            },
                            '<',
                        )
                        .to($refs.joeTannenbaum, {
                            autoAlpha: 0,
                            delay: 1,
                        })
                }
            "
            class="grid size-8 overflow-hidden rounded"
        >
            {{-- Taylor --}}
            <img
                x-ref="taylorOtwell"
                src="{{ Vite::asset('resources/images/laracon-us-2025/speakers/Taylor-Otwell.webp') }}"
                alt="Taylor Otwell"
                class="z-[1] h-full w-full object-cover [grid-area:1/-1]"
            />

            {{-- Evan --}}
            <img
                x-ref="evanYou"
                src="{{ Vite::asset('resources/images/laracon-us-2025/speakers/Evan-You.webp') }}"
                alt="Evan You"
                class="z-[2] h-full w-full object-cover [grid-area:1/-1]"
            />

            {{-- Jeffrey --}}
            <img
                x-ref="jeffreyWay"
                src="{{ Vite::asset('resources/images/laracon-us-2025/speakers/Jeffrey-Way.webp') }}"
                alt="Jeffrey Way"
                class="z-[3] h-full w-full object-cover [grid-area:1/-1]"
            />

            {{-- Joe --}}
            <img
                x-ref="joeTannenbaum"
                src="{{ Vite::asset('resources/images/laracon-us-2025/speakers/Joe-Tannenbaum.webp') }}"
                alt="Joe Tannenbaum"
                class="z-[5] h-full w-full object-cover [grid-area:1/-1]"
            />
        </div>

        {{-- Laracon US Icon --}}
        <x-icons.laracon-us class="h-4 text-white" />
    </div>

    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        {{-- Text --}}
        <div>
            <div
                class="bg-clip-text tracking-tight text-transparent"
                style="
                    background-image: linear-gradient(
                        90deg,
                        #ff8b9f 0%,
                        white 35%,
                        #ff8b9f 70%
                    );
                    background-size: 200% 100%;
                    animation: shine 2s linear infinite;
                "
            >
                Ticket Giveaway
            </div>
        </div>
        {{-- Arrow --}}
        <x-icons.right-arrow class="size-3 shrink-0 text-white" />
    </div>

    {{-- Left blur --}}
    <div
        class="absolute -top-10 -left-20 -z-10 size-36 rounded-full bg-violet-400 blur-2xl sm:-top-16 sm:-left-16 sm:size-52 sm:blur-3xl"
    ></div>

    {{-- Right blur --}}
    <div
        class="absolute -top-10 -right-20 -z-10 size-36 rounded-full bg-rose-500 blur-2xl sm:-top-16 sm:-right-16 sm:size-52 sm:blur-3xl"
    ></div>
</a>
