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
    {{-- Sponsors --}}
    <h3 class="flex items-center gap-1.5 opacity-60">
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
