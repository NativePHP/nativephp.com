<x-newsletter-status title="You're in!">
    <x-slot:icon>
        <x-icons.party-popper
            class="size-8 text-emerald-600 dark:text-emerald-400"
            aria-hidden="true"
        />
    </x-slot>

    <p>
        Thanks for subscribing. Your
        <b>10% discount code</b>
        is on its way to your inbox right now.
    </p>

    <p>
        From here on you'll get NativePHP updates, release notes and the
        occasional behind-the-scenes story. No spam, ever.
    </p>

    <x-slot:actions>
        <a
            href="{{ route('pricing') }}"
            class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
        >
            Spend your discount
        </a>
    </x-slot>
</x-newsletter-status>
