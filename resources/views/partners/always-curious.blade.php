<x-partner-profile partnerName="Always Curious" tagline="Strategy-led software. Native apps from the Laravel stack you already trust.">
    <x-slot name="logo">
        <img
            src="/img/sponsors/always-curious.svg"
            class="h-16 w-auto"
            alt="Always Curious logo"
        />
    </x-slot>

    <x-slot name="introduction">
        <p class="text-pretty">
            Always Curious is a technology consultancy based in Richmond, Virginia. They work across software strategy, platform architecture, and creative design for teams in fintech, field service, aerospace, nonprofit, and live events.
        </p>
        <p class="mt-4 text-pretty">
            Every engagement starts with one question: <strong class="font-semibold text-gray-900 dark:text-white">what's the simplest path that will still be the right answer in three years?</strong> NativePHP is often that answer. It lets their clients turn proven Laravel codebases and teams into real native apps without standing up a second stack.
        </p>
    </x-slot>

    <x-slot name="featuredProject">
        <h3 class="text-xl font-semibold md:text-2xl">
            Featured Project: Event Ticket Scanner
        </h3>

        <div class="mt-4 space-y-4 text-gray-600 dark:text-zinc-400">
            <p>
                <a href="https://eventticketscanner.com" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-600 hover:underline dark:text-blue-400">eventticketscanner.com</a>
            </p>

            <p>
                Door check-in for Event Tickets (The Events Calendar plugin), built entirely in Laravel with NativePHP for Mobile v4. Point the camera at a ticket and get an unmistakable full-screen answer: <strong class="font-semibold text-green-600 dark:text-green-400">green</strong> for admit, <strong class="font-semibold text-amber-600 dark:text-amber-400">amber</strong> for already checked in (with who and when), <strong class="font-semibold text-red-600 dark:text-red-400">red</strong> for invalid, each with its own haptic buzz. No Wi-Fi at the venue? It keeps scanning with offline mode.
            </p>

            <ul class="list-inside list-disc space-y-2">
                <li>
                    <strong class="font-semibold text-gray-900 dark:text-white">Truly native UI:</strong> screens are rendered with NativePHP's EDGE components (native top bar, tabs, lists and dialogs), not a webview.
                </li>
                <li>
                    <strong class="font-semibold text-gray-900 dark:text-white">Native hardware from PHP:</strong> continuous QR scanning, haptics, and keychain-backed credential storage through NativePHP facades.
                </li>
                <li>
                    <strong class="font-semibold text-gray-900 dark:text-white">Pair in one scan:</strong> scan a QR code in WordPress admin to connect a device. No passwords typed at the door.
                </li>
                <li>
                    <strong class="font-semibold text-gray-900 dark:text-white">Offline-first:</strong> attendees live on-device and every check-in is recorded locally, queued, and synced when the connection returns.
                </li>
                <li>
                    <strong class="font-semibold text-gray-900 dark:text-white">More than a scanner:</strong> attendee search, manual check-in and undo, and live event stats.
                </li>
            </ul>

            <p>
                <strong class="font-semibold text-gray-900 dark:text-white">Stack:</strong> Laravel, NativePHP Mobile v4 (SuperNative), SQLite, and a companion WordPress plugin sharing one OpenAPI contract.
            </p>
        </div>
    </x-slot>

    <x-slot name="whatWeBuild">
        <ul class="list-inside list-disc space-y-2">
            <li><strong class="font-semibold text-gray-900 dark:text-white">Native desktop and mobile apps</strong> with NativePHP</li>
            <li><strong class="font-semibold text-gray-900 dark:text-white">Laravel platforms</strong> with Livewire, Filament, and Flux UI</li>
            <li><strong class="font-semibold text-gray-900 dark:text-white">Payments and fintech architecture</strong> informed by years building real-time transaction platforms in the industry</li>
            <li><strong class="font-semibold text-gray-900 dark:text-white">Omnichannel and operations tooling</strong> for field service, manufacturing, and events</li>
        </ul>
    </x-slot>

    <x-slot name="whyWorkWithUs">
        <ul class="list-inside list-disc space-y-2">
            <li><strong class="font-semibold text-gray-900 dark:text-white">Senior, hands-on architecture.</strong> Teams work directly with the designers and builders of their system.</li>
            <li><strong class="font-semibold text-gray-900 dark:text-white">Deep Laravel and PHP roots.</strong> WordPress contributors since 1.0.1, commercial plugin authors, and long-time Laravel practitioners and community sponsors.</li>
            <li><strong class="font-semibold text-gray-900 dark:text-white">Built for the long run.</strong> Secure, maintainable systems on Cloud with deep AI-Native integrations that teams can own.</li>
        </ul>
    </x-slot>

    <x-slot name="contact">
        <p class="mb-6">
            Always Curious partners with teams who need a Laravel app to go native, or an idea that needs a partner who operates beyond launch.
        </p>

        <div class="space-y-4">
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">
                    Tim Wood
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Co-Founder and Principal Architect
                </div>
                <a href="mailto:tim@alwayscurious.co" class="text-blue-600 hover:underline dark:text-blue-400">
                    tim@alwayscurious.co
                </a>
            </div>

            <div>
                <div class="font-semibold text-gray-900 dark:text-white">
                    Laurie Wood
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Co-Founder and Chief Strategy Officer
                </div>
                <a href="mailto:laurie@alwayscurious.co" class="text-blue-600 hover:underline dark:text-blue-400">
                    laurie@alwayscurious.co
                </a>
            </div>

            <div class="pt-4">
                <a
                    href="https://alwayscurious.co/we-love-nativephp"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-xl bg-zinc-800 px-6 py-3 text-sm font-medium text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                >
                    Visit Always Curious
                    <x-icons.right-arrow class="size-3" />
                </a>
            </div>
        </div>
    </x-slot>
</x-partner-profile>
