<x-layout title="Baking Delicious Native Apps">
    {{-- Hero --}}
    <section class="mt-10 px-5">
        {{-- Header --}}
        <header
            class="relative isolate grid place-items-center gap-0.5 uppercase"
        >
            <h1 class="text-8xl font-extrabold">Build</h1>
            <h1 class="relative isolate text-8xl font-extrabold text-[#9D91F1]">
                Native

                {{-- Blurred circle --}}
                <div
                    class="absolute -right-32 -top-20 size-60 rounded-full bg-white/60 blur-[100px]"
                ></div>
            </h1>
            <h1 class="text-8xl font-extrabold">PHP Apps</h1>

            {{-- Shiny line --}}
            <div
                class="absolute right-1/2 top-32 z-20 translate-x-1/2 rotate-[50deg]"
            >
                <div
                    x-init="
                        () => {
                            motion.animate(
                                $el,
                                {
                                    y: [-30, 0],
                                    opacity: [0, 0, 1],
                                },
                                {
                                    duration: 1.2,
                                    ease: motion.easeOut,
                                },
                            )
                        }
                    "
                    class="h-2.5 w-[26rem] bg-gradient-to-r from-transparent to-white/50 ring-1 ring-white/50"
                ></div>
            </div>
        </header>

        {{-- Description --}}
        <h3
            class="mx-auto max-w-4xl pt-5 text-center text-lg/relaxed text-gray-600 md:text-xl/relaxed"
        >
            Bring your
            <a
                href="https://www.php.net"
                target="_blank"
                class="inline-block font-medium text-cyan-500 transition duration-200 will-change-transform hover:-translate-y-0.5"
            >
                PHP
            </a>
            &
            <a
                href="https://laravel.com"
                target="_blank"
                class="inline-block font-medium text-[#F53003] transition duration-200 will-change-transform hover:-translate-y-0.5"
            >
                Laravel
            </a>
            skills to the world of
            <span class="text-black">desktop & mobile apps</span>
            . You can build cross-platform applications effortlessly—no extra
            tools, just the stack you love.
        </h3>

        {{-- Button --}}
        <div class="grid place-items-center pt-5">
            <a
                href="/docs/"
                class="group isolate z-0 grid place-items-center leading-snug text-white"
            >
                {{-- Label --}}
                <div
                    class="z-10 grid place-items-center gap-1.5 self-center justify-self-center [grid-area:1/-1]"
                >
                    <div>Get</div>
                    <div>Started</div>

                    {{-- Arrow --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 15 11"
                        fill="none"
                        class="mt-1 w-5 transition duration-300 ease-out will-change-transform group-hover:translate-x-0.5"
                    >
                        <path
                            d="M1 4.8C0.613401 4.8 0.3 5.1134 0.3 5.5C0.3 5.8866 0.613401 6.2 1 6.2L1 4.8ZM14.495 5.99498C14.7683 5.72161 14.7683 5.27839 14.495 5.00503L10.0402 0.550253C9.76684 0.276886 9.32362 0.276886 9.05025 0.550253C8.77689 0.823621 8.77689 1.26684 9.05025 1.5402L13.0101 5.5L9.05025 9.4598C8.77689 9.73317 8.77689 10.1764 9.05025 10.4497C9.32362 10.7231 9.76683 10.7231 10.0402 10.4497L14.495 5.99498ZM1 6.2L14 6.2L14 4.8L1 4.8L1 6.2Z"
                            fill="#DBDAE8"
                        />
                    </svg>
                </div>

                {{-- Blur --}}
                <div
                    class="z-30 size-20 self-center justify-self-center bg-white opacity-0 blur-xl transition duration-300 ease-in-out [grid-area:1/-1] group-hover:opacity-10"
                ></div>

                {{-- Shape --}}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-32 self-center justify-self-center text-black transition duration-500 ease-out will-change-transform [grid-area:1/-1] group-hover:rotate-6 group-hover:text-zinc-900"
                    viewBox="0 0 133 133"
                    fill="none"
                >
                    <path
                        d="M133 66.5028C133 58.2246 128.093 50.5844 119.798 44.4237C121.305 34.2085 119.374 25.3317 113.518 19.4759C107.663 13.6202 98.7915 11.689 88.5707 13.1967C82.4213 4.9071 74.7811 0 66.5028 0C58.2246 0 50.5844 4.9071 44.4237 13.2023C34.2085 11.6946 25.3317 13.6258 19.4759 19.4816C13.6202 25.3374 11.689 34.2086 13.1967 44.4293C4.9071 50.5787 0 58.2246 0 66.5028C0 74.7811 4.9071 82.4213 13.2023 88.582C11.6946 98.7971 13.6258 107.674 19.4816 113.53C25.3374 119.385 34.2086 121.317 44.4293 119.809C50.5844 128.099 58.2302 133.011 66.5085 133.011C74.7867 133.011 82.4269 128.104 88.5876 119.809C98.8027 121.317 107.68 119.385 113.535 113.53C119.391 107.674 121.322 98.8027 119.815 88.582C128.104 82.4269 133.017 74.7811 133.017 66.5028H133Z"
                        fill="currentColor"
                    />
                </svg>
            </a>
        </div>
    </section>

    {{-- Sponsors --}}
    <section class="mx-auto mt-20 max-w-5xl px-5">
        <div class="divide-y divide-[#242A2E]/20 *:py-8">
            {{-- Featured sponsors --}}
            <div
                class="flex flex-col items-center justify-between gap-10 md:flex-row md:items-start"
            >
                <h2 class="shrink-0 text-xl font-medium">Featured Sponsors</h2>
                <div
                    class="flex grow flex-wrap items-center justify-center gap-5 md:justify-end"
                >
                    <x-sponsors-featured />
                </div>
            </div>
            {{-- Corporate sponsors --}}
            <div
                class="flex flex-col items-center justify-between gap-10 md:flex-row md:items-start"
            >
                <h2 class="shrink-0 text-xl font-medium">Corporate Sponsors</h2>
                <div
                    class="flex grow flex-wrap items-center justify-center gap-5 md:justify-end"
                >
                    <x-sponsors-corporate />
                </div>
            </div>
        </div>

        <a
            href="/docs/getting-started/sponsoring"
            class="mt-6 inline-block rounded border bg-white px-4 py-1.5 text-xs font-semibold text-black"
        >
            Want your logo here?
        </a>
    </section>
</x-layout>
