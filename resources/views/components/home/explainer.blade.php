<section
    class="mt-5"
    aria-labelledby="explainer-title"
    role="region"
>
    {{-- Part 1 --}}
    <div class="flex flex-col gap-5 lg:flex-row">
        {{-- How does it work --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        gsap.fromTo(
                            $el,
                            { x: -10, autoAlpha: 0 },
                            {
                                x: 0,
                                autoAlpha: 1,
                                duration: 0.7,
                                ease: 'power2.out',
                            },
                        )
                    })
                }
            "
            class="relative flex flex-col items-center gap-5 overflow-hidden rounded-2xl bg-gray-200/60 p-8 sm:flex-row sm:justify-between sm:p-10 lg:max-w-165 xl:shrink-0 dark:bg-mirage"
        >
            {{-- Left side --}}
            <div class="relative z-10 flex flex-col gap-5 pl-5">
                {{-- Header --}}
                <div
                    class="flex flex-col items-center gap-1 text-center text-pretty 2xs:items-start 2xs:text-left"
                >
                    <p
                        class="text-lg text-gray-600 lg:text-xl dark:text-zinc-400"
                    >
                        Under the hood
                    </p>
                    <h2
                        id="explainer-title"
                        class="text-2xl font-bold text-gray-800 lg:text-3xl dark:text-white"
                    >
                        How does it work?
                    </h2>
                </div>
                {{-- Description: Mobile --}}
                <p
                    x-show="$store.platform.is('mobile')"
                    id="platform-panel-mobile"
                    role="tabpanel"
                    aria-label="How NativePHP for Mobile works"
                    class="text-pretty text-gray-600 sm:max-w-75 dark:text-zinc-400"
                >
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        NativePHP
                    </span>
                    bundles PHP with your app and runs it
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        inside
                    </span>
                    the app process. No server. No network round-trip.

                    <br />
                    <br />
                    Your Blade renders straight to real
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        SwiftUI
                    </span>
                    and
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        Jetpack Compose
                    </span>
                    views over
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        shared memory.
                    </span>
                    No web view. No JSON bridge.

                    <br />
                    <br />
                    We call this
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        SuperNative.
                    </span>

                    <br />
                    <br />
                    You still write PHP like you’re used to—just with a few
                    extra tools that connect it to the device's native features.
                    <br />
                    <br />
                    That’s it. It feels like
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        magic,
                    </span>
                    but it’s just PHP... on your user's device!
                </p>

                {{--
                    v4 is still a prerelease, so link the version explicitly:
                    the unversioned URL resolves to the latest stable docs.
                --}}
                <a
                    x-show="$store.platform.is('mobile')"
                    href="{{ route('docs.show', ['platform' => 'mobile', 'version' => 4, 'page' => 'architecture/super-native']) }}"
                    class="group inline-flex items-center gap-2 text-sm font-medium text-gray-700 transition duration-200 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white"
                >
                    Learn more about SuperNative
                    <span
                        class="transition duration-200 will-change-transform group-hover:translate-x-1"
                        aria-hidden="true"
                    >
                        &rarr;
                    </span>
                </a>

                {{-- Description: Desktop --}}
                <p
                    x-show="$store.platform.is('desktop')"
                    x-cloak
                    id="platform-panel-desktop"
                    role="tabpanel"
                    aria-label="How NativePHP for Desktop works"
                    class="text-pretty text-gray-600 sm:max-w-75 dark:text-zinc-400"
                >
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        NativePHP
                    </span>
                    ships a
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        statically-compiled PHP binary
                    </span>
                    inside your app, alongside an
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        Electron
                    </span>
                    shell. Your users install nothing else.

                    <br />
                    <br />
                    The two talk to each other over
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        authenticated HTTP
                    </span>
                    on the device, so your Laravel routes drive a real
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        Chromium window,
                    </span>
                    rendering the same HTML, CSS and JavaScript you already
                    write.

                    <br />
                    <br />
                    You still write PHP like you’re used to—just with a few
                    extra tools for windows, menus, notifications and the file
                    system.
                    <br />
                    <br />
                    That’s it. It feels like
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        magic,
                    </span>
                    but it’s just PHP... on your user's device!
                </p>
            </div>

            {{-- Right side --}}
            <div class="relative z-10 pl-5 sm:pl-0">
                {{-- Shared dashed frame used by both platform diagrams --}}
                <style>
                    .php-dashed-border {
                        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='8' ry='8' stroke='%23333' stroke-width='3' stroke-dasharray='4%2c 10' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
                        border-radius: 8px;
                    }
                </style>

                <div x-show="$store.platform.is('mobile')">
                    <x-illustrations.mobile-stack />
                </div>

                <div
                    x-show="$store.platform.is('desktop')"
                    x-cloak
                >
                    <x-illustrations.desktop-stack />
                </div>
            </div>
            {{-- Grid illustration --}}
            <div
                class="pointer-events-none absolute inset-y-0 right-0 z-0 h-full w-[520px] text-gray-300 md:w-[620px] dark:text-white/7"
                aria-hidden="true"
            >
                <div
                    class="h-full w-full [background-image:linear-gradient(to_right,currentColor_0_1px,transparent_1px),linear-gradient(to_bottom,currentColor_0_1px,transparent_1px)] mask-l-from-30% [background-size:20px_100%,100%_20px] [background-position:0.5px_0,0_0.5px] bg-repeat [mask-repeat:no-repeat] [-webkit-mask-repeat:no-repeat]"
                ></div>
            </div>
            {{-- Dashed vertical line --}}
            <div
                class="pointer-events-none absolute inset-y-0 left-6 z-20 w-px text-gray-300 dark:text-white/10"
                aria-hidden="true"
            >
                <div
                    class="h-full w-px [background-image:linear-gradient(to_bottom,currentColor_0_8px,transparent_8px_16px)] [background-size:100%_16px] [background-position:0_0.5px] bg-repeat"
                ></div>
            </div>
            {{-- Solid vertical line --}}
            <div
                class="pointer-events-none absolute inset-y-0 left-10 z-20 w-px text-gray-300 dark:text-white/10"
                aria-hidden="true"
            >
                <div
                    class="h-full w-px bg-current [background-position:0_0.5px]"
                ></div>
            </div>
            {{-- Dashed horizontal line --}}
            <div
                class="pointer-events-none absolute inset-x-0 top-7 z-20 h-px text-gray-300 dark:text-white/10"
                aria-hidden="true"
            >
                <div
                    class="h-px w-full [background-image:linear-gradient(to_right,currentColor_0_8px,transparent_8px_16px)] [background-size:16px_100%] [background-position:0.5px_0] bg-repeat"
                ></div>
            </div>
        </div>

        {{-- Right side --}}
        {{--
            Grow to fill rather than sizing to content: the Mobile track's
            pill list is short, so a max-content width leaves a gap on the
            right instead of lining up with the cards above.
        --}}
        <div class="flex flex-col gap-5 lg:max-w-sm lg:grow xl:max-w-none">
            {{-- Performance --}}
            <div class="grid items-stretch gap-5 xs:grid-cols-2">
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                gsap.fromTo(
                                    $el,
                                    { y: -10, autoAlpha: 0 },
                                    {
                                        y: 0,
                                        autoAlpha: 1,
                                        duration: 0.7,
                                        ease: 'power2.out',
                                    },
                                )
                            })
                        }
                    "
                    class="flex flex-col items-center gap-3 rounded-2xl bg-gradient-to-tl from-[#FEF3C6] to-[#FFFBEB] p-7 2xs:items-start 2xl:gap-4 2xl:p-8 dark:from-mirage dark:to-mirage"
                >
                    <x-icons.home.charging-thunder
                        class="size-12 text-yellow-400 2xl:size-14 dark:text-amber-300"
                    />
                    <div
                        class="flex flex-col items-center gap-1 text-center text-pretty 2xs:items-start 2xs:text-left"
                    >
                        <h3
                            class="text-xl font-semibold text-gray-800 2xl:text-2xl dark:text-white"
                        >
                            Fast apps
                        </h3>
                        <h4
                            class="text-gray-600 2xl:text-lg dark:text-zinc-400"
                        >
                            Laravel running at native speed
                        </h4>
                    </div>
                </div>
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                gsap.fromTo(
                                    $el,
                                    { y: 10, autoAlpha: 0 },
                                    {
                                        y: 0,
                                        autoAlpha: 1,
                                        duration: 0.7,
                                        ease: 'power2.out',
                                    },
                                )
                            })
                        }
                    "
                    class="flex flex-col items-center gap-3 rounded-2xl bg-gradient-to-tl from-[#ECFCCA] to-[#F7FEE7] p-7 2xs:items-start 2xl:gap-4 2xl:p-8 dark:from-mirage dark:to-mirage"
                >
                    <x-icons.home.rocket
                        class="size-12 text-lime-400 2xl:size-14 dark:text-lime-300"
                    />
                    <div
                        class="flex flex-col items-center gap-1 text-center text-pretty 2xs:items-start 2xs:text-left"
                    >
                        <h3
                            class="text-xl font-semibold text-gray-800 2xl:text-2xl dark:text-white"
                        >
                            <span x-show="$store.platform.is('mobile')">
                                Tiny apps
                            </span>
                            <span
                                x-show="$store.platform.is('desktop')"
                                x-cloak
                            >
                                One file
                            </span>
                        </h3>
                        <h4
                            class="text-gray-600 2xl:text-lg dark:text-zinc-400"
                        >
                            <span x-show="$store.platform.is('mobile')">
                                Mobile apps under 50MB
                            </span>
                            <span
                                x-show="$store.platform.is('desktop')"
                                x-cloak
                            >
                                Ships as a single executable
                            </span>
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Tools --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                $el,
                                { x: 10, autoAlpha: 0 },
                                {
                                    x: 0,
                                    autoAlpha: 1,
                                    duration: 0.7,
                                    ease: 'power2.out',
                                },
                            )
                        })
                    }
                "
                class="flex flex-col gap-4 rounded-2xl bg-gradient-to-tl from-[#DBDCFB] to-[#F9FAFB] p-7 2xl:p-8 dark:from-mirage dark:to-mirage"
            >
                <div
                    class="flex flex-col items-center gap-1.5 text-center text-pretty 2xs:items-start 2xs:text-left"
                >
                    <h3
                        class="text-2xl font-bold text-gray-800 dark:text-white"
                    >
                        Bring your favorite tools
                    </h3>
                    <h4
                        x-show="$store.platform.is('mobile')"
                        class="text-gray-600 2xl:text-lg dark:text-zinc-400"
                    >
                        Use (almost) any Composer package!
                    </h4>
                    <h4
                        x-show="$store.platform.is('desktop')"
                        x-cloak
                        class="text-gray-600 2xl:text-lg dark:text-zinc-400"
                    >
                        Use any Composer package and front-end framework you
                        like.
                    </h4>
                </div>

                @php
                    // Work the same whether your UI is native or a web view.
                    $phpSkills = [
                        ['name' => 'Laravel', 'link' => 'https://laravel.com/', 'icon' => 'icons.skills.laravel'],
                        ['name' => 'Pest', 'link' => 'https://pestphp.com/', 'icon' => 'icons.skills.pest'],
                        ['name' => 'PHPUnit', 'link' => 'https://phpunit.de/', 'icon' => 'icons.skills.phpunit'],
                    ];
                    // Render HTML, so they belong to the web view story.
                    $webSkills = [
                        ['name' => 'Livewire', 'link' => 'https://livewire.laravel.com', 'icon' => 'icons.skills.livewire'],
                        ['name' => 'FilamentPHP', 'link' => 'https://filamentphp.com/', 'icon' => 'icons.skills.filamentphp'],
                        ['name' => 'TailwindCSS', 'link' => 'https://tailwindcss.com/', 'icon' => 'icons.skills.tailwind-css'],
                        ['name' => 'Alpine.js', 'link' => 'https://alpinejs.dev/', 'icon' => 'icons.skills.alpinejs'],
                        ['name' => 'Inertia.js', 'link' => 'https://inertiajs.com/', 'icon' => 'icons.skills.inertiajs'],
                        ['name' => 'React', 'link' => 'https://reactjs.org/', 'icon' => 'icons.skills.reactjs'],
                        ['name' => 'Vue.js', 'link' => 'https://vuejs.org/', 'icon' => 'icons.skills.vuejs'],
                        ['name' => 'Nuxt', 'link' => 'https://nuxtjs.org/', 'icon' => 'icons.skills.nuxtjs'],
                        ['name' => 'Next.js', 'link' => 'https://nextjs.org/', 'icon' => 'icons.skills.nextjs'],
                        ['name' => 'TypeScript', 'link' => 'https://www.typescriptlang.org/', 'icon' => 'icons.skills.typescript'],
                        ['name' => 'JavaScript', 'link' => 'https://www.javascript.com/', 'icon' => 'icons.skills.javascript'],
                    ];
                @endphp

                {{--
                    One list: the web front-ends drop out on the mobile path.
                    Each icon must render exactly once — several skill SVGs use
                    document-wide ids (Pest's gradient is plain id="a"), so a
                    duplicated pill silently loses its fill.
                --}}
                <div
                    class="flex flex-wrap items-start gap-x-2.5 gap-y-3.5 lg:pt-2 2xl:gap-x-3"
                >
                    @foreach ($phpSkills as $skill)
                        <x-home.skill-pill
                            :name="$skill['name']"
                            :link="$skill['link']"
                            data-tools="all"
                        >
                            <x-dynamic-component :component="$skill['icon']" />
                        </x-home.skill-pill>
                    @endforeach

                    @foreach ($webSkills as $skill)
                        <x-home.skill-pill
                            x-show="$store.platform.is('desktop')"
                            x-cloak
                            :name="$skill['name']"
                            :link="$skill['link']"
                            data-tools="desktop"
                        >
                            <x-dynamic-component :component="$skill['icon']" />
                        </x-home.skill-pill>
                    @endforeach
                </div>

                {{-- Render targets --}}
                <div
                    class="flex flex-col items-center gap-3 border-t border-gray-300/70 pt-4 text-center 2xs:items-start 2xs:text-left dark:border-white/10"
                >
                    <p
                        x-show="$store.platform.is('mobile')"
                        class="text-sm text-pretty text-gray-600 2xl:text-base dark:text-zinc-400"
                    >
                        Blade templates feel like HTML and render to real native
                        views.
                        <span
                            class="font-medium text-gray-700 dark:text-zinc-300"
                        >
                            No web view required
                        </span>
                    </p>

                    <div
                        x-show="$store.platform.is('mobile')"
                        class="flex flex-wrap items-start gap-x-2.5 gap-y-3.5 2xl:gap-x-3"
                    >
                        @php
                            $renderTargets = [
                                ['name' => 'SwiftUI', 'link' => 'https://developer.apple.com/xcode/swiftui/', 'icon' => 'icons.home.apple'],
                                ['name' => 'Jetpack Compose', 'link' => 'https://developer.android.com/compose', 'icon' => 'icons.home.android'],
                            ];
                        @endphp

                        @foreach ($renderTargets as $renderTarget)
                            <x-home.skill-pill
                                :name="$renderTarget['name']"
                                :link="$renderTarget['link']"
                                data-tools="mobile"
                            >
                                <x-dynamic-component
                                    :component="$renderTarget['icon']"
                                />
                            </x-home.skill-pill>
                        @endforeach
                    </div>

                    <p
                        x-show="$store.platform.is('desktop')"
                        x-cloak
                        class="text-sm text-pretty text-gray-600 2xl:text-base dark:text-zinc-400"
                    >
                        Your front-end renders in a
                        <span
                            class="font-medium text-gray-700 dark:text-zinc-300"
                        >
                            Chromium window.
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Part 2 --}}
    <div class="mt-5 flex flex-col gap-5 lg:flex-row">
        {{-- Left side --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        gsap.fromTo(
                            $el,
                            { x: -10, autoAlpha: 0 },
                            {
                                x: 0,
                                autoAlpha: 1,
                                duration: 0.7,
                                ease: 'power2.out',
                            },
                        )
                    })
                }
            "
            class="w-full rounded-2xl bg-gray-200/60 p-8 md:p-10 lg:max-w-md xl:max-w-lg dark:bg-mirage"
        >
            {{-- Header --}}
            <div
                class="flex flex-col items-center gap-1 text-center text-pretty 2xs:items-start 2xs:text-left"
            >
                <p class="text-lg text-gray-600 lg:text-xl dark:text-zinc-400">
                    Step by step
                </p>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    How do I get it?
                </h2>
            </div>

            {{-- Steps --}}
            <ol class="mt-5 flex flex-col gap-3">
                <li
                    class="flex items-center gap-3 rounded-2xl bg-white/50 py-3 pr-5 pl-3 font-medium dark:bg-slate-950/30"
                >
                    <div
                        class="grid size-10 shrink-0 place-items-center rounded-xl bg-blue-100 dark:bg-blue-500/20"
                    >
                        <div
                            class="[--icon-bg:#F9FBF0] [--icon-dot:#BEDBFF] [--icon-stroke:#155DFC] dark:[--icon-bg:--alpha(var(--color-blue-400)/30%)] dark:[--icon-dot:--alpha(var(--color-blue-400)/70%)] dark:[--icon-stroke:--alpha(var(--color-blue-300)/80%)]"
                        >
                            <x-icons.home.document
                                class="size-5"
                                aria-hidden="true"
                            />
                        </div>
                    </div>
                    <span class="text-gray-400 dark:text-zinc-400">1.</span>
                    <span class="text-gray-800 dark:text-white">
                        Read the docs
                    </span>
                </li>
                <li
                    class="flex items-center gap-3 rounded-2xl bg-white/50 py-3 pr-5 pl-3 font-medium dark:bg-slate-950/30"
                >
                    <div
                        class="grid size-10 shrink-0 place-items-center rounded-xl bg-violet-100 dark:bg-violet-500/20"
                    >
                        <div
                            class="[--icon-bg:#fff] [--icon-dot:#DDD6FF] [--icon-stroke:#7F22FE] dark:[--icon-bg:--alpha(var(--color-violet-400)/30%)] dark:[--icon-dot:--alpha(var(--color-violet-400)/70%)] dark:[--icon-stroke:--alpha(var(--color-violet-300)/80%)]"
                        >
                            <x-icons.home.browser
                                class="size-5"
                                aria-hidden="true"
                            />
                        </div>
                    </div>
                    <span class="text-gray-400 dark:text-zinc-400">2.</span>
                    <span class="text-gray-800 dark:text-white">
                        Install
                        <code
                            class="rounded bg-black/5 px-1.5 py-0.5 text-sm dark:bg-white/10"
                        >
                            <span x-show="$store.platform.is('mobile')">
                                nativephp/mobile
                            </span>
                            <span
                                x-show="$store.platform.is('desktop')"
                                x-cloak
                            >
                                nativephp/desktop
                            </span>
                        </code>
                    </span>
                </li>
                <li
                    class="flex items-center gap-3 rounded-2xl bg-white/50 py-3 pr-5 pl-3 font-medium dark:bg-slate-950/30"
                >
                    <div
                        class="grid size-10 shrink-0 place-items-center rounded-xl bg-cyan-100 dark:bg-cyan-500/20"
                    >
                        <div
                            class="[--icon-bg:#fff] [--icon-dot:#CEFAFE] [--icon-stroke:#0092B8] dark:[--icon-bg:--alpha(var(--color-sky-400)/30%)] dark:[--icon-dot:--alpha(var(--color-sky-400)/70%)] dark:[--icon-stroke:--alpha(var(--color-sky-300)/80%)]"
                        >
                            <x-icons.home.startup
                                class="size-5"
                                aria-hidden="true"
                            />
                        </div>
                    </div>
                    <span class="text-gray-400 dark:text-zinc-400">3.</span>
                    <span class="text-gray-800 dark:text-white">
                        Build your app.
                    </span>
                </li>
            </ol>
        </div>

        {{-- Right side --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        gsap.fromTo(
                            $el,
                            { x: 10, autoAlpha: 0 },
                            {
                                x: 0,
                                autoAlpha: 1,
                                duration: 0.7,
                                ease: 'power2.out',
                            },
                        )
                    })
                }
            "
            class="relative z-0 flex flex-col justify-center gap-4 overflow-hidden rounded-2xl bg-[#F0F2E7] p-7 2xl:p-8 dark:bg-mirage"
        >
            <div
                class="flex flex-col items-center gap-1 text-center text-pretty 2xs:items-start 2xs:text-left"
            >
                <p class="text-lg text-[#9FA382] lg:text-xl dark:text-zinc-400">
                    Your next app starts here
                </p>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    What can I build?
                </h2>
            </div>

            {{-- Description --}}
            <p
                class="text-center text-pretty text-gray-600 2xs:text-left dark:text-zinc-400"
            >
                Whether you're building tools for your team, apps for your
                customers, or your next big idea —
                <span class="font-medium text-gray-700 dark:text-zinc-300">
                    NativePHP
                </span>
                gives you the flexibility and performance to bring it to life.
            </p>

            <div
                class="flex flex-wrap items-start justify-center gap-x-2.5 gap-y-3 [--icon-bg:#F9FBF0] [--icon-stroke:#717838] 2xs:justify-start 2xl:gap-x-3 dark:[--icon-bg:--alpha(var(--color-cyan-500)/30%)] dark:[--icon-stroke:--alpha(var(--color-cyan-400)/80%)]"
            >
                @php
                    $categories = [
                        ['name' => 'SaaS clients', 'icon' => 'icons.home.web'],
                        ['name' => 'Games', 'icon' => 'icons.home.game'],
                        ['name' => 'eCommerce', 'icon' => 'icons.home.shop'],
                        ['name' => 'Social apps', 'icon' => 'icons.home.social'],
                        ['name' => 'Field services', 'icon' => 'icons.home.wrench'],
                        ['name' => 'Health', 'icon' => 'icons.home.health'],
                    ];
                @endphp

                @foreach ($categories as $category)
                    <x-home.category-pill :name="$category['name']">
                        <x-dynamic-component :component="$category['icon']" />
                    </x-home.category-pill>
                @endforeach
            </div>

            {{-- Decorative circle --}}
            <div
                class="absolute -top-20 -right-20 -z-10 size-60 rounded-full bg-gradient-to-r from-[#C1D2AF]/25 to-[#E8F9EE]/0 dark:hidden"
            ></div>
        </div>
    </div>
</section>
