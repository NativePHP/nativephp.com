<x-layout title="The Vibes - Partner Prospectus">
    <div class="mx-auto max-w-5xl px-6 py-16">

        {{-- ═══ HERO / COVER ═══ --}}
        <section class="mb-24 text-center">
            <p class="mb-4 text-sm font-medium tracking-wide text-gray-500 dark:text-gray-400">
                PARTNER PROSPECTUS &middot; 2026
            </p>

            <h1 class="text-5xl font-extrabold md:text-6xl lg:text-7xl">
                The
                <span class="bg-gradient-to-r from-violet-500 to-indigo-500 bg-clip-text text-transparent dark:from-violet-400 dark:to-indigo-400">Vibes</span>
            </h1>

            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                The First NativePHP In-Person Meetup
            </p>

            <div class="mt-6 inline-flex items-center gap-2 rounded-full bg-violet-100 px-5 py-2 text-sm font-medium text-violet-700 dark:bg-violet-500/15 dark:text-violet-300">
                July 30, 2026 &middot; Boston, MA &middot; 100 Developers
            </div>

            <p class="mt-8 text-sm text-gray-500 dark:text-gray-500">
                Loft on Two &middot; One Financial Center &middot; 9 AM – 4 PM
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                An intimate, community-powered gathering for the NativePHP community.
            </p>
            <p class="mt-4">
                <a href="{{ route('the-vibes') }}" class="inline-flex items-center gap-1 text-sm font-medium text-violet-500 hover:text-violet-400">
                    View the event page &rarr;
                </a>
            </p>
        </section>

        {{-- ═══ WHY PARTNER ═══ --}}
        <section class="mb-20">
            <h2 class="text-3xl font-bold md:text-4xl">
                Why Partner With
                <span class="bg-gradient-to-r from-violet-500 to-indigo-500 bg-clip-text text-transparent dark:from-violet-400 dark:to-indigo-400">The Vibes?</span>
            </h2>
            <div class="mt-2 h-1 w-32 rounded bg-violet-500"></div>

            <p class="mt-6 max-w-2xl text-gray-600 dark:text-gray-400">
                The Vibes is the first-ever in-person NativePHP meetup — a curated, single-day gathering
                for 100 passionate Laravel and PHP developers to connect, collaborate, and celebrate the
                community. It's intimate, it's exclusive, and it's the perfect environment to get in front
                of engaged developers.
            </p>

            {{-- Stats --}}
            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-violet-500 dark:text-violet-400">100</span>
                        <span class="font-semibold">Developers</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">Intimate &amp; exclusive. Real conversations, not small talk.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-violet-500 dark:text-violet-400">1</span>
                        <span class="font-semibold">Day Event</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">Focused, high-energy, and designed for meaningful connection.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-violet-500 dark:text-violet-400">1st</span>
                        <span class="font-semibold">Ever</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">The inaugural event. Be part of something from the start.</p>
                </div>
            </div>

            {{-- Who Attends --}}
            <h3 class="mt-12 text-xl font-bold">Who Attends</h3>
            <div class="mt-1 h-0.5 w-24 rounded bg-violet-500"></div>
            <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-400">
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                    Senior Laravel &amp; PHP developers
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                    Engineering leads and CTOs from startups to enterprise
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                    Open-source maintainers and contributors
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                    Agency owners and freelance developers
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                    Developer advocates and community builders
                </li>
            </ul>

            {{-- What's Included --}}
            <h3 class="mt-12 text-xl font-bold">What's Included for Attendees</h3>
            <div class="mt-1 h-0.5 w-48 rounded bg-violet-500"></div>
            <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-400">
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-emerald-500"></span>
                    Catered local Boston food, snacks &amp; drinks all day
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-emerald-500"></span>
                    Community networking in an intimate 100-person setting
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-emerald-500"></span>
                    Open collaboration space for hacking and pairing
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-emerald-500"></span>
                    Sponsor surprises and exclusive giveaways
                </li>
            </ul>
        </section>

        {{-- ═══ PARTNERSHIP TIERS ═══ --}}
        <section class="mb-20">
            <h2 class="text-3xl font-bold md:text-4xl">Partnership Tiers</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Choose the level that fits your goals. Limited spots ensure maximum visibility.</p>
            <div class="mt-2 h-1 w-40 rounded bg-violet-500"></div>

            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                {{-- Headline Partner --}}
                <div class="rounded-2xl border-2 border-yellow-400/60 bg-gray-50 p-6 dark:bg-white/5">
                    <p class="text-center text-xs font-bold uppercase tracking-widest text-yellow-500">Headline Partner</p>
                    <p class="mt-3 text-center text-4xl font-extrabold">$1,000</p>
                    <div class="mx-auto mt-3 w-fit rounded-full bg-yellow-500/15 px-4 py-1 text-xs font-medium text-yellow-500">5 spots</div>

                    <hr class="my-5 border-gray-200 dark:border-white/10">

                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-yellow-500">✓</span>
                            5 event tickets included
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-yellow-500">✓</span>
                            Logo on The Vibes website
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-yellow-500">✓</span>
                            Logo in The Vibes newsletter
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-yellow-500">✓</span>
                            Premium brand visibility
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-yellow-500">✓</span>
                            First pick of table/booth space
                        </li>
                    </ul>
                </div>

                {{-- Supporting Partner --}}
                <div class="rounded-2xl border-2 border-violet-500/40 bg-gray-50 p-6 dark:bg-white/5">
                    <p class="text-center text-xs font-bold uppercase tracking-widest text-violet-500 dark:text-violet-400">Supporting Partner</p>
                    <p class="mt-3 text-center text-4xl font-extrabold">$500</p>
                    <div class="mx-auto mt-3 w-fit rounded-full bg-violet-500/15 px-4 py-1 text-xs font-medium text-violet-500 dark:text-violet-400">10 spots</div>

                    <hr class="my-5 border-gray-200 dark:border-white/10">

                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-violet-500">✓</span>
                            2 event tickets included
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-violet-500">✓</span>
                            Logo on The Vibes website
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-violet-500">✓</span>
                            Logo in The Vibes newsletter
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-violet-500">✓</span>
                            Community recognition
                        </li>
                    </ul>
                </div>

                {{-- Community Partner --}}
                <div class="rounded-2xl border-2 border-emerald-500/40 bg-gray-50 p-6 dark:bg-white/5">
                    <p class="text-center text-xs font-bold uppercase tracking-widest text-emerald-500">Community Partner</p>
                    <p class="mt-3 text-center text-4xl font-extrabold">$250</p>
                    <div class="mx-auto mt-3 w-fit rounded-full bg-emerald-500/15 px-4 py-1 text-xs font-medium text-emerald-500">20 spots</div>

                    <hr class="my-5 border-gray-200 dark:border-white/10">

                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-emerald-500">✓</span>
                            1 event ticket included
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-emerald-500">✓</span>
                            Logo on The Vibes website
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-emerald-500">✓</span>
                            Support the community
                        </li>
                    </ul>
                </div>
            </div>

            {{-- All Partners --}}
            <div class="mt-10">
                <h3 class="text-lg font-bold">All Partners Receive:</h3>
                <ul class="mt-3 space-y-2 text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-3">
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                        Recognition during The Vibes event day
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                        Direct access to 100 engaged Laravel/PHP developers
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-violet-500"></span>
                        Association with the NativePHP and Laravel community
                    </li>
                </ul>
            </div>
        </section>

        {{-- ═══ CONTACT / CTA ═══ --}}
        <section class="mb-16 text-center">
            <h2 class="text-3xl font-bold md:text-4xl">Let's Make</h2>
            <p class="text-3xl font-bold text-violet-500 dark:text-violet-400 md:text-4xl">The Vibes Happen</p>
            <div class="mx-auto mt-2 h-1 w-24 rounded bg-violet-500"></div>

            <p class="mx-auto mt-6 max-w-lg text-gray-600 dark:text-gray-400">
                Your partnership helps us bring together 100 passionate developers
                for an unforgettable day of community, collaboration, and good vibes.
            </p>

            {{-- Contact Card --}}
            <div class="mx-auto mt-8 max-w-sm rounded-2xl border-2 border-violet-500/40 bg-gray-50 p-8 text-left dark:bg-white/5">
                <h3 class="mb-4 text-center text-lg font-bold">Get In Touch</h3>

                <p class="text-xs font-bold uppercase tracking-wide text-violet-500 dark:text-violet-400">Email</p>
                <p class="mb-4">
                    <a href="mailto:sponsors@nativephp.com" class="text-gray-700 hover:text-violet-500 dark:text-gray-300 dark:hover:text-violet-400">sponsors@nativephp.com</a>
                </p>

                <p class="text-xs font-bold uppercase tracking-wide text-violet-500 dark:text-violet-400">Event Page</p>
                <p>
                    <a href="{{ route('the-vibes') }}" class="text-gray-700 hover:text-violet-500 dark:text-gray-300 dark:hover:text-violet-400">nativephp.com/the-vibes</a>
                </p>
            </div>

            {{-- Quick Overview Table --}}
            <div class="mx-auto mt-10 max-w-xl">
                <h3 class="mb-4 text-lg font-bold">Partnership Overview</h3>
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        <tr>
                            <td class="py-3 font-semibold">Headline Partner</td>
                            <td class="py-3 font-semibold text-violet-500 dark:text-violet-400">$1,000</td>
                            <td class="py-3 text-gray-500">5 spots</td>
                            <td class="py-3 text-gray-500">5 tickets + logo on site &amp; newsletter</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-semibold">Supporting Partner</td>
                            <td class="py-3 font-semibold text-violet-500 dark:text-violet-400">$500</td>
                            <td class="py-3 text-gray-500">10 spots</td>
                            <td class="py-3 text-gray-500">2 tickets + logo on site &amp; newsletter</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-semibold">Community Partner</td>
                            <td class="py-3 font-semibold text-violet-500 dark:text-violet-400">$250</td>
                            <td class="py-3 text-gray-500">20 spots</td>
                            <td class="py-3 text-gray-500">1 ticket + logo on site</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-12 text-xs text-gray-400 dark:text-gray-600">
                NativePHP &middot; The Vibes 2026 &middot; Presented by Bifrost Technology, LLC
            </p>
        </section>

    </div>
</x-layout>
