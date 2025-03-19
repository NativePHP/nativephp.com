<footer class="2xl:max-w-8xl mx-auto max-w-5xl px-5 pb-5 pt-20 xl:max-w-7xl">
    <div class="flex flex-wrap items-center justify-between gap-6">
        <a
            href="/"
            class="transition duration-200 will-change-transform hover:scale-[1.02]"
        >
            <x-logo class="h-5" />
        </a>
        <div class="flex flex-wrap items-center justify-center gap-2.5">
            <x-social-networks-all />
        </div>
    </div>
    <div class="flex items-center pb-3 pt-5">
        <div class="size-1.5 rotate-45 bg-gray-200/90"></div>
        <div class="h-0.5 w-full bg-gray-200/90"></div>
        <div class="size-1.5 rotate-45 bg-gray-200/90"></div>
    </div>
    <div
        class="flex flex-wrap items-center justify-between gap-x-5 gap-y-3 text-sm text-gray-500"
    >
        <div>
            Logo by
            <a
                href="https://twitter.com/caneco"
                target="_blank"
                class="transition duration-200 hover:text-black"
            >
                Caneco
            </a>
            .
        </div>
        <div>
            © {{ date('Y') }} Maintained by
            <a
                href="https://twitter.com/marcelpociot"
                target="_blank"
                class="transition duration-200 hover:text-black"
            >
                Marcel Pociot
            </a>
            and
            <a
                href="https://twitter.com/simonhamp"
                target="_blank"
                class="transition duration-200 hover:text-black"
            >
                Simon Hamp
            </a>
            .
        </div>
    </div>
</footer>
