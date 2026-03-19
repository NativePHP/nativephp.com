@props([
    'small' => false,
])

<a
    href="https://bifrost.nativephp.com/"
    x-data="{ tl: null, items: [] }"
    x-init="
        $nextTick(() => {
            this.items = Array.from($refs.textContainer.children);
            if (this.items.length > 1) {
                gsap.set(this.items, { autoAlpha: 0, y: 10 });
                gsap.set(this.items[0], { autoAlpha: 1, y: 0 });

                const hold = 0.7;
                this.tl = gsap.timeline({ repeat: -1 });

                for (let i = 0; i < this.items.length; i++) {
                    const curr = this.items[i];
                    const next = this.items[(i + 1) % this.items.length];

                    this.tl.to(curr, { duration: 0.5, autoAlpha: 0, y: -10, ease: 'circ.inOut' }, `+=${hold}`)
                           .to(next, { duration: 0.5, autoAlpha: 1, y: 0, ease: 'circ.inOut' }, '<');
                }
            }
        })
    "
    @mouseenter="
        if (tl) {
            tl.pause();
            gsap.to(items, { autoAlpha: 0, y: -10, duration: 0.2 });
            gsap.to(items[0], { autoAlpha: 1, y: 0, duration: 0.2 });
        }
    "
    @mouseleave="
        if (tl) {
            tl.restart();
        }
    "
    @class([
        'group relative z-0 inline-flex items-center overflow-hidden rounded-full bg-gray-200 transition duration-200 will-change-transform hover:scale-x-105 dark:bg-slate-800',

        'px-4 py-2 text-sm' => $small,

        'px-6 py-3' => ! $small,
    ])
>
    <div
        class="@container absolute inset-0 flex items-center"
        aria-hidden="true"
    >
        <div
            class="absolute h-[100cqw] w-[100cqw] scale-110 animate-[spin_3s_linear_infinite] bg-[conic-gradient(from_0_at_50%_50%,--alpha(var(--color-blue-400)/70%)_0deg,--alpha(var(--color-indigo-400)/70%)_40deg,--alpha(var(--color-orange-300)/70%)_80deg,transparent_110deg,transparent_250deg,--alpha(var(--color-rose-400)/50%)_280deg,--alpha(var(--color-fuchsia-400)/50%)_320deg,--alpha(var(--color-blue-400)/70%)_360deg)] transition duration-300 will-change-transform group-hover:animate-[spin_1s_linear_infinite] dark:bg-[conic-gradient(from_0_at_50%_50%,--alpha(var(--color-blue-400)/70%)_0deg,--alpha(var(--color-indigo-400)/70%)_40deg,--alpha(var(--color-teal-400)/70%)_80deg,transparent_110deg,transparent_250deg,--alpha(var(--color-rose-400)/50%)_280deg,--alpha(var(--color-fuchsia-400)/50%)_320deg,--alpha(var(--color-blue-400)/70%)_360deg)]"
        ></div>
    </div>
    <div
        class="absolute inset-0.5 rounded-full bg-white dark:bg-slate-950"
        aria-hidden="true"
    ></div>
    <div
        class="absolute bottom-0 left-1/2 h-1/3 w-4/5 -translate-x-1/2 rounded-full bg-indigo-400/15 opacity-50 blur-md transition-all duration-500 group-hover:h-2/3 group-hover:opacity-100"
        aria-hidden="true"
    ></div>
    <span
        x-ref="textContainer"
        class="inline-grid font-medium"
    >
        <span
            class="self-center justify-self-center whitespace-nowrap [grid-area:1/-1]"
        >
            Try Bifrost!
        </span>
        <span
            class="self-center justify-self-center whitespace-nowrap [grid-area:1/-1]"
        >
            Build
        </span>
        <span
            class="self-center justify-self-center whitespace-nowrap [grid-area:1/-1]"
        >
            Distribute
        </span>
        <span
            class="self-center justify-self-center whitespace-nowrap [grid-area:1/-1]"
        >
            {{ $small ? 'Ship' : 'Ship it!' }}
        </span>
    </span>
</a>
