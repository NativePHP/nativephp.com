<x-layout title="NativePHP Ultra">
    {{-- Hero Section --}}
    <section
        class="mt-10 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center text-center">
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
                NativePHP Ultra
            </h1>

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
                Premium plugins, tools, and support to supercharge your
                NativePHP development.
            </h2>
        </header>
    </section>

    {{-- Pricing Section --}}
    <livewire:mobile-pricing />

    {{-- FAQ Section --}}
    <section
        class="mt-24"
        aria-labelledby="faq-heading"
    >
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
            <x-faq-card question="What is NativePHP Ultra?">
                <p>
                    NativePHP Ultra is a premium subscription that gives you
                    access to all first-party plugins, discounts on
                    third-party plugins, the Claude Code Plugin Dev Kit,
                    discounts on NativePHP courses and apps, Teams
                    support, and premium support with expedited
                    turnaround times.
                </p>
            </x-faq-card>

            <x-faq-card question="Do I need Ultra to use NativePHP?">
                <p>
                    No! NativePHP for Mobile is completely free. Ultra is
                    an optional premium subscription for developers who
                    want access to additional tools, plugins, and
                    priority support.
                </p>
            </x-faq-card>

            <x-faq-card question="What first-party plugins are included?">
                <p>
                    All first-party NativePHP plugins are included with
                    your Ultra subscription at no additional cost. As we
                    release new first-party plugins, they will be
                    automatically available to you.
                </p>
            </x-faq-card>

            <x-faq-card question="What is the Claude Code Plugin Dev Kit?">
                <p>
                    The Claude Code Plugin Dev Kit is a set of tools and
                    resources that help you build NativePHP plugins using
                    Claude Code. It's available for free to Ultra
                    subscribers.
                </p>
            </x-faq-card>

            <x-faq-card question="What discounts do I get on courses and apps?">
                <p>
                    Ultra subscribers get discounts on NativePHP courses
                    and apps. As we release new educational content and
                    tools, you'll automatically be eligible for
                    subscriber pricing.
                </p>
            </x-faq-card>

            <x-faq-card question="How does Teams support work?">
                <p>
                    Ultra includes Teams support, which lets you invite
                    other users into your team so they can share your
                    plugins and other Ultra benefits. As the account
                    owner, you can manage your team members and remove
                    access at any time.
                </p>
            </x-faq-card>

            <x-faq-card question="What does premium support include?">
                <p>
                    Premium support gives you expedited turnaround on
                    support issues. When you need help, your requests are
                    prioritized so you can get back to building faster.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I switch between monthly and annual billing?">
                <p>
                    You can manage your billing via the Stripe billing
                    portal. If you'd like to switch between monthly and
                    annual, you can cancel your current subscription and
                    start a new one on the billing interval you prefer.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I cancel my subscription?">
                <p>
                    Yes, you can cancel at any time. You'll continue to
                    have access to Ultra benefits until the end of your
                    current billing period.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I get an invoice?">
                <p>
                    Yes, invoices are sent automatically with your receipt
                    via email after each payment.
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
