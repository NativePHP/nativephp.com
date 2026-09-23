<x-layout title="Build My App">
    @push('head')
        <style>html { scroll-behavior: smooth; }</style>
        @if (config('services.turnstile.site_key'))
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif
    @endpush

    <div class="mx-auto max-w-5xl">
        {{-- Hero --}}
        <section class="mt-12">
            <div class="text-center">
                <h1
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
                                        ease: motion.easeOut,
                                    },
                                )
                            })
                        }
                    "
                    class="text-4xl md:text-5xl"
                >
                    <span class="text-[#99ceb2] dark:text-indigo-500">{</span>
                    <span class="font-bold">Build My App</span>
                    <span class="text-[#99ceb2] dark:text-indigo-500">}</span>
                </h1>

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
                                        ease: motion.easeOut,
                                    },
                                )
                            })
                        }
                    "
                    class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 dark:text-zinc-400"
                >
                    Got an app idea? Let's build it together. The NativePHP core team partners with founders and businesses
                    to design, build, and ship cross-platform apps with PHP and Laravel.
                </p>
            </div>
        </section>

        {{-- What We Build --}}
        <section class="mt-20">
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [10, 0],
                                    opacity: [0, 1],
                                    scale: [0.9, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.1),
                                },
                            )
                        })
                    }
                "
                class="grid gap-6 md:grid-cols-3"
            >
                <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-[#1a1a2e]">
                    <div class="mx-auto grid size-12 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                        <x-icons.device-mobile-phone class="size-6" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold">Mobile Apps</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                        iOS and Android apps built with NativePHP for Mobile, ready for the App Store and Play Store.
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-[#1a1a2e]">
                    <div class="mx-auto grid size-12 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                        <x-icons.pc class="size-6" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold">Desktop Apps</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                        Native desktop apps for macOS, Windows, and Linux using NativePHP for Desktop.
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-[#1a1a2e]">
                    <div class="mx-auto grid size-12 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                        <x-heroicon-o-rocket-launch class="size-6" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold">End-to-End Delivery</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                        From idea and design through to launch, marketing, and ongoing iteration. We sweat the details.
                    </p>
                </div>
            </div>
        </section>

        {{-- Form --}}
        <section id="enquiry-form" class="mt-24 scroll-mt-24 pb-24">
            <h2
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="text-center text-3xl font-semibold"
            >
                Tell Us About Your App
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-center text-gray-600 dark:text-zinc-400">
                Share your idea and rough budget. We'll be in touch to plan the next steps.
            </p>

            <div class="mx-auto mt-8 max-w-2xl rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e] md:p-12">
                <livewire:lead-submission-form />
            </div>

            <p class="mx-auto mt-6 max-w-2xl text-center text-sm text-gray-500 dark:text-gray-400">
                Just need a quick technical session?
                <a href="{{ route('consulting') }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400">
                    Book a consulting slot
                </a>
                instead.
            </p>
        </section>
    </div>
</x-layout>
