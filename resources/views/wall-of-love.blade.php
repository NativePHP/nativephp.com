<x-layout title="Thank You to Our Early Adopters">
    {{-- Hero Section --}}
    <section
        class="mt-10 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 size-60 translate-x-1/2 rounded-full bg-purple-100/70 blur-3xl md:w-80 dark:bg-slate-500/30"
                aria-hidden="true"
            ></div>

            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: -10 },
                                { autoAlpha: 1, y: 0, duration: 0.7, ease: 'power2.out' },
                            )
                        })
                    }
                "
                class="font-bold"
            >
                <div class="relative">
                    <div
                        class="bg-gradient-to-br from-zinc-900 to-zinc-500 bg-clip-text text-5xl tracking-tighter text-transparent sm:text-6xl dark:from-white"
                    >
                        Thank
                    </div>

                    <div class="absolute -top-4 left-45">
                        <x-icons.star
                            x-init="
                                () => {
                                    gsap.to($el, {
                                        rotate: 180,
                                        duration: 3,
                                        repeat: -1,
                                        ease: 'linear',
                                    })
                                }
                            "
                            class="size-5 text-gray-600 dark:text-gray-300"
                            aria-hidden="true"
                        />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="bg-gradient-to-br from-purple-600 to-purple-300 bg-clip-text text-5xl tracking-tighter text-transparent sm:text-6xl dark:bg-gradient-to-t"
                    >
                        You!
                    </div>
                    <div
                        class="bg-gradient-to-br from-purple-500 to-purple-300 bg-clip-text text-lg leading-6 text-transparent dark:bg-gradient-to-tl"
                    >
                        <div>Early</div>
                        <div>Adopters</div>
                    </div>
                </div>
            </h1>

            <div class="mt-5 flex items-center justify-center gap-1">
                <div
                    class="size-2.5 rounded-sm bg-gradient-to-tl from-rose-400/70 to-rose-300/70"
                ></div>
                <div
                    class="size-2.5 rounded-sm bg-gradient-to-tl from-pink-400/70 to-pink-300/70"
                ></div>
                <div
                    class="size-2.5 rounded-sm bg-gradient-to-tl from-fuchsia-400/70 to-fuchsia-300/70"
                ></div>
                <div
                    class="size-2.5 rounded-sm bg-gradient-to-tl from-purple-400/70 to-purple-300/70"
                ></div>
                <div
                    class="size-2.5 rounded-sm bg-gradient-to-tl from-violet-400/70 to-violet-300/70"
                ></div>
                <div
                    class="size-2.5 rounded-sm bg-gradient-to-tl from-indigo-400/70 to-indigo-300/70"
                ></div>
            </div>

            {{-- Description --}}
            <p
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: 10 },
                                { autoAlpha: 1, y: 0, duration: 0.7, ease: 'power2.out' },
                            )
                        })
                    }
                "
                class="mx-auto mt-5 max-w-2xl text-center text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                Every great story starts with a small circle of believers. You
                stood with us at the beginning, and your support will always be
                part of the NativePHP story.
            </p>
        </header>
    </section>

    {{-- List --}}
    @php
        $earlyAdopters = [
            [
                'name' => 'Sara Johnson',
                'url' => 'https://example.com',
                'image' => 'https://i.pravatar.cc/300?img=31',
                'title' => 'Founder at Example Co',
            ],
            [
                'name' => 'Jane Smith',
                'image' => 'https://i.pravatar.cc/300?img=5',
                'title' => 'CEO at Example Inc',
                'featured' => true,
            ],
            [
                'name' => 'Alice Johnson',
                'url' => 'https://example.net',
                'image' => 'https://i.pravatar.cc/300?img=7',
            ],
            [
                'name' => 'Eve Wilson',
                'url' => 'https://example.edu',
                'image' => 'https://i.pravatar.cc/300?img=9',
            ],
            [
                'name' => 'Charlie Davis',
                'image' => 'https://i.pravatar.cc/300?img=10',
            ],
            [
                'name' => 'Bob Brown',
                'url' => 'https://example.io',
                'image' => 'https://i.pravatar.cc/300?img=11',
            ],
            [
                'name' => 'Frank Miller',
                'url' => 'https://example.dev',
                'image' => 'https://i.pravatar.cc/300?img=12',
            ],
            [
                'name' => 'Grace Lee',
                'image' => 'https://i.pravatar.cc/300?img=16',
            ],
            [
                'name' => 'Tara Adams',
                'url' => 'https://example.app',
                'image' => 'https://i.pravatar.cc/300?img=24',
            ],
            [
                'name' => 'Ivy Anderson',
                'url' => 'https://example.site',
                'image' => 'https://i.pravatar.cc/300?img=65',
            ],
            [
                'name' => 'Jack Thomas',
                'image' => 'https://i.pravatar.cc/300?img=32',
            ],
            [
                'name' => 'Kathy Martinez',
                'url' => 'https://example.tech',
                'image' => 'https://i.pravatar.cc/300?img=33',
            ],
        ];
    @endphp

    <div
        class="relative z-10 mt-10 grid grid-cols-[repeat(auto-fill,minmax(13rem,1fr))] items-start justify-center gap-7"
    >
        @foreach ($earlyAdopters as $adopter)
            <x-wall-of-love.early-adopter-card
                :name="$adopter['name']"
                :image="$adopter['image']"
                :url="$adopter['url'] ?? null"
                :title="$adopter['title'] ?? null"
                :featured="$adopter['featured'] ?? false"
            />
        @endforeach
    </div>
</x-layout>
