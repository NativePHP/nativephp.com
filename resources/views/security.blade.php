<x-layout title="Security">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl md:mt-14"
        aria-labelledby="article-title"
    >
        <header class="relative grid place-items-center text-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-slate-500/50"
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
                Security Policy
            </h1>

            {{-- Subtitle --}}
            <p class="max-w-xl pt-4 text-gray-600 dark:text-gray-400">
                How to report a security vulnerability in NativePHP, Bifrost or
                a plugin.
            </p>
        </header>

        {{-- Divider --}}
        <x-divider />

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
            <div
                class="not-prose rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200"
                role="note"
            >
                <p class="font-semibold">
                    Please don't disclose security issues publicly.
                </p>
                <p class="mt-1 text-sm">
                    Don't open a public issue, pull request or discussion for a
                    vulnerability. Report it privately using one of the options
                    below.
                </p>
            </div>

            <p>
                All security vulnerabilities will be promptly addressed. Where
                you report one depends on what it affects.
            </p>

            <h2>Open source packages</h2>

            <p>
                For NativePHP's open source packages, use GitHub's private
                <strong>Report a vulnerability</strong>
                form on the package's repository. Only the maintainers can see
                what you send, and we can work on a fix with you there before
                anything is made public.
            </p>

            @php
                $packageGroups = [
                    'Core' => [
                        'nativephp/mobile' => 'mobile-air',
                        'nativephp/desktop' => 'desktop',
                    ],
                    'Mobile plugins' => [
                        'nativephp/mobile-browser' => 'mobile-browser',
                        'nativephp/mobile-camera' => 'mobile-camera',
                        'nativephp/mobile-clipboard' => 'mobile-clipboard',
                        'nativephp/mobile-device' => 'mobile-device',
                        'nativephp/mobile-dialog' => 'mobile-dialog',
                        'nativephp/mobile-dynamic-notifications' => 'mobile-dynamic-notifications',
                        'nativephp/mobile-file' => 'mobile-file',
                        'nativephp/mobile-media-player' => 'mobile-media-player',
                        'nativephp/mobile-microphone' => 'mobile-microphone',
                        'nativephp/mobile-network' => 'mobile-network',
                        'nativephp/mobile-ota' => 'mobile-ota',
                        'nativephp/mobile-share' => 'mobile-share',
                        'nativephp/mobile-system' => 'mobile-system',
                        'nativephp/mobile-ui' => 'mobile-ui',
                        'nativephp/mobile-vibe' => 'mobile-vibe',
                    ],
                    'Tools and starter kits' => [
                        'nativephp/php-bin' => 'php-bin',
                        'nativephp/laravel-cloud-deploy' => 'laravel-cloud-deploy',
                        'nativephp/web-ui' => 'web-ui',
                        'nativephp/mobile-starter' => 'mobile-starter',
                        'nativephp/superstack' => 'superstack',
                    ],
                ];
            @endphp

            @foreach ($packageGroups as $group => $packages)
                <h3>{{ $group }}</h3>

                <ul
                    class="not-prose divide-y divide-gray-200 rounded-xl border border-gray-200 text-sm dark:divide-white/10 dark:border-white/10"
                >
                    @foreach ($packages as $package => $repository)
                        <li
                            class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 px-4 py-2.5"
                        >
                            <span
                                class="font-mono text-gray-700 dark:text-gray-300"
                            >
                                {{ $package }}
                            </span>
                            <a
                                href="https://github.com/NativePHP/{{ $repository }}/security/advisories/new"
                                class="font-medium"
                            >
                                Report a vulnerability
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach

            <h2>Bifrost, this website and private plugins</h2>

            <p>
                Email
                <a href="mailto:support@nativephp.com">support@nativephp.com</a>
                about vulnerabilities in
                <a href="https://bifrost.nativephp.com">Bifrost</a>
                or nativephp.com, and in NativePHP's own plugins that aren't
                open source.
            </p>

            <h2>Third-party plugins</h2>

            <p>
                Plugins made by other developers are maintained by their
                authors, so report vulnerabilities in them to the author. Every
                plugin's page in the
                <a href="{{ route('plugins') }}">plugin directory</a>
                lists its support contact.
            </p>

            <p>
                If you can't reach the author, or you think a plugin is
                malicious, use the report form on the plugin's page so the
                NativePHP team can step in.
            </p>

            <h2>What to include</h2>

            <ul>
                <li>
                    The package, service or plugin affected, and its version.
                </li>
                <li>A description of the vulnerability and its impact.</li>
                <li>Steps to reproduce it, or a proof of concept.</li>
            </ul>
        </article>
    </section>
</x-layout>
