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
        class="dark:bg-mirage space-y-3 rounded-2xl bg-gray-100 p-7"
    >
        {{-- Plan Name --}}
        <h3
            id="ultra-tier-heading"
            class="text-3xl font-semibold flex gap-4 items-center"
        >
            <span class="rounded-full bg-zinc-300 dark:bg-zinc-600 px-4 py-1">
                Ultra
            </span>
            <span class="text-zinc-600 dark:text-zinc-300 text-xl font-normal">Partners get even more!</span>
        </h3>

        <p class="dark:text-gray-400">
            <span class="font-semibold">Ultra</span> is our Partnership Program, offering
            <span class="font-medium">dedicated support</span>, <span class="font-medium">feature development</span>,
            <span class="font-medium">training</span>, <span class="font-medium">marketing opportunities</span>
            and other enterprise-oriented services for teams of any size.
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
