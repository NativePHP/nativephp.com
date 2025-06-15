<div>
    <div
        x-data="{
            open: @entangle('showModal'),
        }"
        @open-purchase-modal.window="
            open = true;
            $wire.setPlan($event.detail.plan);
        "
    >
        <!-- Modal Backdrop -->
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
            x-cloak
        >
            <div
                @click.away="open = false"
                class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl dark:bg-mirage"
            >
                <div class="mb-6 text-center">
                    <h3 class="text-xl font-semibold dark:text-white">
                        Get Started with NativePHP
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Enter your email to continue to checkout
                    </p>
                </div>

                <form
                    wire:submit.prevent="submit"
                    class="space-y-8"
                >
                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Email Address
                        </label>
                        <input
                            type="email"
                            id="email"
                            wire:model.blur="email"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-purple-400 focus:outline-hidden dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="your@email.com"
                            required
                            x-effect="if (open) $nextTick(() => $el.focus())"
                        />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-xl bg-zinc-200 px-8 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-zinc-800 px-8 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-900 dark:bg-[#d68ffe] dark:text-black dark:hover:bg-[#e1acff]"
                        >
                            Next
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
