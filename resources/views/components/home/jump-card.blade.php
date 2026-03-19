{{-- Jump Card - Preview on real devices --}}
<a
    href="https://bifrost.nativephp.com/jump"
    target="_blank"
    rel="noopener noreferrer"
    class="group relative block h-full overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/20 via-cyan-500/10 to-indigo-500/20 p-0.5 ring-1 ring-zinc-200/50 transition duration-300 hover:ring-blue-400/50 dark:ring-blue-500/30"
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
                        delay: 0.4,
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
                        x: -15,
                        y: 15,
                        duration: 5,
                        repeat: -1,
                        yoyo: true,
                        ease: 'sine.inOut',
                    })
                }
            "
            class="pointer-events-none absolute -bottom-10 -left-10 size-32 rounded-full bg-blue-500/20 blur-[40px] transition duration-500 group-hover:bg-blue-500/30"
            aria-hidden="true"
        ></div>

        {{-- Platform badges (top-right) --}}
        <div class="absolute top-5 right-5 flex items-center gap-2 text-xs text-gray-500 md:top-6 md:right-6 dark:text-slate-500">
            <span class="rounded-full bg-gray-100 px-2.5 py-1 ring-1 ring-gray-200 dark:bg-slate-800/50 dark:ring-slate-700/50">
                iOS
            </span>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 ring-1 ring-gray-200 dark:bg-slate-800/50 dark:ring-slate-700/50">
                Android
            </span>
        </div>

        {{-- Badge --}}
        <div class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-blue-500/20 px-2.5 py-1 text-xs font-medium text-blue-600 dark:text-blue-300">
            <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
            </svg>
            Preview Tool
        </div>

        {{-- Title --}}
        <h3 class="text-xl font-bold text-gray-800 md:text-2xl dark:text-white">
            Jump
        </h3>

        {{-- Tagline --}}
        <p class="mt-1 text-sm text-blue-600/80 dark:text-blue-200/80">
            Code here. Jump there.
        </p>

        {{-- Description --}}
        <p class="mt-3 flex-1 text-sm leading-relaxed text-gray-600 dark:text-slate-400">
            Preview your NativePHP app on real devices instantly.
        </p>

        {{-- Features list --}}
        <ul class="mt-3 space-y-1.5 text-xs text-gray-600 dark:text-slate-400">
            <li class="flex items-center gap-2">
                <svg class="size-3 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Works offline after download
            </li>
            <li class="flex items-center gap-2">
                <svg class="size-3 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                No Xcode or Android Studio
            </li>
            <li class="flex items-center gap-2">
                <svg class="size-3 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Free for local development
            </li>
        </ul>

        {{-- CTA --}}
        <div class="mt-4 flex items-center gap-2 text-sm font-medium text-blue-600 transition duration-300 group-hover:text-blue-500 dark:text-blue-400 dark:group-hover:text-blue-300">
            <span>Jump in</span>
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
