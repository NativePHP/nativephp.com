{{-- Mimi Card - AI-powered app creation --}}
<a
    href="https://bifrost.nativephp.com/mimi"
    target="_blank"
    rel="noopener noreferrer"
    class="group relative block h-full overflow-hidden rounded-2xl bg-gradient-to-br from-pink-500/20 via-fuchsia-500/10 to-violet-500/20 p-0.5 ring-1 ring-pink-500/30 transition duration-300 hover:ring-pink-400/50"
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
    <div class="relative flex h-full flex-col overflow-hidden rounded-xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-5 md:p-6">
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
            class="pointer-events-none absolute -right-10 -top-10 size-32 rounded-full bg-pink-500/20 blur-[40px] transition duration-500 group-hover:bg-pink-500/30"
            aria-hidden="true"
        ></div>

        {{-- Badge --}}
        <div class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-pink-500/20 px-2.5 py-1 text-xs font-medium text-pink-300">
            <span
                x-init="
                    () => {
                        gsap.to($el, {
                            scale: 1.3,
                            duration: 0.6,
                            repeat: -1,
                            yoyo: true,
                            ease: 'power1.inOut',
                        })
                    }
                "
                class="inline-block"
            >
                <svg class="size-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3L13.5 8.5L19 10L13.5 11.5L12 17L10.5 11.5L5 10L10.5 8.5L12 3Z"/>
                </svg>
            </span>
            AI-Powered
        </div>

        {{-- Title --}}
        <h3 class="text-xl font-bold text-white md:text-2xl">
            Mimi
        </h3>

        {{-- Tagline --}}
        <p class="mt-1 text-sm text-pink-200/80">
            Describe it. Build it.
        </p>

        {{-- Description --}}
        <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-400">
            Turn your ideas into native mobile apps with AI. Just describe what you want.
        </p>

        {{-- Feature highlights --}}
        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-slate-500">
            <span class="flex items-center gap-1">
                <svg class="size-3 text-pink-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3L13.5 8.5L19 10L13.5 11.5L12 17L10.5 11.5L5 10L10.5 8.5L12 3Z"/>
                </svg>
                AI-Powered
            </span>
            <span class="size-1 rounded-full bg-slate-700"></span>
            <span class="flex items-center gap-1">
                <svg class="size-3 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Instant Preview
            </span>
            <span class="size-1 rounded-full bg-slate-700"></span>
            <span class="flex items-center gap-1">
                <svg class="size-3 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                    <line x1="12" y1="18" x2="12" y2="18"/>
                </svg>
                Native Apps
            </span>
        </div>

        {{-- Tech stack --}}
        <div class="mt-3 flex flex-wrap gap-1.5">
            <span class="rounded-md bg-slate-800/60 px-2 py-1 text-xs text-slate-400 ring-1 ring-slate-700/50">Laravel</span>
            <span class="rounded-md bg-slate-800/60 px-2 py-1 text-xs text-slate-400 ring-1 ring-slate-700/50">Livewire</span>
            <span class="rounded-md bg-slate-800/60 px-2 py-1 text-xs text-slate-400 ring-1 ring-slate-700/50">Tailwind</span>
            <span class="rounded-md bg-slate-800/60 px-2 py-1 text-xs text-slate-400 ring-1 ring-slate-700/50">NativePHP</span>
        </div>

        {{-- Features list --}}
        <ul class="mt-3 space-y-1.5 text-xs text-slate-400">
            <li class="flex items-center gap-2">
                <svg class="size-3 text-pink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Real-time code generation
            </li>
            <li class="flex items-center gap-2">
                <svg class="size-3 text-pink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Export to GitHub anytime
            </li>
        </ul>

        {{-- CTA --}}
        <div class="mt-4 flex items-center gap-2 text-sm font-medium text-pink-400 transition duration-300 group-hover:text-pink-300">
            <span>Start creating</span>
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
