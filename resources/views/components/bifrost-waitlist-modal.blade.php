@props(['user' => null])

{{--
    Bifrost waitlist modal. Opened from anywhere on the site by dispatching an
    `open-bifrost-waitlist-modal` window event, and on load when the URL has
    `?waitlist`. The form posts to Kit with fetch and shows Kit's reply in place,
    so people never leave the page. If the request itself fails, it points to
    the hosted Kit form instead. Pass a signed-in `user` to prefill their first
    name and email, so signing up is one click.
--}}
<div
    x-data="{
        open: false,
        status: 'idle',
        error: '',
        show() {
            this.open = true
            this.$nextTick(() => (this.$refs.email.value ? this.$refs.submit : this.$refs.name)?.focus())
        },
        async submit() {
            this.status = 'sending'
            this.error = ''
            window.fathom?.trackEvent('bifrost_waitlist_modal_submit')

            try {
                const response = await fetch(this.$refs.form.action, {
                    method: 'POST',
                    body: new FormData(this.$refs.form),
                    headers: { Accept: 'application/json' },
                })
                const reply = await response.json()

                if (reply.status === 'success') {
                    this.status = 'done'
                    window.fathom?.trackEvent('bifrost_waitlist_signup')

                    return
                }

                this.error = reply.errors?.messages?.join(' ') || 'Something went wrong. Please try again.'
                this.status = 'error'
            } catch (e) {
                this.error = 'We couldn\'t reach our email provider.'
                this.status = 'failed'
            }
        },
    }"
    @if (request()->has('waitlist'))
        x-init="show()"
    @endif
    @open-bifrost-waitlist-modal.window="show()"
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
        aria-labelledby="bifrost-waitlist-modal-heading"
        x-cloak
    >
        <div
            @click.away="open = false"
            class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white p-8 shadow-xl ring-1 ring-black/5 dark:bg-mirage dark:ring-white/10"
        >
            {{-- Decorative glow --}}
            <div
                class="pointer-events-none absolute -top-24 left-1/2 size-48 -translate-x-1/2 rounded-full bg-gradient-to-r from-sky-400/40 via-indigo-500/40 to-violet-500/40 blur-3xl"
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

            {{-- Signup --}}
            <div x-show="status !== 'done'">
                <div class="relative text-center">
                    <x-icons.rocket
                        class="mx-auto size-8 text-indigo-600 dark:text-indigo-300"
                        aria-hidden="true"
                    />

                    <h2
                        id="bifrost-waitlist-modal-heading"
                        class="mt-4 text-xl font-semibold text-balance"
                    >
                        Ship your PHP app to the App Store
                    </h2>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Free, practical guides from the Bifrost team on native
                        builds, signing and getting through store review.
                    </p>
                </div>

                <form
                    x-ref="form"
                    method="post"
                    action="{{ config('services.kit.waitlist_subscribe_url') }}"
                    @submit.prevent="submit()"
                    class="relative mt-6 flex flex-col gap-3"
                >
                    <label for="bifrost-waitlist-name" class="sr-only">First name</label>
                    <input
                        x-ref="name"
                        type="text"
                        id="bifrost-waitlist-name"
                        name="fields[first_name]"
                        placeholder="First name"
                        autocomplete="given-name"
                        value="{{ $user?->first_name }}"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                    />

                    <label for="bifrost-waitlist-email" class="sr-only">Email address</label>
                    <input
                        x-ref="email"
                        type="email"
                        id="bifrost-waitlist-email"
                        name="email_address"
                        placeholder="you@example.com"
                        autocomplete="email"
                        value="{{ $user?->email }}"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                    />

                    <p
                        x-show="status === 'error' || status === 'failed'"
                        x-cloak
                        role="alert"
                        class="text-sm text-red-600 dark:text-red-400"
                    >
                        <span x-text="error"></span>
                        <a
                            x-show="status === 'failed'"
                            href="{{ config('services.kit.waitlist_hosted_url') }}"
                            target="_blank"
                            rel="noopener"
                            class="underline"
                        >Sign up here instead.</a>
                    </p>

                    <button
                        x-ref="submit"
                        type="submit"
                        :disabled="status === 'sending'"
                        class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-gray-800 disabled:opacity-60 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                    >
                        <span x-show="status !== 'sending'">Get the free guides</span>
                        <span x-show="status === 'sending'" x-cloak>Signing you up…</span>
                    </button>

                    <p class="text-center text-xs text-gray-400 dark:text-gray-500">
                        We'll send a confirmation email first. Unsubscribe any time.
                    </p>
                </form>
            </div>

            {{-- Done --}}
            <div x-show="status === 'done'" x-cloak class="relative text-center">
                <x-icons.checkmark
                    class="mx-auto size-8 text-emerald-600 dark:text-emerald-400"
                    aria-hidden="true"
                />

                <h2 class="mt-4 text-xl font-semibold">Check your inbox</h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    We've sent you a confirmation email. Click the link in it and
                    the first guide is on its way.
                </p>

                <button
                    type="button"
                    @click="open = false"
                    class="mt-6 rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    Done
                </button>
            </div>
        </div>
    </div>
</div>
