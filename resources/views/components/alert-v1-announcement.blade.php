<div
    class="relative z-0 mt-5 flex flex-col items-start gap-3 overflow-hidden rounded-2xl bg-slate-100 p-4 ring-1 ring-black/5 min-[450px]:flex-row min-[450px]:items-center dark:bg-mirage"
    role="alert"
    aria-labelledby="beta-alert-title"
    aria-describedby="beta-alert-description"
>
    <div
        class="absolute left-0 top-0 -z-10 size-32 -rotate-45 rounded-full bg-white blur-2xl min-[450px]:top-1/2 min-[450px]:-translate-y-1/2 dark:block dark:bg-blue-500/40"
        aria-hidden="true"
    ></div>

    {{-- Icon --}}
    <div
        class="grid size-10 place-items-center rounded-full bg-gray-200/50 dark:bg-black/30"
    >
        <x-icons.colored-confetti
            class="-mr-px size-[22px] shrink-0 mix-blend-multiply dark:hidden"
            aria-hidden="true"
        />

        <x-icons.confetti
            class="hidden size-5 shrink-0 dark:block"
            aria-hidden="true"
        />
    </div>

    {{-- Title --}}
    <h2 class="font-medium">NativePHP for desktop has finally reached v1!</h2>
</div>
