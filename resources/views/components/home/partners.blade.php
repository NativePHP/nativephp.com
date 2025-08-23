<section
    class="mt-5"
    aria-labelledby="sponsors-title"
>
    <div class="dark:bg-mirage rounded-2xl bg-gray-200/60 p-8 md:p-10">
        <div class="flex flex-col gap-1">
            <h2
                id="sponsors-title"
                class="text-2xl font-bold text-gray-800 lg:text-3xl dark:text-white"
            >
                Our Partners
            </h2>
            <h3 class="text-lg text-gray-600 lg:text-xl dark:text-zinc-400">
                NativePHP wouldn't be possible without amazing Partners
            </h3>
        </div>

        <div class="mt-7 flex flex-wrap gap-5 lg:flex-nowrap">
            {{-- Featured partners --}}
            <div
                class="grid w-full grid-cols-[repeat(auto-fill,minmax(15rem,1fr))] gap-5 lg:w-auto lg:grid-cols-1"
                aria-label="Featured partners of the NativePHP project"
            >
                <x-home.featured-partner-card
                    partnerName="BeyondCode"
                    tagline="Essential tools for web developers"
                    href="https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp"
                >
                    <x-slot:logo>
                        <img
                            src="/img/sponsors/beyondcode.webp"
                            class="block dark:hidden"
                            loading="lazy"
                            alt="BeyondCode logo - PHP development tools and packages"
                            width="160"
                            height="40"
                        />
                        <img
                            src="/img/sponsors/beyondcode-dark.webp"
                            class="hidden dark:block"
                            loading="lazy"
                            alt="BeyondCode logo - PHP development tools and packages"
                            width="160"
                            height="40"
                        />
                    </x-slot>

                    <x-slot:description>
                        From local full stack development to cutting-edge AI
                        platforms, we provide the tools for building your next
                        great app.
                    </x-slot>
                </x-home.featured-partner-card>
                <x-home.featured-partner-card
                    partnerName="Laradevs"
                    tagline="Hire the best Laravel developers anywhere"
                    href="https://laradevs.com/?ref=nativephp"
                >
                    <x-slot:logo>
                        <x-sponsors.logos.laradevs
                            class="text-black dark:text-white"
                            aria-hidden="true"
                        />
                    </x-slot>

                    <x-slot:description>
                        Need a freelancer or engineer? Laradevs has you covered.
                        Filter by skills, experience, location, availability,
                        and pay.
                    </x-slot>
                </x-home.featured-partner-card>
                <x-home.featured-partner-card
                    partnerName="Nexcalia"
                    tagline="Hire the best Laravel developers anywhere"
                    href="https://www.nexcalia.com/?ref=nativephp"
                >
                    <x-slot:logo>
                        <x-sponsors.logos.nexcalia
                            class="text-black dark:text-white"
                            aria-hidden="true"
                        />
                    </x-slot>

                    <x-slot:description>
                        Need a freelancer or engineer? Laradevs has you covered.
                        Filter by skills, experience, location, availability,
                        and pay.
                    </x-slot>
                </x-home.featured-partner-card>
            </div>

            {{-- Right side --}}
            <div
                class="flex w-full flex-wrap items-start gap-5 lg:w-1/2 lg:flex-col lg:items-stretch lg:justify-start"
            >
                {{-- Become a partner button --}}
                <a
                    href="/docs/getting-started/sponsoring"
                    aria-label="Learn about sponsoring the NativePHP project"
                    class="flex items-center gap-5 rounded-xl bg-gradient-to-t from-gray-800 to-gray-950 p-6 text-indigo-100"
                >
                    <div class="flex flex-col gap-1">
                        <div class="">
                            Get more from NativePHP as a partner!
                        </div>
                        <p class="text-sm opacity-70">
                            Our Partners are helping us bring NativePHP to
                            everyone and getting some incredible benefits to
                            boot
                        </p>
                    </div>
                </a>

                {{-- Partners list --}}
                <div
                    class="grid w-full grid-cols-[repeat(auto-fill,minmax(10rem,1fr))] gap-3.5 lg:w-auto lg:grid-cols-1 xl:grid-cols-2"
                    aria-label="Partners of the NativePHP project"
                >
                    <x-home.partner-card
                        partnerName="RedGalaxy"
                        href="https://www.redgalaxy.co.uk/"
                    >
                        <x-sponsors.logos.redgalaxy
                            alt="RedGalaxy logo"
                            loading="lazy"
                        />
                    </x-home.partner-card>
                    <x-home.partner-card
                        partnerName="Sevalla"
                        href="https://sevalla.com/?utm_source=nativephp&utm_medium=Referral&utm_campaign=homepage"
                    >
                        <x-sponsors.logos.sevalla
                            class="text-black dark:text-white"
                            alt="Sevalla logo"
                            loading="lazy"
                        />
                    </x-home.partner-card>
                    <x-home.partner-card
                        partnerName="KaasHosting"
                        href="https://www.kaashosting.nl/?lang=en"
                    >
                        <x-sponsors.logos.kaashosting
                            class="fill-[#042340] dark:fill-white"
                            alt="KaasHosting logo"
                            loading="lazy"
                        />
                    </x-home.partner-card>
                    <x-home.partner-card
                        partnerName="Quantumweb"
                        href="https://www.quantumweb.co/"
                    >
                        <x-sponsors.logos.quantumweb
                            class="fill-[#042340] dark:fill-white"
                            alt="Quantumweb logo"
                            loading="lazy"
                        />
                    </x-home.partner-card>
                </div>
            </div>
        </div>
    </div>
</section>
