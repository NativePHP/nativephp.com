{{--
    Two arms fanning from a single point out to ±32px, matching the 48px
    platform tiles stacked with a 16px gap (centres sit 64px apart).
--}}
<svg
    {{ $attributes->merge(['class' => '[--grad-stop-1:--alpha(var(--color-gray-300)/100%)] [--grad-stop-2:--alpha(var(--color-blue-300)/100%)] dark:[--grad-stop-1:--alpha(var(--color-blue-300)/20%)] dark:[--grad-stop-2:--alpha(var(--color-blue-500)/80%)]']) }}
    xmlns="http://www.w3.org/2000/svg"
    width="67"
    height="68"
    viewBox="0 0 67 68"
    fill="none"
>
    {{-- Upper arm --}}
    <path
        d="M1 34C33.5 34 33.5 34 33.5 18C33.5 2 33.5 2 66 2"
        stroke="url(#bifrost_arc_fan)"
        stroke-width="2"
        stroke-linecap="round"
    />
    {{-- Lower arm --}}
    <path
        d="M1 34C33.5 34 33.5 34 33.5 50C33.5 66 33.5 66 66 66"
        stroke="url(#bifrost_arc_fan)"
        stroke-width="2"
        stroke-linecap="round"
    />
    <defs>
        <linearGradient
            id="bifrost_arc_fan"
            x1="0"
            y1="34"
            x2="67"
            y2="34"
            gradientUnits="userSpaceOnUse"
        >
            <stop stop-color="var(--grad-stop-1)" />
            <stop
                offset="1"
                stop-color="var(--grad-stop-2)"
            />
        </linearGradient>
    </defs>
</svg>
