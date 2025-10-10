<x-layout title="NativePHP for iOS and Android">
    {{-- Hero Section --}}
    <section
        class="mt-10 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="text-3xl font-extrabold sm:text-4xl"
            >
                Discounted Licenses
            </h1>

            {{-- Introduction Description --}}
            <h2
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
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                Thanks for supporting NativePHP and Bifrost.<br>
                Now go get your discounted license!
            </h2>
        </header>
    </section>

    {{-- Pricing Section --}}
    <livewire:mobile-pricing :discounted="true" />

    {{-- Ultra Section --}}
    <x-ultra-plan />

    {{-- Testimonials Section --}}
    {{-- <x-testimonials /> --}}

    {{-- FAQ Section --}}
    <section
        class="mt-24"
        aria-labelledby="faq-heading"
    >
        {{-- Section Heading --}}
        <h2
            id="faq-heading"
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [-10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="text-center text-3xl font-semibold opacity-0"
        >
            Frequently Asked Questions
        </h2>

        {{-- FAQ List --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                x: [-50, 0],
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                                delay: motion.stagger(0.1),
                            },
                        )
                    })
                }
            "
            class="mx-auto flex w-full max-w-2xl flex-col items-center gap-4 pt-10"
            aria-labelledby="faq-heading"
        >
            <x-faq-card question="Are these discounted licenses different somehow? What's the catch?">
                <p>
                    No catch! They're the same licenses.
                </p>
            </x-faq-card>

            <x-faq-card
                question="When my discounted license renews, will it renew at the same price or go up to the regular price?"
            >
                <p>
                    It'll renew at the <em>discounted</em> price. As long as you keep up your subscription, you'll
                    benefit from that discounted price.
                </p>
            </x-faq-card>

            <x-faq-card
                question="Can I still build apps if I choose not to renew my license?"
            >
                <p>
                    Yes. Renewing your license entitles you to receive the
                    latest package updates but isn't required to build and
                    release apps.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I upgrade or downgrade my license later?">
                <p>That's not currently possible.</p>
            </x-faq-card>

            <x-faq-card question="Can I use NativePHP for commercial projects?">
                <p>
                    Absolutely! You can use NativePHP for any kind of project,
                    including commercial ones. We can't wait to see what you
                    build!
                </p>
            </x-faq-card>

            <x-faq-card question="Can I get an invoice?">
                <p>
                    You'll get an invoice with your receipt via email and you can always retrieve past invoices
                    in the
                    <a
                        href="https://billing.stripe.com/p/login/4gwaGV5VK0uU44E288"
                        onclick="event.stopPropagation()"
                        class="inline-block underline hover:text-violet-400"
                        aria-label="Stripe billing portal"
                        target="_blank"
                    >
                        Stripe billing portal.
                    </a>
                </p>
            </x-faq-card>

            <x-faq-card question="How can I manage my subscription?">
                <p>
                    You can manage your subscription via the
                    <a
                        href="https://billing.stripe.com/p/login/4gwaGV5VK0uU44E288"
                        onclick="event.stopPropagation()"
                        class="inline-block underline hover:text-violet-400"
                        aria-label="Stripe billing portal"
                        target="_blank"
                    >
                        Stripe billing portal.
                    </a>
                </p>
            </x-faq-card>
        </div>
    </section>
</x-layout>
