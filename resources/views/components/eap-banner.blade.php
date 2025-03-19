<a
    href="/mobile"
    onclick="fathom.trackEvent('alert_click');"
    class="group relative z-30 flex items-center justify-center gap-2 bg-gradient-to-r from-[#352F5B] to-[#6056AA] px-5 py-2.5 text-center"
>
    <style>
        @keyframes shine {
            0% {
                background-position: 200% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
    </style>
    {{-- Text --}}
    <div
        class="bg-clip-text tracking-tight text-transparent"
        style="
            background-image: linear-gradient(
                90deg,
                #8d89b5 0%,
                white 35%,
                #8d89b5 70%
            );
            background-size: 200% 100%;
            animation: shine 2s linear infinite;
        "
    >
        Join our Mobile Early Access Program
    </div>

    {{-- Arrow --}}
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="15"
        height="11"
        viewBox="0 0 15 11"
        fill="none"
        class="shrink-0 transition duration-300 ease-in-out will-change-transform group-hover:translate-x-1"
    >
        <path
            d="M1 4.8C0.613401 4.8 0.3 5.1134 0.3 5.5C0.3 5.8866 0.613401 6.2 1 6.2L1 4.8ZM14.495 5.99498C14.7683 5.72161 14.7683 5.27839 14.495 5.00503L10.0402 0.550253C9.76684 0.276886 9.32362 0.276886 9.05025 0.550253C8.77689 0.823621 8.77689 1.26684 9.05025 1.5402L13.0101 5.5L9.05025 9.4598C8.77689 9.73317 8.77689 10.1764 9.05025 10.4497C9.32362 10.7231 9.76683 10.7231 10.0402 10.4497L14.495 5.99498ZM1 6.2L14 6.2L14 4.8L1 4.8L1 6.2Z"
            fill="#DBDAE8"
        />
    </svg>
</a>
