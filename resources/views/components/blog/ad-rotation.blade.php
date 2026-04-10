@props(['ads' => ['mobile', 'devkit', 'ultra', 'vibes', 'masterclass']])

@php
    $adsJson = json_encode($ads);
@endphp

<div
    x-data="{ ad: {{ $adsJson }}[Math.floor(Math.random() * {{ count($ads) }})] }"
    {{ $attributes }}
>
    {{-- NativePHP Mobile Ad --}}
    @if (in_array('mobile', $ads))
        <a
            x-show="ad === 'mobile'"
            x-cloak
            href="/docs/mobile"
            class="group relative z-0 grid place-items-center overflow-hidden rounded-2xl bg-gray-100 px-4 pt-10 text-center text-pretty transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
        >
            {{-- Logo --}}
            <div>
                <x-logo class="h-5" />
                <span class="sr-only">NativePHP</span>
            </div>

            {{-- Tagline --}}
            <div class="mt-3">
                Bring your
                <strong>Laravel</strong>
                skills to
                <strong>mobile apps.</strong>
            </div>

            {{-- Iphone --}}
            <div class="mt-4 -mb-25">
                <img
                    src="{{ Vite::asset('resources/images/home/iphone.webp') }}"
                    alt=""
                    aria-hidden="true"
                    class="w-25 transition duration-200 will-change-transform group-hover:-translate-y-1 dark:brightness-80 dark:contrast-150"
                    width="92"
                    height="190"
                    loading="lazy"
                />
            </div>

            {{-- Star 1 --}}
            <x-icons.star
                class="absolute top-6 right-3 z-10 w-4 -rotate-7 text-white dark:w-3 dark:text-slate-300 animate-ping"
            />
            {{-- Star 2 --}}
            <x-icons.star
                class="absolute top-3 right-14 z-10 w-3 rotate-5 text-white dark:w-2 dark:text-slate-300 animate-spin "
            />
            {{-- Star 3 --}}
            <x-icons.star
                class="absolute top-2.5 right-7.5 z-10 w-2.5 text-white dark:w-2 dark:text-slate-300 animate-pulse"
            />
            {{-- White blur --}}
            <div class="absolute top-5 -right-10 -z-5">
                <div
                    class="h-5 w-36 rotate-30 rounded-full bg-white/80 blur-md dark:bg-white/5"
                ></div>
            </div>
            {{-- Sky blur --}}
            <div class="absolute top-5 -right-20 -z-10">
                <div
                    class="h-15 w-36 rotate-30 rounded-full bg-sky-300 blur-xl dark:bg-sky-500/30"
                ></div>
            </div>
            {{-- Violet blur --}}
            <div class="absolute -top-10 -right-5 -z-10">
                <div
                    class="h-15 w-36 rotate-30 rounded-full bg-violet-300 blur-xl dark:bg-violet-400/30"
                ></div>
            </div>
        </a>
    @endif

    {{-- Plugin Dev Kit Ad --}}
    @if (in_array('devkit', $ads))
        <a
            x-show="ad === 'devkit'"
            x-cloak
            href="{{ route('products.show', 'plugin-dev-kit') }}"
            class="group relative z-0 grid place-items-center overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-700 px-4 py-8 text-center text-pretty transition duration-200 hover:from-purple-500 hover:to-indigo-600 hover:ring-1 hover:ring-purple-400"
        >
            {{-- Icon --}}
            <div class="grid size-14 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <x-heroicon-s-cube class="size-8" />
            </div>

            {{-- Title --}}
            <div class="mt-3 text-lg font-bold text-white">
                Plugin Dev Kit
            </div>

            {{-- Tagline --}}
            <div class="mt-2 text-sm text-purple-100">
                Build native plugins with
                <strong class="text-white">Claude Code</strong>
            </div>

            {{-- CTA --}}
            <div class="mt-4 rounded-lg bg-white/20 px-4 py-1.5 text-sm font-medium text-white backdrop-blur-sm transition group-hover:bg-white/30">
                Learn More
            </div>

            {{-- Decorative stars --}}
            <x-icons.star
                class="absolute top-4 right-3 z-10 w-3 -rotate-7 text-purple-300 animate-spin "
            />
            <x-icons.star
                class="absolute top-8 left-4 z-10 w-2 rotate-12 text-indigo-300 animate-pulse "
            />
            <x-icons.star
                class="absolute bottom-12 right-6 z-10 w-2.5 text-purple-200 animate-ping "
            />
        </a>
    @endif

    {{-- Ultra Ad --}}
    @if (in_array('ultra', $ads))
        <a
            x-show="ad === 'ultra'"
            x-cloak
            href="{{ route('pricing') }}"
            class="group relative z-0 grid place-items-center overflow-hidden rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 px-4 py-8 text-center text-pretty transition duration-200 hover:from-amber-300 hover:to-orange-400 hover:ring-1 hover:ring-amber-300"
        >
            {{-- Icon --}}
            <div class="grid size-14 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <x-heroicon-s-bolt class="size-8" />
            </div>

            {{-- Title --}}
            <div class="mt-3 text-lg font-bold text-white">
                NativePHP Ultra
            </div>

            {{-- Tagline --}}
            <div class="mt-2 text-sm text-amber-50">
                All NativePHP plugins, teams &amp; priority support from
                <strong class="text-white">${{ config('subscriptions.plans.max.price_monthly') }}/mo</strong>
            </div>

            {{-- CTA --}}
            <div class="mt-4 rounded-lg bg-white/20 px-4 py-1.5 text-sm font-medium text-white backdrop-blur-sm transition group-hover:bg-white/30">
                Learn More
            </div>

            {{-- Decorative stars --}}
            <x-icons.star
                class="absolute top-4 right-3 z-10 w-3 -rotate-7 text-amber-200 animate-ping "
            />
            <x-icons.star
                class="absolute top-8 left-4 z-10 w-2 rotate-12 text-orange-200 animate-spin "
            />
            <x-icons.star
                class="absolute bottom-12 right-6 z-10 w-2.5 text-amber-100 animate-pulse "
            />
        </a>
    @endif

    {{-- The Vibes Ad --}}
    @if (in_array('vibes', $ads))
        <a
            x-show="ad === 'vibes'"
            x-cloak
            href="{{ route('the-vibes') }}"
            class="group relative z-0 grid place-items-center overflow-hidden rounded-2xl px-4 py-8 text-center text-pretty transition duration-200 hover:ring-1 hover:ring-violet-400"
        >
            {{-- Background image --}}
            <img
                src="{{ Vite::asset('resources/images/the-vibes/what-is-vibes.webp') }}"
                alt=""
                aria-hidden="true"
                class="absolute inset-0 -z-10 size-full object-cover blur-[1px] contrast-75 brightness-65"
                loading="lazy"
            />

            {{-- Title --}}
            <div class="z-10 text-lg font-bold text-white drop-shadow">
                The Vibes
            </div>

            {{-- Tagline --}}
            <div class="z-10 mt-2 text-sm text-violet-100 drop-shadow">
                The unofficial Laracon US
                <strong class="text-white">Day 3</strong>
            </div>

            {{-- CTA --}}
            <div class="z-10 mt-4 rounded-lg bg-white/20 px-4 py-1.5 text-sm font-medium text-white backdrop-blur-sm transition group-hover:bg-white/30">
                Grab Your Spot
            </div>

            {{-- Scarcity Label --}}
            <div class="z-10 mt-2 text-xs text-violet-100 drop-shadow">
                Only 100 tickets!
            </div>

            {{-- Decorative stars --}}
            <x-icons.star
                class="absolute top-4 right-3 z-10 w-3 -rotate-7 text-violet-300 animate-ping "
            />
            <x-icons.star
                class="absolute top-8 left-4 z-10 w-2 rotate-12 text-indigo-300 animate-spin "
            />
        </a>
    @endif

    {{-- Masterclass Ad --}}
    @if (in_array('masterclass', $ads))
        <a
            x-show="ad === 'masterclass'"
            x-cloak
            href="{{ route('course') }}"
            class="group relative z-0 grid place-items-center overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 px-4 py-8 text-center text-pretty transition duration-200 hover:from-emerald-400 hover:to-teal-500 hover:ring-1 hover:ring-emerald-400"
        >
            {{-- Icon --}}
            <div class="grid size-14 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <x-heroicon-s-academic-cap class="size-8" />
            </div>

            {{-- Title --}}
            <div class="mt-3 text-lg font-bold text-white">
                The Masterclass
            </div>

            {{-- Tagline --}}
            <div class="mt-2 text-sm text-emerald-50">
                Go from zero to
                <strong class="text-white">published app</strong>
                <br />
                in no time
            </div>

            {{-- CTA --}}
            <div class="mt-4 rounded-lg bg-white/20 px-4 py-1.5 text-sm font-medium text-white backdrop-blur-sm transition group-hover:bg-white/30">
                Learn More
            </div>

            {{-- Early Bird Label --}}
            <div class="mt-2 text-xs text-emerald-100">
                Early Bird Pricing
            </div>

            {{-- Decorative stars --}}
            <x-icons.star
                class="absolute top-4 right-3 z-10 w-3 -rotate-7 text-emerald-200 animate-ping "
            />
            <x-icons.star
                class="absolute top-8 left-4 z-10 w-2 rotate-12 text-teal-200 animate-spin "
            />
            <x-icons.star
                class="absolute bottom-12 right-6 z-10 w-2.5 text-emerald-100 animate-pulse "
            />
        </a>
    @endif
</div>
