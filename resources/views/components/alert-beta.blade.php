<div {{ $attributes->class(['rounded-lg flex items-center p-3 mt-8 space-x-6 border
                text-orange-800 border-orange-300 bg-orange-50
                dark:text-orange-100 dark:bg-orange-900/80 dark:border-orange-600']) }}>
    <x-heroicon-o-shield-exclamation class="size-10 ml-3"/>
    <div>
        <p>
            NativePHP is currently in
            <a href="/docs/getting-started/status" class="font-bold italic font-mono flex-inline px-1 py-0.5 text-base bg-orange-200 dark:bg-orange-600 rounded">alpha</a>
            development
        </p>

        <a href="https://github.com/nativephp/laravel?sponsor=1"
           onclick="fathom.trackEvent('beta_interest');"
           class="group mt-4 font-bold inline-flex items-center rounded-md px-3 py-1
                            bg-orange-200 border border-orange-400 hover:bg-orange-300
                            dark:bg-orange-600 dark:border-orange-400 dark:hover:bg-orange-500 dark:text-orange-100
                            " target="_blank">
            Let's get to beta!
            <x-heroicon-o-rocket-launch class="ml-2 size-5 group-hover:hidden"/>
            <x-heroicon-s-rocket-launch class="hidden ml-2 size-5 group-hover:block"/>
        </a>
    </div>
</div>
