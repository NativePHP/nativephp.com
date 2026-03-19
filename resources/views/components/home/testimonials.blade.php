@php
    $aaronQuotes = [
        [
            'text' => "It does sound insane... that's why I like it!",
            'url' => 'https://youtu.be/GuelLKsWwlc?t=1725',
        ],
        [
            'text' => "It's WILD!",
            'url' => null,
        ],
        [
            'text' => "Did everyone tell them it was crazy? Yes. Why? Because it's crazy! It's a crazy idea. Did they do it anyway? Yes. Did it work? Yes!",
            'url' => 'https://youtu.be/dgr-WAUgELw?t=565',
        ],
    ];

    $aaronQuote = $aaronQuotes[array_rand($aaronQuotes)];

    $testimonials = [
        [
            'name' => 'Aaron Francis',
            'role' => 'Developer & Educator',
            'image' => 'https://unavatar.io/twitter/aarondfrancis',
            'quote' => $aaronQuote['text'],
            'url' => $aaronQuote['url'],
        ],
        [
            'name' => 'Taylor Otwell',
            'role' => 'Creator of Laravel',
            'image' => 'https://unavatar.io/twitter/taylorotwell',
            'quote' => "I think it's super cool... it's just wild",
            'url' => 'https://youtu.be/JElNFR_efnM?t=3830',
        ],
        [
            'name' => 'Nuno Maduro',
            'role' => 'Creator of Pest & Laravel Core',
            'image' => 'https://unavatar.io/twitter/enunomaduro',
            'quote' => 'NativePHP FTW!',
            'url' => 'https://youtu.be/XkreP6Amwq0?t=384',
        ],
    ];
@endphp

<section class="mt-8" aria-labelledby="testimonials-title">
    <h2 id="testimonials-title" class="sr-only">What people are saying</h2>

    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($testimonials as $testimonial)
            <div
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: 10 },
                                { autoAlpha: 1, y: 0, duration: 0.5, delay: {{ $loop->index * 0.1 }}, ease: 'power2.out' },
                            )
                        })
                    }
                "
                class="relative flex flex-col rounded-2xl border border-gray-200/50 bg-gradient-to-b from-white to-gray-50/50 p-5 dark:border-slate-700/50 dark:from-slate-800/50 dark:to-slate-900/50"
            >
                {{-- Quote --}}
                <blockquote class="flex-1">
                    <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                        "{{ $testimonial['quote'] }}"
                    </p>
                </blockquote>

                {{-- Author --}}
                <div class="mt-4 flex items-center gap-3">
                    <img
                        src="{{ $testimonial['image'] }}"
                        alt="{{ $testimonial['name'] }}"
                        class="size-10 rounded-full object-cover"
                        loading="lazy"
                    />
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $testimonial['name'] }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $testimonial['role'] }}
                        </div>
                    </div>
                    @if ($testimonial['url'])
                        <a
                            href="{{ $testimonial['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="grid size-8 place-items-center rounded-full bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40"
                            aria-label="Watch {{ $testimonial['name'] }}'s video"
                        >
                            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
