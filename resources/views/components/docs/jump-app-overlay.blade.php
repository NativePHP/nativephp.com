{{--
    Shown when a QR visitor lands here without the Jump app installed.

    Must stay outside the desktop-only right sidebar: it is the phone — not the
    desktop — that renders this, after the deep link falls back to the browser.
--}}
<div
    x-cloak
    x-data="{
        show: new URLSearchParams(window.location.search).get('jump') === 'qr',
        platform: (() => {
            const ua = navigator.userAgent || '';
            if (/iPad|iPhone|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)) return 'ios';
            if (/android/i.test(ua)) return 'android';
            return 'unknown';
        })(),
        dismiss() { this.show = false; },
    }"
    x-show="show"
    x-effect="document.documentElement.style.overflow = show ? 'hidden' : ''"
    x-on:keydown.escape.window="dismiss()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="jump-overlay-title"
>
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-on:click="dismiss()" aria-hidden="true"></div>

    <div
        x-show="show"
        x-transition
        class="relative w-full max-w-sm rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-slate-900"
    >
        <button
            type="button"
            x-on:click="dismiss()"
            class="absolute right-4 top-4 text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
            aria-label="Dismiss"
        >
            <x-heroicon-o-x-mark class="size-5" />
        </button>

        <div class="mx-auto grid size-12 place-items-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
            <x-heroicon-o-bolt class="size-6" aria-hidden="true" />
        </div>

        <h2 id="jump-overlay-title" class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
            Get the Jump app
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            You'll need the free Jump app to preview this page on your device. Install it, then scan the code again.
        </p>

        <div class="mt-6 flex flex-col gap-3">
            {{-- iOS: shown on Apple devices, or when the platform can't be detected. --}}
            <a
                x-show="platform === 'ios' || platform === 'unknown'"
                href="{{ \App\Support\JumpApp::IOS_APP_STORE_URL }}"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center gap-3 rounded-xl bg-black px-5 py-2.5 text-white transition hover:bg-gray-800"
            >
                <svg viewBox="0 0 24 24" fill="currentColor" class="size-6 shrink-0" aria-hidden="true">
                    <path d="M17.05 12.04c-.03-2.6 2.12-3.85 2.22-3.91-1.21-1.77-3.09-2.01-3.76-2.04-1.6-.16-3.12.94-3.93.94-.81 0-2.06-.92-3.39-.9-1.74.03-3.35 1.01-4.25 2.57-1.81 3.14-.46 7.79 1.3 10.34.86 1.25 1.88 2.65 3.22 2.6 1.29-.05 1.78-.83 3.34-.83 1.56 0 2 .83 3.37.81 1.39-.03 2.27-1.27 3.12-2.53.98-1.45 1.39-2.85 1.41-2.92-.03-.01-2.71-1.04-2.74-4.13M14.53 4.5c.72-.87 1.2-2.08 1.07-3.28-1.03.04-2.28.69-3.02 1.56-.66.77-1.24 2-1.08 3.18 1.15.09 2.32-.59 3.03-1.46"/>
                </svg>
                <span class="text-left leading-tight">
                    <span class="block text-[10px] uppercase tracking-wide opacity-80">Download on the</span>
                    <span class="-mt-0.5 block text-base font-semibold">App Store</span>
                </span>
            </a>

            {{-- Android: shown on Android devices, or when the platform can't be detected. --}}
            <a
                x-show="platform === 'android' || platform === 'unknown'"
                href="{{ \App\Support\JumpApp::ANDROID_PLAY_STORE_URL }}"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center gap-3 rounded-xl bg-black px-5 py-2.5 text-white transition hover:bg-gray-800"
            >
                <svg viewBox="0 0 24 24" class="size-6 shrink-0" aria-hidden="true">
                    <path fill="#00d4ff" d="M3.6 2.3c-.3.3-.4.7-.4 1.2v17c0 .5.1.9.4 1.2l.1.1L13 12.6v-.2z"/>
                    <path fill="#00f076" d="M16.1 15.7l-3.1-3.1v-.2l3.1-3.1.1.1 3.7 2.1c1 .6 1 1.6 0 2.2z"/>
                    <path fill="#ff3a44" d="M16.2 15.8L13 12.6 3.6 22c.4.3.9.4 1.5 0z"/>
                    <path fill="#ffc400" d="M3.6 2.3L13 11.7l3.2-3.2-11-6.2c-.6-.3-1.2-.3-1.6 0z"/>
                </svg>
                <span class="text-left leading-tight">
                    <span class="block text-[10px] uppercase tracking-wide opacity-80">Get it on</span>
                    <span class="-mt-0.5 block text-base font-semibold">Google Play</span>
                </span>
            </a>
        </div>

        <button
            type="button"
            x-on:click="dismiss()"
            class="mt-4 text-xs font-medium text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300"
        >
            Already installed? Scan the code again.
        </button>
    </div>
</div>
