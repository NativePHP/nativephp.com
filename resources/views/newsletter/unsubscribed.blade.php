<x-newsletter-status title="You've been unsubscribed">
    <x-slot:icon>
        <x-icons.close
            class="size-8 text-gray-400 dark:text-gray-500"
            aria-hidden="true"
        />
    </x-slot>

    <p>
        You're off the list and won't hear from us again. No hard feelings
        &mdash; thanks for having been along for the ride.
    </p>

    <p>
        Changed your mind? You can
        <button
            type="button"
            x-on:click="$dispatch('open-newsletter-modal')"
            class="font-medium underline underline-offset-4"
        >
            subscribe again
        </button>
        whenever you like.
    </p>

    <x-slot:actions>
        <a
            href="{{ route('blog') }}"
            class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
        >
            Read the blog instead
        </a>
    </x-slot>
</x-newsletter-status>
