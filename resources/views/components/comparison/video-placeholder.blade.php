@props([
    'title' => 'App Boot Demo',
    'description' => 'Video coming soon',
])

<div
    {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl bg-gray-100 dark:bg-mirage']) }}
>
    <div class="aspect-video">
        {{-- Video slot for when videos are ready --}}
        @if (isset($video))
            {{ $video }}
        @else
            {{-- Placeholder state --}}
            <div class="flex h-full flex-col items-center justify-center gap-4 p-8 text-center">
                {{-- Play button icon --}}
                <div class="grid size-16 place-items-center rounded-full bg-gray-200 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-8"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div>
                    <div class="font-medium text-gray-700 dark:text-gray-300">
                        {{ $title }}
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $description }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
