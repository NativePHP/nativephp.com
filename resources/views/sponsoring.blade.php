<x-layout title="Sponsoring">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl px-5 md:mt-14"
        aria-labelledby="article-title"
    >
        <header class="relative grid place-items-center text-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute right-1/2 top-0 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-slate-500/50"
                aria-hidden="true"
            ></div>

            {{-- Primary Heading --}}
            <h1
                id="article-title"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-5, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mt-8 text-3xl font-extrabold will-change-transform sm:text-4xl"
            >
                Support NativePHP
            </h1>
        </header>

        {{-- Divider --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [5, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="flex items-center pb-3 pt-3.5 will-change-transform"
            aria-hidden="true"
        >
            <div
                class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"
            ></div>
            <div class="h-0.5 w-full bg-gray-200/90 dark:bg-[#242734]"></div>
            <div
                class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"
            ></div>
        </div>

        {{-- Content --}}
        <article
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [5, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="prose mt-2 max-w-none text-gray-600 will-change-transform dark:text-gray-400 dark:prose-headings:text-white"
            aria-labelledby="article-title"
        >
            <p>
                NativePHP is an organization backed by a team of dedicated employees, maintainers, and contributors who commit their time to
                ensure its continued development and improvement. Currently, we operate two major projects: NativePHP for Mobile and
                NativePHP for Desktop. Together, we can continue to grow NativePHP and ensure it remains a valuable tool for the community.
            </p>

            <h2>NativePHP for Mobile</h2>

            <p>
                If you're interested in NativePHP for Mobile, you can <a href="/mobile">purchase a license</a>, which is the best way you can
                support both projects of the NativePHP organization. These funds are used to cover the costs of development, infrastructure,
                and community initiatives for both projects.
            </p>

            <h2>NativePHP for Desktop</h2>

            <p>
                NativePHP for Desktop is a free, open source project available under an MIT license. We encourage and appreciate any
                contributions to the project, whether it's through code, documentation, spreading the word, or a financial sponsorship.
                We provide the following ways of making an easy financial contribution:
            </p>

            <ul>
                <li><a href="https://opencollective.com/nativephp">OpenCollective</a></li>
            </ul>

            <p>
                All contributions are welcome, at any amount, as a one-off payment or on a recurring schedule. These funds are used to
                support the maintainers of the Desktop project.
            </p>

            <p>
                All monthly sponsors above $10/month will be bestowed the <b>Sponsor</b> role on the NativePHP
                <a href="https://discord.gg/X62tWNStZK">Discord</a>, granting access to private channels, early access to new releases, and
                discounts on future premium services.
            </p>

            <h2>Corporate Partners</h2>

            <p>
                If your organization is using NativePHP, we strongly encourage you to consider a Corporate Sponsorship. This level of
                support will provide your team with the added benefits of increased levels of support, hands-on help directly from the
                maintainers of NativePHP and promotion of your brand as a supporter of cutting-edge open source work.
            </p>

            <p>
                For more details, please view our <a href="/partners">partners page</a> or email us at
                <a href="mailto:partners@nativephp.com?subject=Corporate%20Sponsorship">partners@nativephp.com</a>.
            </p>
        </article>
    </section>
</x-layout>
