<aside
    x-init="
        () => {
            motion.inView($el, () => {
                gsap.fromTo(
                    $el,
                    { autoAlpha: 0, x: 5 },
                    { autoAlpha: 1, x: 0, duration: 0.7, ease: 'power1.out' },
                )
            })
        }
    "
    class="sticky top-20 right-0 hidden max-w-52 shrink-0 min-[850px]:block"
>
    <a
        href="{{ route('pricing') }}"
        class="group relative z-0 grid place-items-center overflow-hidden rounded-2xl bg-gray-100 px-4 pt-10 text-center text-pretty transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
    >
        {{-- Logo --}}
        <div>
            <x-logo class="h-5" />
            <span class="sr-only">NativePHP</span>
        </div>

        {{-- Tagline --}}
        <div class="mt-3">
            Bring your
            <strong>Laravel</strong>
            skills to
            <strong>mobile apps.</strong>
        </div>

        {{-- Iphone --}}
        <div class="mt-4 -mb-25">
            <img
                src="{{ Vite::asset('resources/images/home/iphone.webp') }}"
                alt=""
                aria-hidden="true"
                class="w-25 transition duration-200 will-change-transform group-hover:-translate-y-1 dark:brightness-80 dark:contrast-150"
                width="92"
                height="190"
                loading="lazy"
            />
        </div>

        {{-- Star 1 --}}
        <x-icons.star
            class="absolute top-6 right-3 z-10 w-4 -rotate-7 text-white dark:w-3 dark:text-slate-300"
        />
        {{-- Star 2 --}}
        <x-icons.star
            class="absolute top-3 right-14 z-10 w-3 rotate-5 text-white dark:w-2 dark:text-slate-300"
        />
        {{-- Star 3 --}}
        <x-icons.star
            class="absolute top-2.5 right-7.5 z-10 w-2.5 text-white dark:w-2 dark:text-slate-300"
        />
        {{-- White blur --}}
        <div class="absolute top-5 -right-10 -z-5">
            <div
                class="h-5 w-36 rotate-30 rounded-full bg-white/80 blur-md dark:bg-white/5"
            ></div>
        </div>
        {{-- Sky blur --}}
        <div class="absolute top-5 -right-20 -z-10">
            <div
                class="h-15 w-36 rotate-30 rounded-full bg-sky-300 blur-xl dark:bg-sky-500/30"
            ></div>
        </div>
        {{-- Violet blur --}}
        <div class="absolute -top-10 -right-5 -z-10">
            <div
                class="h-15 w-36 rotate-30 rounded-full bg-violet-300 blur-xl dark:bg-violet-400/30"
            ></div>
        </div>
    </a>

    {{-- Sponsors --}}
    <h3 class="mt-3 flex items-center gap-1.5 opacity-60">
        {{-- Icon --}}
        <x-icons.star-circle class="size-6" />
        {{-- Label --}}
        <div>Sponsors</div>
    </h3>
    {{-- List --}}
    <div class="space-y-3 pt-2.5">
        <x-sponsors.lists.docs.featured-sponsors />
    </div>
    {{-- List --}}
    <div class="space-y-3 pt-2.5">
        <x-sponsors.lists.docs.corporate-sponsors />
    </div>
</aside>
