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
        // Get approved submissions
        $approvedSubmissions = App\Models\WallOfLoveSubmission::whereNotNull('approved_at')
            ->inRandomOrder()
            ->get();

        // Convert approved submissions to the format expected by the component
        $earlyAdopters = $approvedSubmissions->map(function ($submission) {
            return [
                'name' => $submission->name,
                'title' => $submission->company,
                'url' => $submission->url,
                'image' => $submission->photo_path
                    ? asset('storage/' . $submission->photo_path)
                    : 'https://avatars.laravel.cloud/' . rand(1, 70) . '?vibe=' . array_rand(['ocean', 'crystal', 'bubble', 'forest', 'sunset']),
                'featured' => rand(0, 4) === 0, // Randomly feature about 20% of submissions
                'testimonial' => $submission->testimonial,
            ];
        })->toArray();
    @endphp

    @if(count($earlyAdopters) > 0)
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        const children = Array.from($el.children)

                        children.forEach((child, i) => {
                            const range = 20 // px
                            const xFrom = (Math.random() * 2 - 1) * range
                            const yFrom = (Math.random() * 2 - 1) * range

                            motion.animate(
                                child,
                                {
                                    x: [xFrom, 0],
                                    y: [yFrom, 0],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: i * 0.06,
                                },
                            )
                        })
                    })
                }
            "
            class="relative z-10 mt-10 grid place-items-center 2xs:block 2xs:columns-[10rem] xl:columns-[12rem]"
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
    @else
        <div class="relative z-10 mt-10 text-center">
            <div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-8 mx-auto max-w-md border border-gray-200 dark:border-gray-700">
                <div class="text-6xl mb-4">🚀</div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Coming Soon!
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Our early adopters will appear here soon.
                </p>
            </div>
        </div>
    @endif
</x-layout>
