<section
    class="mt-5"
    aria-labelledby="sponsors-title"
>
    <div
        class="dark:bg-mirage relative flex max-w-165 items-center gap-10 overflow-hidden rounded-2xl bg-gray-200/60 p-8 md:p-10"
    >
        {{-- Left side --}}
        <div class="relative z-10 flex flex-col gap-5 pl-5">
            {{-- Header --}}
            <div class="flex flex-col gap-1">
                <h3 class="text-lg text-gray-600 lg:text-xl dark:text-zinc-400">
                    Under the hood
                </h3>
                <h2
                    id="sponsors-title"
                    class="text-2xl font-bold text-gray-800 lg:text-3xl dark:text-white"
                >
                    How does it work?
                </h2>
            </div>

            {{-- Description --}}
            <p class="max-w-75 text-pretty text-gray-600 dark:text-zinc-400">
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
                You still write PHP like you’re used to—just with a few extra
                tools that connect it to the device's native features.
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
        <div class="relative z-10">
            <div class="grid">
                {{-- Phone wireframe --}}
                <x-illustrations.phone-wireframe
                    class="w-58 self-center justify-self-center text-[#333333] [grid-area:1/-1] dark:text-gray-500"
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
                        class="php-dashed-border grid place-items-center gap-3 rounded-lg px-2 pt-4 pb-2"
                    >
                        <div
                            class="text-sm font-medium text-gray-800 dark:text-white"
                        >
                            PHP Runtime
                        </div>

                        <div
                            class="grid w-full place-items-center rounded-lg bg-gray-200 px-2 py-7 dark:bg-gray-800"
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
</section>
