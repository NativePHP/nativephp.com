@props([
    'question',
])

@php
    global $count;
@endphp

<div class="w-full opacity-0">
    <div
        x-data="{ open: false }"
        x-on:click="open = !open"
        class="group/faq-card grid cursor-pointer select-none grid-cols-[1.8rem_1fr_2.5rem] gap-x-3 rounded-2xl px-5 py-5 ring-1 transition-all duration-200"
        :class="{ 'ring-gray-200 hover:bg-gray-100 dark:ring-white/20 dark:hover:bg-gray-900/50': !open, 'ring-black/10 bg-gray-100 dark:ring-white/20 dark:bg-gray-900/50': open }"
    >
        {{-- Number --}}
        <div
            class="self-center text-lg font-light text-gray-400"
        >
            {{ str_pad(++$count, 2, 0, STR_PAD_LEFT) }}
        </div>

        {{-- Question --}}
        <div class="self-center font-semibold">
            {{ $question }}
        </div>

        {{-- Arrow --}}
        <div>
            <div
                class="grid size-9 shrink-0 place-items-center rounded-full transition duration-300"
                :class="{ 'group-hover/faq-card:bg-gray-200 dark:group-hover/faq-card:bg-gray-900/70 bg-gray-100 dark:bg-gray-900/70 dark:text-gray-400 text-[#767981]': !open, 'dark:bg-gray-900/70 bg-gray-200': open }"
            >
                <div
                    class="transition-transform duration-300 will-change-transform"
                    :class="{ 'rotate-180': open }"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="size-6"
                        viewBox="0 0 30 30"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_443_417)">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M15.8837 19.6339C15.6493 19.8683 15.3314 19.9999 14.9999 19.9999C14.6685 19.9999 14.3506 19.8683 14.1162 19.6339L7.04494 12.5627C6.92555 12.4474 6.83032 12.3094 6.76481 12.1569C6.6993 12.0044 6.66482 11.8404 6.66338 11.6744C6.66194 11.5085 6.69356 11.3439 6.75641 11.1902C6.81926 11.0366 6.91208 10.8971 7.02945 10.7797C7.14681 10.6623 7.28638 10.5695 7.44 10.5067C7.59362 10.4438 7.75822 10.4122 7.92419 10.4136C8.09017 10.4151 8.25419 10.4495 8.4067 10.5151C8.5592 10.5806 8.69713 10.6758 8.81244 10.7952L14.9999 16.9827L21.1874 10.7952C21.4232 10.5675 21.7389 10.4415 22.0667 10.4443C22.3944 10.4472 22.708 10.5787 22.9397 10.8104C23.1715 11.0422 23.3029 11.3557 23.3058 11.6834C23.3086 12.0112 23.1826 12.3269 22.9549 12.5627L15.8837 19.6339Z"
                                fill="currentColor"
                            />
                        </g>
                        <defs>
                            <clipPath id="clip0_443_417">
                                <rect
                                    width="30"
                                    height="30"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Answer --}}
        <div
            x-show="open"
            x-collapse
            class="col-start-2 text-gray-600 dark:text-gray-500"
        >
            {!! $slot !!}
        </div>
    </div>
</div>
