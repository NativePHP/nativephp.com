{{-- Course Card - NativePHP Masterclass --}}
<a
    href="{{ route('course') }}"
    class="group relative block h-full overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/20 via-teal-500/10 to-cyan-500/20 p-0.5 ring-1 ring-zinc-200/50 transition duration-300 hover:ring-emerald-400/50 dark:ring-emerald-500/30"
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
                        delay: 0.2,
                        ease: 'power2.out',
                    },
                )
            })
        }
    "
>
    <div class="relative flex h-full flex-col overflow-hidden rounded-xl bg-gradient-to-br from-[#F9F9F9] via-white to-[#F9F9F9] p-5 md:p-6 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        {{-- Animated glow --}}
        <div
            x-init="
                () => {
                    gsap.to($el, {
                        x: 20,
                        y: -10,
                        duration: 4,
                        repeat: -1,
                        yoyo: true,
                        ease: 'sine.inOut',
                    })
                }
            "
            class="pointer-events-none absolute -right-10 -top-10 size-32 rounded-full bg-emerald-500/20 blur-[40px] transition duration-500 group-hover:bg-emerald-500/30"
            aria-hidden="true"
        ></div>

        {{-- Badge --}}
        <div class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-500/20 px-2.5 py-1 text-xs font-medium text-emerald-600 dark:text-emerald-300">
            <span class="relative flex size-2">
                <span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
            </span>
            Early Bird
        </div>

        {{-- Title --}}
        <h3 class="text-xl font-bold text-gray-800 md:text-2xl dark:text-white">
            The Masterclass
        </h3>

        {{-- Tagline --}}
        <p class="mt-1 text-sm text-emerald-600/80 dark:text-emerald-200/80">
            Zero to published app.
        </p>

        {{-- Description --}}
        <p class="mt-3 flex-1 text-sm leading-relaxed text-gray-600 dark:text-slate-400">
            Learn to build native mobile and desktop apps using PHP and Laravel.
        </p>

        {{-- Features list --}}
        <ul class="mt-3 space-y-1.5 text-xs text-gray-600 dark:text-slate-400">
            <li class="flex items-center gap-2">
                <svg class="size-3 text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Mobile & Desktop
            </li>
            <li class="flex items-center gap-2">
                <svg class="size-3 text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Use your existing PHP skills
            </li>
            <li class="flex items-center gap-2">
                <svg class="size-3 text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Zero to published app
            </li>
        </ul>

        {{-- CTA --}}
        <div class="mt-4 flex items-center gap-2 text-sm font-medium text-emerald-600 transition duration-300 group-hover:text-emerald-500 dark:text-emerald-400 dark:group-hover:text-emerald-300">
            <span>Start learning</span>
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
