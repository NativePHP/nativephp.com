<section
    class="mt-5"
    aria-labelledby="explainer-title"
    role="region"
>
    {{-- Part 1 --}}
    <div class="flex flex-col gap-5 lg:flex-row">
        {{-- How does it work --}}
        <div
            class="dark:bg-mirage relative flex flex-col items-center gap-5 overflow-hidden rounded-2xl bg-gray-200/60 p-8 sm:flex-row sm:justify-between sm:p-10 lg:max-w-165 xl:shrink-0"
        >
            {{-- Left side --}}
            <div class="relative z-10 flex flex-col gap-5 pl-5">
                {{-- Header --}}
                <div
                    class="2xs:items-start 2xs:text-left flex flex-col items-center gap-1 text-center text-pretty"
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
                {{-- Description --}}
                <p
                    class="text-pretty text-gray-600 sm:max-w-75 dark:text-zinc-400"
                >
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        NativePHP
                    </span>
                    bundles PHP with your app and lets it run inside a
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        Swift
                    </span>
                    ,
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        Kotlin
                    </span>
                    (mobile) or
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        Electron
                    </span>
                    (desktop) shell. It uses special
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        bridges
                    </span>
                    to talk directly to the device and show your app in a
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        native web view
                    </span>
                    .
                    <br />
                    <br />
                    You still write PHP like you’re used to—just with a few
                    extra tools that connect it to the device's native features.
                    <br />
                    <br />
                    That’s it. It feels like
                    <span class="font-medium text-gray-700 dark:text-zinc-300">
                        magic
                    </span>
                    , but it’s just PHP... on your user's device!
                </p>
            </div>

            {{-- Right side --}}
            <div class="relative z-10 pl-5 sm:pl-0">
                <div class="grid">
                    {{-- Phone wireframe --}}
                    <x-illustrations.phone-wireframe
                        class="w-58 self-center justify-self-center text-[#333333] [grid-area:1/-1] dark:text-gray-500"
                        aria-hidden="true"
                    />
                    {{-- Schema --}}
                    <div
                        class="relative top-11 z-12 flex w-51 flex-col gap-3 self-start justify-self-center rounded-lg bg-white/50 p-3 text-xs whitespace-nowrap ring-1 [grid-area:1/-1] dark:bg-slate-950/80 dark:ring-gray-500"
                    >
                        {{-- Header --}}
                        <div>
                            <div
                                class="text-sm font-medium text-gray-800 dark:text-white"
                            >
                                Swift or Kotlin
                            </div>
                            <div class="text-gray-600 dark:text-zinc-400">
                                Shell app
                            </div>
                        </div>
                        {{-- Php runtime --}}
                        <style>
                            .php-dashed-border {
                                background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='8' ry='8' stroke='%23333' stroke-width='3' stroke-dasharray='4%2c 10' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
                                border-radius: 8px;
                            }
                        </style>
                        <div
                            class="php-dashed-border grid place-items-center gap-2 rounded-lg px-2 pt-3.5 pb-4.5"
                        >
                            <div
                                class="text-sm font-medium text-gray-800 dark:text-white"
                            >
                                PHP Runtime
                            </div>
                            <div
                                class="grid w-full place-items-center rounded-lg bg-gray-200 px-2 py-6 dark:bg-gray-800"
                            >
                                <div
                                    class="font-medium text-gray-700 dark:text-white"
                                >
                                    Custom PHP Extension
                                </div>
                            </div>
                        </div>
                        {{-- Core --}}
                        <div class="flex items-center gap-2">
                            {{-- Left --}}
                            <div
                                class="relative grid w-1/2 place-items-center rounded-lg bg-purple-200 px-3 py-7 dark:bg-violet-400/60"
                            >
                                <div
                                    class="flex flex-col gap-0.5 text-center font-medium text-gray-700 capitalize dark:text-white"
                                >
                                    <div>Native</div>
                                    <div>mobile</div>
                                    <div>functions</div>
                                </div>
                                {{-- Bottom arrow --}}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="absolute -top-12 right-1/2 w-2.5 translate-x-1/2"
                                    viewBox="0 0 8 60"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M4 0.113249L1.11325 3L4 5.88675L6.88675 3L4 0.113249ZM3.64645 53.3536C3.84171 53.5488 4.15829 53.5488 4.35356 53.3536L7.53554 50.1716C7.7308 49.9763 7.7308 49.6597 7.53554 49.4645C7.34027 49.2692 7.02369 49.2692 6.82843 49.4645L4 52.2929L1.17157 49.4645C0.976313 49.2692 0.65973 49.2692 0.464468 49.4645C0.269206 49.6597 0.269206 49.9763 0.464468 50.1716L3.64645 53.3536ZM4 3L3.5 3L3.5 53L4 53L4.5 53L4.5 3L4 3Z"
                                        fill="currentColor"
                                    />
                                </svg>
                            </div>
                            {{-- Right --}}
                            <div
                                class="relative grid w-1/2 place-items-center rounded-lg bg-[#d7f7a0] px-3 py-7 dark:bg-teal-300/70"
                            >
                                <div
                                    class="flex flex-col gap-0.5 text-center font-medium text-gray-700 capitalize dark:text-white"
                                >
                                    <div>Custom</div>
                                    <div>Swift/Kotlin</div>
                                    <div>Bridges</div>
                                </div>
                                {{-- Top arrow --}}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="absolute -top-6 right-1/2 w-2.5 translate-x-1/2"
                                    viewBox="0 0 8 36"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M3.875 35.8868L6.76175 33L3.875 30.1132L0.988249 33L3.875 35.8868ZM4.22855 0.646446C4.03329 0.451183 3.71671 0.451183 3.52145 0.646446L0.339465 3.82843C0.144203 4.02369 0.144203 4.34027 0.339465 4.53553C0.534727 4.7308 0.851309 4.7308 1.04657 4.53553L3.875 1.70711L6.70343 4.53553C6.89869 4.7308 7.21527 4.7308 7.41053 4.53553C7.60579 4.34027 7.60579 4.02369 7.41053 3.82843L4.22855 0.646446ZM3.875 33L4.375 33L4.375 1L3.875 1L3.375 1L3.375 33L3.875 33Z"
                                        fill="currentColor"
                                    />
                                </svg>
                                {{-- Bottom arrow --}}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="absolute right-1/2 -bottom-6 w-2.5 translate-x-1/2 -scale-y-100"
                                    viewBox="0 0 8 36"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M3.875 35.8868L6.76175 33L3.875 30.1132L0.988249 33L3.875 35.8868ZM4.22855 0.646446C4.03329 0.451183 3.71671 0.451183 3.52145 0.646446L0.339465 3.82843C0.144203 4.02369 0.144203 4.34027 0.339465 4.53553C0.534727 4.7308 0.851309 4.7308 1.04657 4.53553L3.875 1.70711L6.70343 4.53553C6.89869 4.7308 7.21527 4.7308 7.41053 4.53553C7.60579 4.34027 7.60579 4.02369 7.41053 3.82843L4.22855 0.646446ZM3.875 33L4.375 33L4.375 1L3.875 1L3.375 1L3.375 33L3.875 33Z"
                                        fill="currentColor"
                                    />
                                </svg>
                            </div>
                        </div>
                        {{-- WebView --}}
                        <div
                            class="grid place-items-center gap-2 rounded-lg p-5 ring-1 dark:ring-white/25"
                        >
                            <div
                                class="text-sm font-medium text-gray-800 dark:text-white"
                            >
                                Native WebView
                            </div>
                            <div class="text-gray-600 dark:text-zinc-400">
                                HTML/CSS + JavaScript
                            </div>
                        </div>
                    </div>
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
                class="pointer-events-none absolute inset-x-0 top-8 z-20 h-px text-gray-300 dark:text-white/10"
                aria-hidden="true"
            >
                <div
                    class="h-px w-full [background-image:linear-gradient(to_right,currentColor_0_8px,transparent_8px_16px)] [background-size:16px_100%] [background-position:0.5px_0] bg-repeat"
                ></div>
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex flex-col gap-5 lg:max-w-sm xl:max-w-max">
            {{-- Performance --}}
            <div class="xs:grid-cols-2 grid items-stretch gap-5">
                <div
                    class="dark:from-mirage 2xs:items-start dark:to-mirage flex flex-col items-center gap-3 rounded-2xl bg-gradient-to-tl from-[#FEF3C6] to-[#FFFBEB] p-7 2xl:gap-4 2xl:p-8"
                >
                    <x-icons.home.charging-thunder
                        class="size-12 text-yellow-400 2xl:size-14 dark:text-amber-300"
                    />
                    <div
                        class="2xs:items-start 2xs:text-left flex flex-col items-center gap-1 text-center text-pretty"
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
                    class="dark:from-mirage 2xs:items-start dark:to-mirage flex flex-col items-center gap-3 rounded-2xl bg-gradient-to-tl from-[#ECFCCA] to-[#F7FEE7] p-7 2xl:gap-4 2xl:p-8"
                >
                    <x-icons.home.rocket
                        class="size-12 text-lime-400 2xl:size-14 dark:text-lime-300"
                    />
                    <div
                        class="2xs:items-start 2xs:text-left flex flex-col items-center gap-1 text-center text-pretty"
                    >
                        <h3
                            class="text-xl font-semibold text-gray-800 2xl:text-2xl dark:text-white"
                        >
                            Tiny apps
                        </h3>
                        <h4
                            class="text-gray-600 2xl:text-lg dark:text-zinc-400"
                        >
                            Mobile apps under 50MB
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Tools --}}
            <div
                class="dark:from-mirage dark:to-mirage flex flex-col gap-4 rounded-2xl bg-gradient-to-tl from-[#DBDCFB] to-[#F9FAFB] p-7 2xl:p-8"
            >
                <div
                    class="2xs:items-start 2xs:text-left flex flex-col items-center gap-1 text-center text-pretty"
                >
                    <h3
                        class="text-xl font-semibold text-gray-800 2xl:text-2xl dark:text-white"
                    >
                        Bring your favorite tools
                    </h3>
                    <h4 class="text-gray-600 2xl:text-lg dark:text-zinc-400">
                        Use any Composer packages and front-end frameworks
                    </h4>
                </div>

                <div
                    class="flex flex-wrap items-start gap-x-2.5 gap-y-3 lg:pt-2 2xl:gap-x-3"
                >
                    @php
                        $skills = [
                            ['name' => 'Laravel', 'link' => 'https://laravel.com/', 'icon' => 'icons.skills.laravel'],
                            ['name' => 'React', 'link' => 'https://reactjs.org/', 'icon' => 'icons.skills.reactjs'],
                            ['name' => 'Vue.js', 'link' => 'https://vuejs.org/', 'icon' => 'icons.skills.vuejs'],
                            ['name' => 'Nuxt', 'link' => 'https://nuxtjs.org/', 'icon' => 'icons.skills.nuxtjs'],
                            ['name' => 'Next.js', 'link' => 'https://nextjs.org/', 'icon' => 'icons.skills.nextjs'],
                            ['name' => 'Livewire', 'link' => 'https://livewire.laravel.com', 'icon' => 'icons.skills.livewire'],
                            ['name' => 'FilamentPHP', 'link' => 'https://filamentphp.com/', 'icon' => 'icons.skills.filamentphp'],
                            ['name' => 'Alpine.js', 'link' => 'https://alpinejs.dev/', 'icon' => 'icons.skills.alpinejs'],
                            ['name' => 'Inertia.js', 'link' => 'https://inertiajs.com/', 'icon' => 'icons.skills.inertiajs'],
                            ['name' => 'TailwindCSS', 'link' => 'https://tailwindcss.com/', 'icon' => 'icons.skills.tailwind-css'],
                            ['name' => 'TypeScript', 'link' => 'https://www.typescriptlang.org/', 'icon' => 'icons.skills.typescript'],
                            ['name' => 'JavaScript', 'link' => 'https://www.javascript.com/', 'icon' => 'icons.skills.javascript'],
                            ['name' => 'Pest', 'link' => 'https://pestphp.com/', 'icon' => 'icons.skills.pest'],
                            ['name' => 'PHPUnit', 'link' => 'https://phpunit.de/', 'icon' => 'icons.skills.phpunit'],
                        ];
                    @endphp

                    @foreach ($skills as $skill)
                        <x-home.skill-pill
                            :name="$skill['name']"
                            :link="$skill['link']"
                        >
                            <x-dynamic-component :component="$skill['icon']" />
                        </x-home.skill-pill>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Part 2 --}}
    <div class="mt-5 flex flex-col gap-5 lg:flex-row">
        {{-- Left side --}}
        <div
            class="dark:bg-mirage w-full rounded-2xl bg-gray-200/60 p-8 md:p-10 lg:max-w-md xl:max-w-lg"
        >
            {{-- Header --}}
            <div
                class="2xs:items-start 2xs:text-left flex flex-col items-center gap-1 text-center text-pretty"
            >
                <p class="text-lg text-gray-600 lg:text-xl dark:text-zinc-400">
                    Step by step
                </p>
                <h2
                    class="text-xl font-bold text-gray-800 lg:text-2xl dark:text-white"
                >
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
                        <x-icons.home.document
                            class="size-5"
                            aria-hidden="true"
                        />
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
                        <x-icons.home.browser
                            class="size-5"
                            aria-hidden="true"
                        />
                    </div>
                    <span class="text-gray-400 dark:text-zinc-400">2.</span>
                    <span class="text-gray-800 dark:text-white">
                        Install the package.
                    </span>
                </li>
                <li
                    class="flex items-center gap-3 rounded-2xl bg-white/50 py-3 pr-5 pl-3 font-medium dark:bg-slate-950/30"
                >
                    <div
                        class="grid size-10 shrink-0 place-items-center rounded-xl bg-cyan-100 dark:bg-cyan-500/20"
                    >
                        <x-icons.home.startup
                            class="size-5"
                            aria-hidden="true"
                        />
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
            class="dark:bg-mirage relative z-0 flex flex-col justify-center gap-4 overflow-hidden rounded-2xl bg-[#F0F2E7] p-7 2xl:p-8"
        >
            <div
                class="2xs:items-start 2xs:text-left flex flex-col items-center gap-1 text-center text-pretty"
            >
                <p class="text-lg text-[#9FA382] lg:text-xl dark:text-zinc-400">
                    Your next app starts here
                </p>
                <h2
                    class="text-xl font-bold text-gray-800 lg:text-2xl dark:text-white"
                >
                    What can I build?
                </h2>
            </div>

            {{-- Description --}}
            <p
                class="2xs:text-left text-center text-pretty text-gray-600 dark:text-zinc-400"
            >
                Whether you're building tools for your team, apps for your
                customers, or your next big idea —
                <span class="font-medium text-gray-700 dark:text-zinc-300">
                    NativePHP
                </span>
                gives you the flexibility and performance to bring it to life.
            </p>

            <div
                class="2xs:justify-start flex flex-wrap items-start justify-center gap-x-2.5 gap-y-3 [--icon-bg:#F9FBF0] [--icon-stroke:#717838] 2xl:gap-x-3 dark:[--icon-bg:--alpha(var(--color-teal-500)/30%)] dark:[--icon-stroke:--alpha(var(--color-teal-400)/80%)]"
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
