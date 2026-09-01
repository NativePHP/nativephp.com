<x-newsletter-status title="You're already on the list">
    <x-slot:icon>
        <x-icons.checkmark
            class="size-8 text-emerald-600 dark:text-emerald-400"
            aria-hidden="true"
        />
    </x-slot>

    <p>
        That email address is already subscribed, so there's nothing more to do
        &mdash; you'll keep getting every NativePHP update as it lands.
    </p>

    <p>
        Need your
        <b>discount code</b>
        again? Search your inbox for our welcome email, or
        <a
            href="mailto:support@nativephp.com"
            class="font-medium underline underline-offset-4"
        >
            drop us a line
        </a>
        and we'll sort you out.
    </p>

    <x-slot:actions>
        <a
            href="{{ route('pricing') }}"
            class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
        >
            See what's on offer
        </a>
    </x-slot>
</x-newsletter-status>
