<div
    class="relative h-full rounded-2xl bg-gray-100 p-7 opacity-0 {{ $popular ?? false ? 'ring-1 ring-black dark:bg-black/50 dark:ring-white/20' : 'dark:bg-mirage' }}"
    aria-labelledby="teams-plan-heading"
>
    @if($popular ?? false)
        {{-- Popular badge --}}
        <div
            class="absolute -top-5 -right-3 rounded-xl bg-linear-to-tr from-[#6886FF] to-[#B8C1FF] px-5 py-2 text-sm text-white dark:from-gray-900 dark:to-black dark:ring-1 dark:ring-white/10"
            aria-label="Most popular plan"
        >
            Most Popular
        </div>
    @endif

    {{-- Plan Name --}}
    <h3
        id="teams-plan-heading"
        class="text-3xl font-semibold"
    >
        <span class="rounded-full bg-zinc-300 dark:bg-zinc-600 px-4">
            {{ $name }}
        </span>
    </h3>

    {{-- Price --}}
    <div
        class="flex items-start gap-1.5 pt-5"
        aria-label="Price: ${{ number_format($discounted ? $discountedPrice : $price) }} per year"
    >

        <div>
            @if($discounted)
                <span class="text-2xl font-semibold text-zinc-500">
                    Was ${{ number_format($price) }}
                </span>
            @endif
            <div class="text-5xl font-semibold">
                ${{ number_format($discounted ? $discountedPrice : $price) }}
            </div>
        </div>
        <div class="self-end pb-1.5 text-zinc-500">per year</div>
    </div>

    @auth
        <button
            type="button"
            wire:click="createCheckoutSession('{{ $id }}')"
            class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
            aria-label="Get started with {{ $name }} plan"
        >
            Get started
        </button>
    @else
        <button
            type="button"
            @if($discounted)
                wire:click="handlePurchaseRequest({ plan: '{{ $id }}' })"
            @else
                @click="$dispatch('open-purchase-modal', { plan: '{{ $id }}' })"
            @endif
            class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
            aria-label="Get started with {{ $name }} plan"
        >
            Get started
        </button>
    @endauth

    <x-pricing-plan-features :$features :plan-name="$name" />
</div>
