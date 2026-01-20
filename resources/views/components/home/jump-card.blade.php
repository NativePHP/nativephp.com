{{-- Jump Card - Preview on real devices --}}
<a
    href="https://bifrost.nativephp.com/jump"
    target="_blank"
    rel="noopener noreferrer"
    class="group relative block h-full overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/20 via-cyan-500/10 to-indigo-500/20 p-0.5 ring-1 ring-blue-500/30 transition duration-300 hover:ring-blue-400/50"
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
    <div class="relative flex h-full flex-col overflow-hidden rounded-xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-5 md:p-6">
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

        {{-- Badge --}}
        <div class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-blue-500/20 px-2.5 py-1 text-xs font-medium text-blue-300">
            <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.5L6 14M12 18.5L18 14M12 18.5V12M12 12L6 7.5M12 12L18 7.5M12 5.5V12" />
            </svg>
            Preview Tool
        </div>

        {{-- Title --}}
        <h3 class="text-xl font-bold text-white md:text-2xl">
            Jump
        </h3>

        {{-- Tagline --}}
        <p class="mt-1 text-sm text-blue-200/80">
            Code here. Jump there.
        </p>

        {{-- Description --}}
        <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-400">
            Preview your NativePHP app on real devices instantly. Just scan a QR code.
        </p>

        {{-- Platform badges --}}
        <div class="mt-3 flex items-center gap-3 text-xs text-slate-500">
            <span class="flex items-center gap-1.5 rounded-full bg-slate-800/50 px-2.5 py-1 ring-1 ring-slate-700/50">
                <svg class="size-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                </svg>
                iOS
            </span>
            <span class="flex items-center gap-1.5 rounded-full bg-slate-800/50 px-2.5 py-1 ring-1 ring-slate-700/50">
                <svg class="size-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.523 15.341c-.5 0-.919.168-1.258.504-.338.336-.508.756-.508 1.258v2.795c0 .502.17.922.508 1.258s.758.504 1.258.504c.501 0 .92-.168 1.258-.504.339-.336.508-.756.508-1.258v-2.795c0-.502-.17-.922-.508-1.258-.338-.336-.757-.504-1.258-.504zm-11.046 0c-.5 0-.919.168-1.258.504-.338.336-.508.756-.508 1.258v2.795c0 .502.17.922.508 1.258s.758.504 1.258.504c.501 0 .92-.168 1.258-.504.339-.336.508-.756.508-1.258v-2.795c0-.502-.169-.922-.508-1.258-.338-.336-.757-.504-1.258-.504zM5.519 7.05c-.168.336-.252.714-.252 1.134v7.774c0 .502.169.922.508 1.258.338.336.757.504 1.258.504h.756v2.523c0 .502.17.922.508 1.258.339.336.758.504 1.258.504.501 0 .92-.168 1.259-.504.338-.336.507-.756.507-1.258V17.72h1.26v2.523c0 .502.169.922.508 1.258.338.336.757.504 1.258.504s.92-.168 1.258-.504c.339-.336.508-.756.508-1.258V17.72h.756c.501 0 .92-.168 1.258-.504.339-.336.508-.756.508-1.258V8.184c0-.42-.084-.798-.252-1.134H5.519zm6.481-4.79c.126 0 .231.042.315.126.084.084.126.189.126.315s-.042.231-.126.315c-.084.084-.189.126-.315.126s-.231-.042-.315-.126c-.084-.084-.126-.189-.126-.315s.042-.231.126-.315c.084-.084.189-.126.315-.126zM9.477 2.26c.126 0 .231.042.315.126.084.084.126.189.126.315s-.042.231-.126.315c-.084.084-.189.126-.315.126s-.231-.042-.315-.126c-.084-.084-.126-.189-.126-.315s.042-.231.126-.315c.084-.084.189-.126.315-.126zm4.172.882l.882-1.512c.042-.084.021-.147-.063-.189-.084-.042-.147-.021-.189.063l-.882 1.512c-.756-.336-1.554-.504-2.397-.504s-1.641.168-2.397.504L7.721 1.504c-.042-.084-.105-.105-.189-.063-.084.042-.105.105-.063.189l.882 1.512c-.84.42-1.512 1.008-2.016 1.764-.504.756-.756 1.596-.756 2.52h11.844c0-.924-.252-1.764-.756-2.52-.504-.756-1.176-1.344-2.016-1.764z"/>
                </svg>
                Android
            </span>
        </div>

        {{-- Features list --}}
        <ul class="mt-3 space-y-1.5 text-xs text-slate-400">
            <li class="flex items-center gap-2">
                <svg class="size-3 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Works offline after download
            </li>
            <li class="flex items-center gap-2">
                <svg class="size-3 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                No Xcode or Android Studio
            </li>
            <li class="flex items-center gap-2">
                <svg class="size-3 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Free for local development
            </li>
        </ul>

        {{-- CTA --}}
        <div class="mt-4 flex items-center gap-2 text-sm font-medium text-blue-400 transition duration-300 group-hover:text-blue-300">
            <span>Jump In</span>
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
