<x-layout title="Build My App">
    @push('head')
        @if (config('services.turnstile.site_key'))
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif
    @endpush

    <div class="mx-auto max-w-3xl">
        <section class="mt-12">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl">
                    <span class="text-[#99ceb2] dark:text-indigo-500">{</span>
                    <span class="font-bold">Build My App</span>
                    <span class="text-[#99ceb2] dark:text-indigo-500">}</span>
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 dark:text-zinc-400">
                    Need help bringing your app idea to life?<br>
                    Tell us about your project! We'd love to help.
                </p>
            </div>
        </section>

        <section class="mt-12 pb-24">
            <div class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e] md:p-12">
                <livewire:lead-submission-form />
            </div>
        </section>
    </div>
</x-layout>
