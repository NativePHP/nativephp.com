<figure
    class="flex items-center justify-center py-10"
    aria-labelledby="bifrost-diagram-caption"
>
    {{-- Source (NativePHP app) --}}
    <div
        class="hidden flex-col items-center gap-2 pr-2 text-center 2xs:flex sm:pr-0"
    >
        <x-mini-logo
            class="h-7 sm:h-9"
            aria-hidden="true"
            focusable="false"
        />

        <p class="hidden whitespace-nowrap sm:block md:text-lg">
            <span class="font-semibold">NativePHP</span>
            <span>app</span>
        </p>
    </div>

    {{-- Left line (decorative) --}}
    <div
        class="hidden h-0.5 w-10 bg-gradient-to-r from-transparent to-blue-500/50 2xs:block sm:w-20"
        aria-hidden="true"
    ></div>

    {{-- Center (Bifrost) --}}
    <div
        class="relative"
        aria-label="Bifrost build orchestration"
    >
        {{-- Decorative blank box (top) --}}
        <div
            class="absolute -top-26 left-0 -z-10 grid h-23 w-35 place-items-center rounded-2xl mask-t-from-0% ring-3 ring-gray-200 ring-inset xs:w-40 sm:-top-30 sm:h-27 sm:w-47 dark:ring-white/3"
            aria-hidden="true"
        >
            <div
                class="h-1.5 w-15 rounded-full bg-gray-200 dark:bg-white/3"
            ></div>
        </div>

        <div
            class="grid h-23 w-35 place-items-center overflow-hidden rounded-2xl bg-gradient-to-tl from-transparent to-blue-500/50 p-[3px] xs:w-40 sm:h-27 sm:w-47 dark:from-blue-300/20"
        >
            <div
                class="grid h-full w-full place-items-center rounded-[calc(var(--radius-2xl)-2px)] bg-white dark:bg-slate-950"
            >
                <x-logos.bifrost
                    class="h-3.5 xs:h-4 sm:h-5"
                    role="img"
                    aria-label="Bifrost"
                />
                <span class="sr-only">Bifrost build system</span>
            </div>
        </div>

        {{-- Decorative blank box (bottom) --}}
        <div
            class="absolute -bottom-26 left-0 -z-10 grid h-23 w-35 place-items-center rounded-2xl mask-b-from-0% ring-3 ring-gray-200 ring-inset xs:w-40 sm:-bottom-30 sm:h-27 sm:w-47 dark:ring-white/3"
            aria-hidden="true"
        >
            <div
                class="h-1.5 w-15 rounded-full bg-gray-200 dark:bg-white/3"
            ></div>
        </div>
    </div>

    {{-- Right (Build outputs to platforms) --}}
    <div class="flex items-center sm:pr-14 md:pr-24">
        <div aria-hidden="true">
            <x-icons.home.arc-connector aria-hidden="true" />
            <div
                class="-my-0.5 h-0.5 w-16 rounded-full bg-gradient-to-r from-blue-300/20 to-blue-500/80"
            ></div>
            <div class="-scale-y-100">
                <x-icons.home.arc-connector aria-hidden="true" />
            </div>
        </div>

        {{-- Platforms list --}}
        <ul
            class="relative flex flex-col gap-4"
            aria-label="Target platforms"
        >
            {{-- Decorative (top) --}}
            <li
                class="absolute -top-15 left-0 -z-10 grid size-12 place-items-center rounded-xl mask-t-from-0% ring-2 ring-gray-200 ring-inset dark:ring-white/3"
                aria-hidden="true"
            >
                <div
                    class="h-1 w-5 rounded-full bg-gray-200 dark:bg-white/3"
                ></div>
            </li>

            <li
                class="grid size-12 place-items-center rounded-xl bg-white dark:bg-slate-950/50"
            >
                <x-icons.home.apple
                    class="h-8"
                    role="img"
                    aria-label="Apple (macOS)"
                />
                <span class="sr-only">Apple (macOS)</span>
            </li>
            <li
                class="grid size-12 place-items-center rounded-xl bg-white dark:bg-slate-950/50"
            >
                <x-icons.home.android
                    class="h-7"
                    role="img"
                    aria-label="Android"
                />
                <span class="sr-only">Android</span>
            </li>
            <li
                class="grid size-12 place-items-center rounded-xl bg-white dark:bg-slate-950/50"
            >
                <x-icons.home.windows
                    class="h-6"
                    role="img"
                    aria-label="Windows"
                />
                <span class="sr-only">Windows</span>
            </li>

            {{-- Decorative (bottom) --}}
            <li
                class="absolute -bottom-15 left-0 -z-10 grid size-12 place-items-center rounded-xl mask-b-from-0% ring-2 ring-gray-200 ring-inset dark:ring-white/3"
                aria-hidden="true"
            >
                <div
                    class="h-1 w-5 rounded-full bg-gray-200 dark:bg-white/3"
                ></div>
            </li>
        </ul>
    </div>

    <figcaption
        id="bifrost-diagram-caption"
        class="sr-only"
    >
        Diagram: NativePHP app passes through Bifrost build system to produce
        Apple (macOS), Android, and Windows platform outputs.
    </figcaption>
</figure>
