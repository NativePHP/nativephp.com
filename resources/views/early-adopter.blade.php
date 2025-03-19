<x-layout title="NativePHP for iOS and Android">
    {{-- Hero --}}
    <section class="mt-10 px-5 md:mt-14">
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Tagline --}}
            <h1
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
                NativePHP For Mobile
            </h1>

            {{-- Description --}}
            <h3
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
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed"
            >
                Development of NativePHP for mobile has already started and you
                can get access and start building apps right now!
            </h3>
        </header>

        {{-- Cards --}}
        <div class="flex flex-wrap items-center justify-center gap-6 pt-10">
            {{-- iOS --}}
            <div
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [-10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="relative"
            >
                <div
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#EBECF6] p-6 text-center ring-4 ring-inset ring-white/60"
                >
                    {{-- Subtitle --}}
                    <h6 class="text-sm text-gray-500">Available on</h6>
                    {{-- Title --}}
                    <h2 class="pt-1 text-4xl font-semibold">iOS</h2>
                    {{-- Text --}}
                    <h4 class="pt-2.5 text-sm">
                        Join the Early Access Program to start developing iOS
                        apps.
                    </h4>
                    {{-- Mockup --}}
                    <div class="pt-10">
                        <img
                            src="{{ Vite::asset('resources/images/mobile/ios_phone_mockup.webp') }}"
                            alt=""
                            class="-mb-40 w-40"
                        />
                    </div>
                    {{-- White blurred circle --}}
                    <div
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl"
                    ></div>
                    {{-- Blue blurred circle --}}
                    <div
                        class="absolute bottom-0 right-1/2 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#9CA8D9]/40 blur-2xl"
                    ></div>
                </div>

                {{-- Blurred circle --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#DDE2F3] blur-[100px]"
                ></div>
            </div>

            {{-- Android --}}
            <div
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="relative"
            >
                <div
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#F6F1EB] p-6 text-center ring-4 ring-inset ring-white/60"
                >
                    {{-- Subtitle --}}
                    <h6 class="text-sm text-gray-500">Coming soon for</h6>
                    {{-- Title --}}
                    <h2 class="pt-1 text-4xl font-semibold">Android</h2>
                    {{-- Text --}}
                    <h4 class="pt-2.5 text-sm">
                        We're at hard work to make this possible, stay tuned!
                    </h4>
                    {{-- Mockup --}}
                    <div class="pt-10">
                        <img
                            src="{{ Vite::asset('resources/images/mobile/android_phone_mockup.webp') }}"
                            alt=""
                            class="-mb-40 w-40"
                        />
                    </div>
                    {{-- White blurred circle --}}
                    <div
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl"
                    ></div>
                    {{-- Center blurred circle --}}
                    <div
                        class="absolute bottom-0 right-1/2 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#E0D7CE] blur-2xl"
                    ></div>
                </div>

                {{-- Blurred circle --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#FBF2E7] blur-[100px]"
                ></div>
            </div>
        </div>
    </section>
</x-layout>
