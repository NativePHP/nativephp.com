<div {!! ! $licenseKey ? 'wire:poll.2s="loadData"' : '' !!}>
    {{-- Hero Section --}}
    <section
        class="mt-10 px-5 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
                x-init="
                    () => {
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
                    }
                "
                class="text-3xl font-extrabold sm:text-4xl"
            >
                You're In!
            </h1>

            {{-- Introduction Description --}}
            <h2
                x-init="
                    () => {
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
                    }
                "
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                We're excited to have you join the NativePHP community!
            </h2>
        </header>

        {{-- Success Card --}}
        <div
            class="mx-auto mt-10 max-w-xl"
            x-init="
                () => {
                    motion.animate(
                        $el,
                        {
                            opacity: [0, 1],
                            y: [20, 0],
                        },
                        {
                            duration: 0.7,
                            ease: motion.easeOut,
                        },
                    )
                }
            "
        >
            <div
                class="overflow-hidden rounded-xl bg-white p-6 shadow-lg dark:bg-mirage"
            >
                <div class="flex flex-col items-center gap-5 text-center">
                    <div
                        x-ref="checkmark"
                        class="relative grid size-10 place-items-center rounded-full bg-emerald-400 text-black opacity-0 ring-[9px] ring-emerald-400/20"
                        aria-hidden="true"
                        style="transform: none; opacity: 1"
                    >
                        <svg
                            class="size-8"
                            viewBox="0 0 16 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M12.466666666666665 4.800013333333333c-0.26666666666666666 -0.26666666666666666 -0.6666666666666666 -0.26666666666666666 -0.9333333333333332 0L6.533333333333333 9.799999999999999l-2.0666666666666664 -2.0666666666666664c-0.26666666666666666 -0.26666666666666666 -0.6666666666666666 -0.26666666666666666 -0.9333333333333332 0 -0.26666666666666666 0.26666666666666666 -0.26666666666666666 0.6666666666666666 0 0.9333333333333332l2.533333333333333 2.533333333333333c0.13333333333333333 0.13333333333333333 0.26666666666666666 0.19999999999999998 0.4666666666666666 0.19999999999999998 0.19999999999999998 0 0.3333333333333333 -0.06666666666666667 0.4666666666666666 -0.19999999999999998l5.466666666666666 -5.466653333333333c0.26666666666666666 -0.26666666666666666 0.26666666666666666 -0.6666666666666666 0 -0.9333333333333332Z"
                                fill="currentColor"
                                stroke-width="0.6667"
                            ></path>
                        </svg>

                        <div
                            class="absolute right-1/2 top-1/2 hidden size-24 -translate-y-1/2 translate-x-1/2 rounded-full bg-emerald-400/20 blur-2xl dark:block"
                            aria-hidden="true"
                        ></div>
                    </div>

                    <div class="space-y-1 dark:text-white">
                        <div
                            x-ref="success_title"
                            class="text-xl font-medium opacity-0"
                            style="transform: none; opacity: 1"
                        >
                            Payment Successful!
                        </div>
                        <div
                            x-ref="success_subtitle"
                            class="text-sm opacity-0"
                            style="transform: none; opacity: 0.5"
                        >
                            You've purchased a license.
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    @if ($licenseKey)
                        <p class="text-gray-600 dark:text-gray-400">
                            License key
                        </p>
                        <div
                            class="relative mt-2 rounded-md bg-gray-200 p-4 dark:bg-gray-800"
                            x-data="{
                                showMessage: false,
                                copyToClipboard() {
                                    navigator.clipboard
                                        .writeText(
                                            this.$root.querySelector('.copy-license').textContent.trim(),
                                        )
                                        .then(() => (this.showMessage = true))

                                    setTimeout(() => (this.showMessage = false), 2000)
                                },
                            }"
                        >
                            <div
                                class="absolute right-2 top-3 flex items-center space-x-2"
                            >
                                <div
                                    x-show="showMessage"
                                    x-transition
                                    class="py-1 font-bold text-indigo-400 transition duration-300"
                                    style="display: none"
                                >
                                    Copied!
                                </div>
                                <button
                                    type="button"
                                    title="Copy to clipboard"
                                    class="bg-gray-200 p-1 transition duration-300 dark:bg-gray-800"
                                    @click.prevent="copyToClipboard"
                                    :class="{
                                        'text-black/50 hover:text-black/80 dark:text-white/20 dark:hover:text-white/60': !showMessage,
                                        'text-indigo-400 hover:text-indigo-400': showMessage,
                                    }"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="block size-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                            <pre
                                class="mt-0 overflow-clip pt-0"
                            ><code class="copy-license">{{ $licenseKey }}</code></pre>
                        </div>
                        <p
                            class="mt-2 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Store this somewhere safe. You'll need it later.
                        </p>
                        @if ($email)
                            <p class="mt-6 text-gray-600 dark:text-gray-400">
                                Email
                            </p>
                            <div
                                class="relative mt-2 rounded-md bg-gray-200 p-4 dark:bg-gray-800"
                                x-data="{
                                    showMessage: false,
                                    copyToClipboard() {
                                        navigator.clipboard
                                            .writeText(
                                                this.$root.querySelector('.copy-email').textContent.trim(),
                                            )
                                            .then(() => (this.showMessage = true))
                                        setTimeout(() => (this.showMessage = false), 2000)
                                    },
                                }"
                            >
                                <div
                                    class="absolute right-2 top-3 flex items-center space-x-2"
                                >
                                    <div
                                        x-show="showMessage"
                                        x-transition
                                        class="py-1 font-bold text-indigo-400 transition duration-300"
                                        style="display: none"
                                    >
                                        Copied!
                                    </div>
                                    <button
                                        type="button"
                                        title="Copy to clipboard"
                                        class="bg-gray-200 p-1 transition duration-300 dark:bg-gray-800"
                                        @click.prevent="copyToClipboard"
                                        :class="{
                                        'text-black/50 hover:text-black/80 dark:text-white/20 dark:hover:text-white/60': !showMessage,
                                        'text-indigo-400 hover:text-indigo-400': showMessage,
                                    }"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="block size-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                                <pre
                                    class="mt-0 overflow-clip pt-0"
                                ><code class="copy-email">{{ $email }}</code></pre>
                            </div>
                        @endif
                    @else
                        <div
                            class="mt-6 flex items-center justify-center gap-2"
                        >
                            <div
                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"
                            ></div>
                            <div
                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"
                                style="animation-delay: 0.2s"
                            ></div>
                            <div
                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"
                                style="animation-delay: 0.4s"
                            ></div>
                            <span
                                class="ml-2 text-lg text-gray-600 dark:text-gray-400"
                            >
                                License registration in progress
                            </span>
                        </div>
                        <p
                            class="mt-6 text-center text-gray-600 dark:text-gray-400"
                        >
                            Please
                            <span class="font-medium dark:text-gray-300">
                                check your email
                            </span>
                            shortly for a copy of your license key. This page
                            will also update if your license key is ready.
                        </p>

                        <p
                            class="mt-2 text-center text-gray-600 dark:text-gray-400"
                        >
                            Once you receive your license key, you can start
                            building amazing mobile apps with NativePHP!
                        </p>
                    @endif
                </div>

                <a
                    href="/docs/mobile/1/getting-started/installation"
                    class="mt-10 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-900 dark:bg-[#d68ffe] dark:text-black dark:hover:bg-[#e1acff]"
                >
                    <div class="flex items-center justify-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"
                            ></path>
                            <path
                                d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"
                            ></path>
                        </svg>
                        <span>View Installation Guide</span>
                    </div>
                </a>

                @if ($subscription === \App\Enums\Subscription::Max)
                    <div
                        class="mt-6 rounded-xl bg-indigo-50 p-4 dark:bg-indigo-900/20"
                    >
                        <div class="flex">
                            <div class="shrink-0">
                                <svg
                                    class="h-5 w-5 text-indigo-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-indigo-800 dark:text-indigo-300"
                                >
                                    Repo Access
                                </h3>
                                <div
                                    class="mt-2 text-sm text-indigo-700 dark:text-indigo-200"
                                >
                                    <p>
                                        As a Max subscriber, you have access to
                                        the NativePHP/mobile repository. To
                                        access it, please log in to
                                        <!-- display: inline -->
                                        <a
                                            href="https://auth.anystack.sh/?accountType=customer"
                                            class="font-medium underline hover:text-indigo-600 dark:hover:text-indigo-100"
                                            target="_blank"
                                            >AnyStack.sh</a
                                        >
                                        using the same email address you used
                                        for your purchase.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Next Steps --}}
        <div class="mx-auto mt-16 max-w-2xl">
            <h3
                class="text-center text-2xl font-semibold"
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.7,
                                delay: 0.3,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
            >
                What's Next?
            </h3>

            <div
                class="mt-8 grid gap-6 md:grid-cols-2"
                x-init="
                    () => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                opacity: [0, 1],
                                y: [20, 0],
                            },
                            {
                                duration: 0.5,
                                delay: motion.stagger(0.1, { start: 0.4 }),
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
            >
                <a
                    href="/docs/mobile/1/getting-started/installation"
                    class="rounded-2xl bg-gray-100 p-6 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
                >
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-6 text-violet-600 dark:text-violet-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium">Install the Package</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Follow our step-by-step guide to install and set up
                        NativePHP in your Laravel project.
                    </p>
                </a>

                <a
                    href="https://discord.gg/X62tWNStZK"
                    class="rounded-2xl bg-gray-100 p-6 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
                >
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-6 text-indigo-600 dark:text-indigo-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                            ></path>
                            <circle
                                cx="9"
                                cy="7"
                                r="4"
                            ></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium">Join Our Community</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Connect with other developers, get help, and share your
                        experiences in our Discord community.
                    </p>
                </a>

                <div
                    class="rounded-2xl bg-gray-100 p-6 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
                >
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-6 text-emerald-600 dark:text-emerald-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline
                                points="22 12 18 12 15 21 9 3 6 12 2 12"
                            ></polyline>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium">Build Your First App</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Follow our tutorials to create your first mobile app
                        using PHP and Laravel. Coming soon.
                    </p>
                </div>

                @if ($subscription === \App\Enums\Subscription::Max)
                    <div
                        class="rounded-2xl bg-gray-100 p-6 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
                    >
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-full"
                        >
                            <x-icons.github class="dark:fill-white" />
                        </div>
                        <h4 class="text-lg font-medium">Access the Repo</h4>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            <a
                                href="https://auth.anystack.sh/register?accountType=customer"
                                target="_blank"
                                class="text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300"
                            >
                                Create a customer account
                            </a>
                            on Anystack to gain access to the NativePHP for
                            Mobile
                            <a
                                href="https://github.com/nativephp/mobile/issues"
                                target="_blank"
                                class="text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300"
                            >
                                repository
                            </a>
                            where you can let us know if you find any bugs as
                            you build.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
