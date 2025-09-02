<div
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
    class="flex items-center pt-3.5 pb-3"
    aria-hidden="true"
>
    <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
    <div class="h-0.5 w-full bg-gray-200/90 dark:bg-[#242734]"></div>
    <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
</div>
