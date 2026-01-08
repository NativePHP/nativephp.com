@php
    $submissions = \App\Models\WallOfLoveSubmission::query()
        ->approved()
        ->promoted()
        ->latest()
        ->take(4)
        ->get();
@endphp

@if ($submissions->count() > 0)
    <section class="mt-12" aria-labelledby="feedback-title">
        <div class="text-center">
            <h2
                id="feedback-title"
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: 10 },
                                { autoAlpha: 1, y: 0, duration: 0.5, ease: 'power2.out' },
                            )
                        })
                    }
                "
                class="text-lg font-semibold text-gray-900 dark:text-white"
            >
                What developers are saying
            </h2>
            <p
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: 10 },
                                { autoAlpha: 1, y: 0, duration: 0.5, delay: 0.1, ease: 'power2.out' },
                            )
                        })
                    }
                "
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
            >
                From the <a href="{{ route('wall-of-love') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Wall of Love</a>
            </p>
        </div>

        <div class="mt-6 columns-1 gap-4 sm:columns-2 lg:columns-4">
            @foreach ($submissions as $submission)
                <div
                    x-init="
                        () => {
                            motion.inView($el, () => {
                                gsap.fromTo(
                                    $el,
                                    { autoAlpha: 0, y: 15 },
                                    { autoAlpha: 1, y: 0, duration: 0.4, delay: {{ $loop->index * 0.08 }}, ease: 'power2.out' },
                                )
                            })
                        }
                    "
                    class="mb-4 break-inside-avoid rounded-xl border border-gray-200/60 bg-white p-4 dark:border-slate-700/60 dark:bg-slate-800/50"
                >
                    {{-- Quote --}}
                    <blockquote class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                        "{{ $submission->promoted_testimonial ?? $submission->testimonial }}"
                    </blockquote>

                    {{-- Author --}}
                    <div class="mt-3 flex items-center gap-2.5">
                        @if ($submission->photo_path)
                            <img
                                src="{{ Storage::disk('public')->url($submission->photo_path) }}"
                                alt="{{ $submission->name }}"
                                class="size-7 rounded-full object-cover"
                                loading="lazy"
                            />
                        @else
                            <div class="grid size-7 place-items-center rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 text-xs font-medium text-white">
                                {{ substr($submission->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-xs font-medium text-gray-900 dark:text-white">
                                @if ($submission->url)
                                    <a
                                        href="{{ $submission->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="hover:text-indigo-600 dark:hover:text-indigo-400"
                                    >
                                        {{ $submission->name }}
                                    </a>
                                @else
                                    {{ $submission->name }}
                                @endif
                            </div>
                            @if ($submission->company)
                                <div class="truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ $submission->company }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
