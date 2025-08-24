<section
    class="mx-auto mt-20 max-w-5xl"
    aria-labelledby="laracon-talk-title"
    role="region"
>
    <div
        class="flex flex-col items-center gap-5 lg:flex-row lg:justify-between"
    >
        {{-- Left side --}}
        <div class="text-center lg:max-w-96 lg:text-left">
            <p
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
                class="inline-block rounded-full px-3 py-1 text-sm font-medium uppercase ring-1 ring-black dark:ring-white/15"
            >
                Laracon US Talk
            </p>
            <h2
                id="laracon-talk-title"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
                class="pt-2.5 text-xl font-medium capitalize opacity-0"
            >
                Where did this come from?
            </h2>

            <p
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
                class="pt-1.5 leading-relaxed text-gray-500 opacity-0 dark:text-gray-400"
            >
                Watch Marcel's original NativePHP talk from Laracon US 2023 in
                Nashville. Minds were blown as he demonstrated how to use
                Laravel to build cross-platform desktop applications.
            </p>
        </div>

        {{-- Right side --}}
        <div class="grid place-items-center">
            <a
                href="https://www.youtube.com/watch?v=iG7VscBFnqo"
                target="_blank"
                rel="noopener noreferrer"
                class="group relative"
                title="Marcel Pociot at Laracon US - Building Desktop Applications with PHP"
                aria-label="Watch Marcel Pociot's talk at Laracon US 2023"
            >
                {{-- Play button --}}
                <div
                    class="absolute top-1/2 right-1/2 grid size-16 translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/10 backdrop-blur-sm transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d]"
                    aria-hidden="true"
                >
                    <x-icons.play-button
                        x-init="
                                () => {
                                    motion.animate(
                                        $el,
                                        {
                                            x: [0, 3],
                                        },
                                        {
                                            duration: 0.6,
                                            repeat: Infinity,
                                            repeatType: 'mirror',
                                            ease: motion.easeInOut,
                                        },
                                    )
                                }
                            "
                        class="size-7"
                        aria-hidden="true"
                    />
                    <span class="sr-only">Play video</span>
                </div>
                {{-- Image --}}
                <img
                    src="{{ Vite::asset('resources/images/marcel2023laraconus.webp') }}"
                    alt="Marcel Pociot at Laracon US - Building Desktop Applications with PHP"
                    class="w-full max-w-[505px] rounded-2xl ring-1 ring-black/10"
                    width="505"
                    height="284"
                    loading="lazy"
                />
            </a>
        </div>
    </div>
</section>
