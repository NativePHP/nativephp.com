<section
    class="mx-auto mt-10 max-w-2xl"
    aria-labelledby="ultra-tier-heading"
>
    <div
        x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
        class="dark:bg-mirage space-y-3 rounded-2xl bg-gray-100 p-7 text-center"
    >
        {{-- Plan Name --}}
        <h3
            id="ultra-tier-heading"
            class="text-2xl font-semibold"
        >
            Ultra
        </h3>

        {{-- Price --}}
        {{-- <div --}}
        {{-- class="flex items-start gap-1.5 pt-5" --}}
        {{-- aria-label="Price: $10,000+ per year" --}}
        {{-- > --}}
        {{-- <div class="text-5xl font-semibold">$20,000</div> --}}
        {{-- <div class="self-end pb-1.5 text-zinc-500">per year</div> --}}
        {{-- </div> --}}

        <p class="dark:text-gray-400">
            Partners get even more! <b>Ultra</b> is our Partnership Program, offering dedicated support,
            feature development, training, license management, marketing opportunities, and other
            enterprise-oriented services for teams of any size.
        </p>
        <div>
            <a
                href="{{ route('partners') }}"
                class="mx-auto mt-5 block w-full max-w-xs rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
            >
                Learn more
            </a>
        </div>
    </div>
</section>
