@props(['showcase'])

<article
    x-data="{
        currentSlide: 0,
        screenshots: {{ json_encode($showcase->screenshots ?? []) }},
        get hasScreenshots() {
            return this.screenshots && this.screenshots.length > 0
        },
        get totalSlides() {
            return this.screenshots ? this.screenshots.length : 0
        },
        nextSlide() {
            if (this.currentSlide < this.totalSlides - 1) {
                this.currentSlide++
            }
        },
        prevSlide() {
            if (this.currentSlide > 0) {
                this.currentSlide--
            }
        }
    }"
    class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white transition-all hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
>
    {{-- NEW Badge --}}
    @if($showcase->isNew())
        <div class="absolute top-4 left-4 z-20">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-400 to-emerald-500 text-white shadow-lg">
                NEW
            </span>
        </div>
    @endif

    {{-- Screenshot Carousel --}}
    <div class="relative aspect-video bg-gray-100 dark:bg-gray-900 overflow-hidden">
        @if($showcase->screenshots && count($showcase->screenshots) > 0)
            <div class="relative h-full">
                @foreach($showcase->screenshots as $index => $screenshot)
                    <img
                        x-show="currentSlide === {{ $index }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        src="{{ Storage::disk('public')->url($screenshot) }}"
                        alt="{{ $showcase->title }} screenshot {{ $index + 1 }}"
                        class="absolute inset-0 w-full h-full object-contain"
                    >
                @endforeach

                {{-- Navigation Arrows --}}
                <template x-if="totalSlides > 1">
                    <div>
                        <button
                            x-show="currentSlide > 0"
                            @click.prevent="prevSlide()"
                            class="absolute left-2 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors"
                            aria-label="Previous screenshot"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            x-show="currentSlide < totalSlides - 1"
                            @click.prevent="nextSlide()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors"
                            aria-label="Next screenshot"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Slide Indicators --}}
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                            <template x-for="(_, idx) in screenshots" :key="idx">
                                <button
                                    @click.prevent="currentSlide = idx"
                                    :class="currentSlide === idx ? 'bg-white' : 'bg-white/50'"
                                    class="w-2 h-2 rounded-full transition-colors"
                                    :aria-label="'Go to screenshot ' + (idx + 1)"
                                ></button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        @elseif($showcase->image)
            <img
                src="{{ Storage::disk('public')->url($showcase->image) }}"
                alt="{{ $showcase->title }}"
                class="w-full h-full object-contain"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-6">
        {{-- Header with icon and title --}}
        <div class="flex items-start gap-4">
            @if($showcase->image)
                <img
                    src="{{ Storage::disk('public')->url($showcase->image) }}"
                    alt="{{ $showcase->title }} icon"
                    class="w-14 h-14 rounded-xl object-cover shrink-0 shadow-sm"
                >
            @else
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shrink-0">
                    <span class="text-white text-xl font-bold">{{ substr($showcase->title, 0, 1) }}</span>
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                    {{ $showcase->title }}
                </h3>

                {{-- Platform badges --}}
                <div class="flex items-center gap-2 mt-1">
                    @if($showcase->has_mobile)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Mobile
                        </span>
                    @endif
                    @if($showcase->has_desktop)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Desktop
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Description --}}
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm line-clamp-3">
            {{ $showcase->description }}
        </p>

        {{-- Download/Store Links --}}
        <div class="mt-6 flex flex-wrap gap-2">
            @if($showcase->has_mobile)
                @if($showcase->app_store_url)
                    <a
                        href="{{ $showcase->app_store_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-colors dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                    >
                        <x-icons.app-store class="w-4 h-4" />
                        App Store
                    </a>
                @endif
                @if($showcase->play_store_url)
                    <a
                        href="{{ $showcase->play_store_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-colors dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                    >
                        <x-icons.play-store class="w-4 h-4" />
                        Play Store
                    </a>
                @endif
            @endif

            @if($showcase->has_desktop)
                @if($showcase->macos_download_url)
                    <a
                        href="{{ $showcase->macos_download_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                        macOS
                    </a>
                @endif
                @if($showcase->windows_download_url)
                    <a
                        href="{{ $showcase->windows_download_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 12V6.75l6-1.32v6.48L3 12zm6.73.02l-.01 6.52L3 17.25v-5.2l6.73-.03zM10.5 5.25l9-2v7.52l-9 .03V5.25zm0 8.02l9 .03v7.45l-9-2V13.27z"/></svg>
                        Windows
                    </a>
                @endif
                @if($showcase->linux_download_url)
                    <a
                        href="{{ $showcase->linux_download_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.504 0c-.155 0-.315.008-.48.021-4.226.333-3.105 4.807-3.17 6.298-.076 1.092-.3 1.953-1.05 3.02-.885 1.051-2.127 2.75-2.716 4.521-.278.832-.41 1.684-.287 2.489a.424.424 0 00-.11.135c-.26.268-.45.6-.663.839-.199.199-.485.267-.797.4-.313.136-.658.269-.864.68-.09.189-.136.394-.132.602 0 .199.027.4.055.536.058.399.116.728.04.97-.249.68-.28 1.145-.106 1.484.174.334.535.47.94.601.81.2 1.91.135 2.774.6.926.466 1.866.67 2.616.47.526-.116.97-.464 1.208-.946.587-.003 1.23-.269 2.26-.334.699-.058 1.574.267 2.577.2.025.134.063.198.114.333l.003.003c.391.778 1.113 1.132 1.884 1.071.771-.06 1.592-.536 2.257-1.306.631-.765 1.683-1.084 2.378-1.503.348-.199.629-.469.649-.853.023-.4-.2-.811-.714-1.376v-.097l-.003-.003c-.17-.2-.25-.535-.338-.926-.085-.401-.182-.786-.492-1.046h-.003c-.059-.054-.123-.067-.188-.135a.357.357 0 00-.19-.064c.431-1.278.264-2.55-.173-3.694-.533-1.41-1.465-2.638-2.175-3.483-.796-1.005-1.576-1.957-1.56-3.368.026-2.152.236-6.133-3.544-6.139zm.529 3.405h.013c.213 0 .396.062.584.198.19.135.33.332.438.533.105.259.158.459.166.724 0-.02.006-.04.006-.06v.105a.086.086 0 01-.004-.021l-.004-.024a1.807 1.807 0 01-.15.706.953.953 0 01-.213.335.71.71 0 00-.088-.042c-.104-.045-.198-.064-.284-.133a1.312 1.312 0 00-.22-.066c.05-.06.146-.133.183-.198.053-.128.082-.264.088-.402v-.02a1.21 1.21 0 00-.061-.4c-.045-.134-.101-.2-.183-.333-.084-.066-.167-.132-.267-.132h-.016c-.093 0-.176.03-.262.132a.8.8 0 00-.205.334 1.18 1.18 0 00-.09.4v.019c.002.089.008.179.02.267-.193-.067-.438-.135-.607-.202a1.635 1.635 0 01-.018-.2v-.02a1.772 1.772 0 01.15-.768c.082-.22.232-.406.43-.533a.985.985 0 01.594-.2zm-2.962.059h.036c.142 0 .27.048.399.135.146.129.264.288.344.465.09.199.14.4.153.667v.004c.007.134.006.2-.002.266v.08c-.03.007-.056.018-.083.024-.152.055-.274.135-.393.2.012-.09.013-.18.003-.267v-.015c-.012-.133-.04-.2-.082-.333a.613.613 0 00-.166-.267.248.248 0 00-.183-.064h-.021c-.071.006-.13.04-.186.132a.552.552 0 00-.12.27.944.944 0 00-.023.33v.015c.012.135.037.2.08.334.046.134.098.2.166.268.01.009.02.018.034.024-.07.057-.117.07-.176.136a.304.304 0 01-.131.068 2.62 2.62 0 01-.275-.402 1.772 1.772 0 01-.155-.667 1.759 1.759 0 01.08-.668 1.43 1.43 0 01.283-.535c.128-.133.26-.2.418-.2zm1.37 1.706c.332 0 .733.065 1.216.399.293.2.523.269 1.052.468h.003c.255.136.405.266.478.399v-.131a.571.571 0 01.016.47c-.123.31-.516.643-1.063.842v.002c-.268.135-.501.333-.775.465-.276.135-.588.292-1.012.267a1.139 1.139 0 01-.448-.067 3.566 3.566 0 01-.322-.198c-.195-.135-.363-.332-.612-.465v-.005h-.005c-.4-.246-.616-.512-.686-.71-.07-.268-.005-.47.193-.6.224-.135.38-.271.483-.336.104-.074.143-.102.176-.131h.002v-.003c.169-.202.436-.47.839-.601.139-.036.294-.065.466-.065zm2.8 2.142c.358 1.417 1.196 3.475 1.735 4.473.286.534.855 1.659 1.102 3.024.156-.005.33.018.513.064.646-1.671-.546-3.467-1.089-3.966-.22-.2-.232-.335-.123-.335.59.534 1.365 1.572 1.646 2.757.13.535.16 1.104.021 1.67.067.028.135.06.205.067 1.032.534 1.413.938 1.23 1.537v-.043c-.06-.003-.12 0-.18 0h-.016c.151-.467-.182-.825-1.065-1.224-.915-.4-1.646-.336-1.77.465-.008.043-.013.066-.018.135-.068.023-.139.053-.209.064-.43.268-.662.669-.793 1.187-.13.533-.17 1.156-.205 1.869v.003c-.02.334-.17.838-.319 1.35-1.5 1.072-3.58 1.538-5.348.334a2.645 2.645 0 00-.402-.533 1.45 1.45 0 00-.275-.333c.182 0 .338-.03.465-.067a.615.615 0 00.35-.2.807.807 0 00.15-.267c.04-.133.06-.2.065-.267v-.005c.135-.6.455-1.2.91-1.667.516-.534 1.088-.931 1.942-.931.614 0 1.168.2 1.644.535l-.015-.067c-.074-.267-.229-.666-.418-.934a1.678 1.678 0 00-.571-.467 2.29 2.29 0 00-.726-.2c-.65-.067-1.29.066-1.89.4-.6.333-1.13.734-1.704 1.134l-.035.066v-.135a.982.982 0 01-.015-.333c.024-.135.06-.267.108-.398v-.002a14.618 14.618 0 01.205-.6c.055-.135.12-.267.18-.398.046-.066.085-.135.135-.2.07-.066.14-.135.22-.198.08-.066.155-.135.245-.198.25-.2.488-.374.713-.534.236-.134.484-.266.75-.398h.002c.268-.135.531-.267.785-.4.269-.135.48-.266.72-.465.112-.066.225-.2.37-.332.145-.135.282-.267.39-.465.148-.135.234-.334.293-.535.06-.2.095-.4.108-.667v-.067c.033.2.112.466.217.666.132.268.268.534.42.8.157.2.343.465.541.6.199.2.398.332.596.468.66.4 1.308.668 1.882.868z"/></svg>
                        Linux
                    </a>
                @endif
            @endif
        </div>
    </div>
</article>
