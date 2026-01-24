{{-- Bifrost Card - Cloud development platform --}}
<a
    href="https://bifrost.nativephp.com/"
    target="_blank"
    rel="noopener noreferrer"
    onclick="fathom.trackEvent('bifrost_card_click');"
    class="group relative block h-full overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500/20 via-indigo-500/10 to-purple-500/20 p-0.5 ring-1 ring-zinc-200/50 transition duration-300 hover:ring-sky-400/50 dark:ring-sky-500/30"
    x-init="
        () => {
            motion.inView($el, (element) => {
                gsap.fromTo(
                    $el,
                    { y: 20, autoAlpha: 0 },
                    {
                        y: 0,
                        autoAlpha: 1,
                        duration: 0.6,
                        delay: 0.1,
                        ease: 'power2.out',
                    },
                )
            })
        }
    "
>
    <div class="relative flex h-full flex-col overflow-hidden rounded-xl bg-gradient-to-br from-[#F9F9F9] via-white to-[#F9F9F9] p-6 md:p-8 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        {{-- Animated glow --}}
        <div
            x-init="
                () => {
                    gsap.to($el, {
                        x: 30,
                        y: -20,
                        duration: 6,
                        repeat: -1,
                        yoyo: true,
                        ease: 'sine.inOut',
                    })
                }
            "
            class="pointer-events-none absolute -right-10 -top-10 size-40 rounded-full bg-sky-500/20 blur-[50px] transition duration-500 group-hover:bg-sky-500/30"
            aria-hidden="true"
        ></div>
        <div
            x-init="
                () => {
                    gsap.to($el, {
                        x: -20,
                        y: 15,
                        duration: 5,
                        repeat: -1,
                        yoyo: true,
                        ease: 'sine.inOut',
                    })
                }
            "
            class="pointer-events-none absolute -bottom-10 -left-10 size-32 rounded-full bg-purple-500/15 blur-[40px] transition duration-500 group-hover:bg-purple-500/25"
            aria-hidden="true"
        ></div>

        {{-- Badge --}}
        <div class="mb-4 inline-flex w-fit items-center gap-1.5 rounded-full bg-sky-500/20 px-2.5 py-1 text-xs font-medium text-sky-600 dark:text-sky-300">
            <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
            </svg>
            Cloud Platform
        </div>

        {{-- Logo --}}
        <div class="mb-2">
            <x-logos.bifrost class="h-6" />
        </div>

        {{-- Tagline --}}
        <p class="text-sm text-sky-600/80 dark:text-sky-200/80">
            Build in the cloud. Deploy anywhere.
        </p>

        {{-- Description --}}
        <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-slate-400">
            And when you've built your app, get it to the stores and into the hands of users as fast as humanly possible.
        </p>

        {{-- Bifrost Diagram --}}
        <div class="my-2 flex flex-1 items-center justify-center overflow-hidden">
            <x-illustrations.bifrost-diagram />
        </div>

        {{-- CTA --}}
        <div class="mt-4 flex items-center gap-2 text-sm font-medium text-sky-600 transition duration-300 group-hover:text-sky-500 dark:text-sky-400 dark:group-hover:text-sky-300">
            <span>Ship it!</span>
            <svg
                class="size-4 transition duration-300 group-hover:translate-x-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </div>
    </div>
</a>
