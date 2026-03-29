<x-layout title="Support - NativePHP">
    {{-- Support Grid Section --}}
    <section class="mx-auto mt-10 max-w-5xl px-5 md:mt-14">
        {{-- Header --}}
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-bold md:text-5xl dark:text-white/90">Support</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-white/60">
                Get help with NativePHP through our various support channels.
            </p>
        </header>

        {{-- Additional Support Information --}}
        <div class="my-10 rounded-xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] p-8 dark:from-blue-900/10 dark:to-[#4c407f]/25">
            <h2 class="mb-4 text-2xl font-medium">Read the docs</h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                Before reaching out for help, take a look through our <a href="/docs" class="font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">documentation</a>. It's concise by design &mdash; you can read the whole thing in under an hour &mdash; and most questions are answered there.
            </p>
        </div>

        {{-- Priority Support --}}
        <div class="mb-10 overflow-hidden rounded-xl border border-violet-200 bg-gradient-to-br from-violet-50 to-violet-100/50 dark:border-violet-800/50 dark:from-violet-900/20 dark:to-violet-800/10">
            <div class="flex flex-col items-center gap-6 p-8 sm:flex-row sm:items-start">
                <div class="grid size-16 shrink-0 place-items-center rounded-full bg-violet-600 text-white">
                    <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Priority Support</h2>
                    @if (auth()->check() && auth()->user()->hasUltraAccess())
                        <p class="mt-1 text-gray-600 dark:text-gray-400">As an Ultra subscriber, you have access to priority support. Submit a ticket and our team will get back to you as quickly as possible.</p>
                        <a href="{{ route('customer.support.tickets.create') }}" class="mt-4 inline-flex items-center rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-200 hover:bg-violet-700 dark:bg-violet-700 dark:hover:bg-violet-600">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Submit a Ticket
                        </a>
                    @else
                        <p class="mt-1 text-gray-600 dark:text-gray-400">Need direct help from the NativePHP team? Priority support with ticket-based assistance is available exclusively to Ultra subscribers.</p>
                        <a href="{{ route('pricing') }}" class="mt-4 inline-flex items-center rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-200 hover:bg-violet-700 dark:bg-violet-700 dark:hover:bg-violet-600">
                            Learn about Ultra
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Support Grid --}}
        <div class="grid w-full grid-cols-1 gap-8 md:grid-cols-3">

            {{-- GitHub Mobile Issues --}}
            <a href="https://github.com/NativePHP/mobile-air/issues"
               target="_blank"
               rel="noopener"
               class="group flex w-full flex-col items-center rounded-xl bg-gray-100/80 p-8 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30"
               aria-label="Report mobile issues on GitHub">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 transition duration-300 group-hover:rotate-3 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                    <x-icons.device-mobile-phone class="size-8" />
                </div>
                <h2 class="text-xl font-medium">GitHub Issues for Mobile</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Report issues or contribute to NativePHP for Mobile</p>
            </a>

            {{-- GitHub Desktop Issues --}}
            <a href="https://github.com/NativePHP/desktop/issues"
               target="_blank"
               rel="noopener"
               class="group flex w-full flex-col items-center rounded-xl bg-gray-100/80 p-8 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30"
               aria-label="Report desktop issues on GitHub">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 transition duration-300 group-hover:rotate-3 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                    <x-icons.pc class="size-8" />
                </div>
                <h2 class="text-xl font-medium">GitHub Issues for Desktop</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Report issues or contribute to NativePHP for Desktop</p>
            </a>

            {{-- Discord Box --}}
            <a href="{{ $discordLink }}"
               target="_blank"
               rel="noopener"
               class="group flex w-full flex-col items-center rounded-xl bg-gray-100/80 p-8 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30"
               aria-label="Join our Discord community">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-[#5865F2] ring-1 ring-black/5 transition duration-300 group-hover:-rotate-3 dark:bg-gray-900 dark:ring-white/10">
                    <x-icons.discord class="size-8" />
                </div>
                <h2 class="text-xl font-medium">Discord</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Join the community and get real-time help</p>
            </a>

        </div>
    </section>
</x-layout>
