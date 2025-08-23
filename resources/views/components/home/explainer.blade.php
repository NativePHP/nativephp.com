<section
    class="mt-5"
    aria-labelledby="sponsors-title"
>
    <div
        class="dark:bg-mirage relative flex max-w-160 items-center gap-10 rounded-2xl bg-gray-200/60 p-8 md:p-10"
    >
        {{-- Left side --}}
        <div class="flex flex-col gap-5">
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
                You still write PHP like you’re used to—just with a few extra
                tools that connect it to the device's native features.
                <br />
                That’s it. It feels like
                <span class="font-medium text-gray-700 dark:text-zinc-300">
                    magic
                </span>
                , but it’s just PHP... on your user's device!
            </p>
        </div>

        {{-- Right side --}}
        <div class="">
            <div class="grid">
                <x-illustrations.phone-wireframe
                    class="w-52 text-[#333333] dark:text-gray-400"
                />
            </div>
        </div>
    </div>
</section>
