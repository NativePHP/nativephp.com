{{--
    Newsletter signup modal. Opened from anywhere on the site by dispatching an
    `open-newsletter-modal` window event, so triggers don't have to be nested
    inside it. It also opens itself when the page is loaded with `?newsletter` in
    the URL, which is where the `/newsletter` shortlink sends people. The form
    posts straight to Mailcoach, which then redirects to one of our own
    `newsletter.*` pages depending on the outcome.
--}}
<div
    x-data="{
        open: false,
        show() {
            this.open = true
            this.$nextTick(() => this.$refs.email?.focus())
        },
    }"
    @if (request()->has('newsletter'))
        x-init="show()"
    @endif
    @open-newsletter-modal.window="show()"
    @keydown.escape.window="open = false"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs"
        x-cloak
    ></div>

    <div
        x-show="open"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="newsletter-modal-heading"
        x-cloak
    >
        <div
            @click.away="open = false"
            class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white p-8 shadow-xl ring-1 ring-black/5 dark:bg-mirage dark:ring-white/10"
        >
            {{-- Decorative glow --}}
            <div
                class="pointer-events-none absolute -top-24 left-1/2 size-40 -translate-x-1/2 rounded-full bg-emerald-400/40 blur-3xl"
                aria-hidden="true"
            ></div>

            {{-- Close --}}
            <button
                type="button"
                @click="open = false"
                class="absolute top-4 right-4 rounded-lg p-2 text-gray-400 transition duration-200 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/5 dark:hover:text-gray-200"
            >
                <x-icons.xmark class="size-3" />
                <span class="sr-only">Close</span>
            </button>

            <div class="text-center">
                <x-icons.gift
                    class="mx-auto size-8 text-emerald-600 dark:text-emerald-400"
                    aria-hidden="true"
                />

                <h2
                    id="newsletter-modal-heading"
                    class="mt-4 text-xl font-semibold"
                >
                    Get 10% off
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Join the NativePHP newsletter and we'll email you a 10%
                    discount code, along with the latest updates and news.
                </p>
            </div>

            <form
                method="post"
                action="{{ config('services.mailcoach.newsletter_subscribe_url') }}"
                class="mt-6 flex flex-col gap-3"
            >
                {{-- Honeypot: must be submitted empty to pass Mailcoach's bot check --}}
                <div class="absolute -left-[9999px]">
                    <label
                        for="newsletter-{{ config('services.mailcoach.honeypot_field') }}"
                    >
                        Your pet
                    </label>
                    <input
                        type="text"
                        id="newsletter-{{ config('services.mailcoach.honeypot_field') }}"
                        name="{{ config('services.mailcoach.honeypot_field') }}"
                        tabindex="-1"
                        autocomplete="nope"
                    />
                </div>

                <label
                    for="newsletter-email"
                    class="sr-only"
                >
                    Email address
                </label>
                <input
                    x-ref="email"
                    type="email"
                    id="newsletter-email"
                    name="email"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                />

                {{-- Where Mailcoach sends people once it has handled the signup --}}
                <input
                    type="hidden"
                    name="redirect_after_subscription_pending"
                    value="{{ route('newsletter.confirm') }}"
                />
                <input
                    type="hidden"
                    name="redirect_after_subscribed"
                    value="{{ route('newsletter.subscribed') }}"
                />
                <input
                    type="hidden"
                    name="redirect_after_already_subscribed"
                    value="{{ route('newsletter.already-subscribed') }}"
                />

                <button
                    type="submit"
                    @click="window.fathom?.trackEvent('newsletter_modal_submit')"
                    class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    Send me the code
                </button>

                <p class="text-center text-xs text-gray-400 dark:text-gray-500">
                    We'll send a confirmation email first. Unsubscribe any time.
                </p>
            </form>
        </div>
    </div>
</div>
