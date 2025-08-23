<section
    class="mt-5"
    aria-labelledby="sponsors-title"
>
    <div
        class="dark:bg-mirage relative flex max-w-160 items-center gap-10 overflow-hidden rounded-2xl bg-gray-200/60 p-8 md:p-10"
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
                <x-illustrations.phone-wireframe
                    class="w-52 text-[#333333] dark:text-gray-400"
                />
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
