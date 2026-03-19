{{-- Native Features Grid Component --}}
<section
    class="mt-20"
    aria-labelledby="features-heading"
>
    <header class="text-center">
        <h2
            id="features-heading"
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
            class="text-3xl font-semibold"
        >
            Native Features Built In
        </h2>
        <p
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
            class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400"
        >
            Access powerful device capabilities with simple PHP facades
        </p>
    </header>

    <div
        x-init="
            () => {
                motion.inView($el, (element) => {
                    motion.animate(
                        Array.from($el.children),
                        {
                            y: [20, 0],
                            opacity: [0, 1],
                            scale: [0.95, 1],
                        },
                        {
                            duration: 0.5,
                            ease: motion.backOut,
                            delay: motion.stagger(0.05),
                        },
                    )
                })
            }
        "
        class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
    >
        {{-- Biometrics --}}
        <a
            href="/docs/mobile/apis/biometrics"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M12 3.75a6.715 6.715 0 0 0-3.722 1.118.75.75 0 1 1-.828-1.25 8.25 8.25 0 0 1 12.8 6.883c0 3.014-.574 5.897-1.62 8.543a.75.75 0 0 1-1.395-.551A21.69 21.69 0 0 0 18.75 10.5 6.75 6.75 0 0 0 12 3.75ZM6.157 5.739a.75.75 0 0 1 .21 1.04A6.715 6.715 0 0 0 5.25 10.5c0 1.613-.463 3.12-1.265 4.393a.75.75 0 0 1-1.27-.8A6.715 6.715 0 0 0 3.75 10.5c0-1.68.503-3.246 1.367-4.55a.75.75 0 0 1 1.04-.211ZM12 7.5a3 3 0 0 0-3 3c0 3.1-1.176 5.927-3.105 8.056a.75.75 0 1 1-1.112-1.008A10.459 10.459 0 0 0 7.5 10.5a4.5 4.5 0 1 1 9 0c0 .547-.022 1.09-.067 1.626a.75.75 0 0 1-1.495-.123c.041-.495.062-.996.062-1.503a3 3 0 0 0-3-3Zm0 2.25a.75.75 0 0 1 .75.75A15.69 15.69 0 0 1 8.97 20.738a.75.75 0 0 1-1.14-.975A14.19 14.19 0 0 0 11.25 10.5a.75.75 0 0 1 .75-.75Zm3.239 5.183a.75.75 0 0 1 .515.927 19.417 19.417 0 0 1-2.585 5.544.75.75 0 0 1-1.243-.84 17.915 17.915 0 0 0 2.386-5.116.75.75 0 0 1 .927-.515Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Biometrics</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Face ID & Touch ID</div>
                </div>
            </div>
        </a>

        {{-- Camera --}}
        <a
            href="/docs/mobile/apis/camera"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                        <path fill-rule="evenodd" d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3H4.5a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Camera</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Photos & gallery</div>
                </div>
            </div>
        </a>

        {{-- Push Notifications --}}
        <a
            href="/docs/mobile/apis/push-notifications"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Push Notifications</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Firebase Cloud Messaging</div>
                </div>
            </div>
        </a>

        {{-- Geolocation --}}
        <a
            href="/docs/mobile/apis/geolocation"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="m11.54 22.351.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Geolocation</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">GPS & location services</div>
                </div>
            </div>
        </a>

        {{-- Secure Storage --}}
        <a
            href="/docs/mobile/apis/secure-storage"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Secure Storage</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Keychain & keystore</div>
                </div>
            </div>
        </a>

        {{-- Haptics --}}
        <a
            href="/docs/mobile/apis/haptics"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path d="M16.5 6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v7.5a3 3 0 0 0 3 3v-6A4.5 4.5 0 0 1 10.5 6h6Z" />
                        <path d="M18 7.5a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3h-7.5a3 3 0 0 1-3-3v-7.5a3 3 0 0 1 3-3H18Z" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Haptics</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Vibration feedback</div>
                </div>
            </div>
        </a>

        {{-- Native Dialogs --}}
        <a
            href="/docs/mobile/apis/dialog"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 0 1-3.476.383.39.39 0 0 0-.297.17l-2.755 4.133a.75.75 0 0 1-1.248 0l-2.755-4.133a.39.39 0 0 0-.297-.17 48.9 48.9 0 0 1-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97ZM6.75 8.25a.75.75 0 0 1 .75-.75h9a.75.75 0 0 1 0 1.5h-9a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H7.5Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Native Dialogs</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Alerts, toasts & share</div>
                </div>
            </div>
        </a>

        {{-- Deep Links --}}
        <a
            href="/docs/mobile/concepts/deep-links"
            class="block rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200 transition duration-200 hover:bg-gray-100 hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:bg-gray-800 dark:hover:ring-gray-700"
        >
            <div class="flex items-center gap-3">
                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M19.902 4.098a3.75 3.75 0 0 0-5.304 0l-4.5 4.5a3.75 3.75 0 0 0 1.035 6.037.75.75 0 0 1-.646 1.353 5.25 5.25 0 0 1-1.449-8.45l4.5-4.5a5.25 5.25 0 1 1 7.424 7.424l-1.757 1.757a.75.75 0 1 1-1.06-1.06l1.757-1.757a3.75 3.75 0 0 0 0-5.304Zm-7.389 4.267a.75.75 0 0 1 1-.353 5.25 5.25 0 0 1 1.449 8.45l-4.5 4.5a5.25 5.25 0 1 1-7.424-7.424l1.757-1.757a.75.75 0 1 1 1.06 1.06l-1.757 1.757a3.75 3.75 0 1 0 5.304 5.304l4.5-4.5a3.75 3.75 0 0 0-1.035-6.037.75.75 0 0 1-.354-1Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">Deep Links</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">URL schemes & universal links</div>
                </div>
            </div>
        </a>
    </div>

    <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        All accessible via simple PHP facades like
        <code class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-emerald-600 dark:bg-gray-800 dark:text-emerald-400">Biometrics::prompt()</code>
    </p>
</section>
