<section
    class="mx-auto mt-24 max-w-6xl px-5"
    aria-labelledby="testimonials-heading"
>
    <header class="relative z-10 grid place-items-center text-center">
        {{-- Section Heading --}}
        <h2
            id="testimonials-heading"
            x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
            class="flex items-center gap-2 rounded-bl-md rounded-br-xl rounded-tl-xl rounded-tr-xl bg-gray-100 py-2 pl-4 pr-5 text-xl text-gray-800 opacity-0 dark:bg-gray-900 dark:text-white"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5"
                viewBox="0 0 21 20"
                fill="none"
                aria-hidden="true"
            >
                <path
                    d="M14.25 2.50009C14.2498 1.95806 14.0734 1.43078 13.7475 0.997694C13.4216 0.564609 12.9638 0.249181 12.443 0.0989345C11.9222 -0.0513117 11.3667 -0.0282364 10.8602 0.164683C10.3537 0.357603 9.92358 0.709917 9.63469 1.16854C9.34581 1.62716 9.21379 2.16725 9.25853 2.70743C9.30328 3.24761 9.52235 3.75862 9.88276 4.16346C10.2432 4.5683 10.7254 4.84505 11.2567 4.95199C11.7881 5.05894 12.3398 4.9903 12.8287 4.75642C12.7064 5.52063 12.4924 6.2673 12.1912 6.98025C11.6812 8.17404 10.9475 9.17657 10.0325 10.2116C9.92809 10.3365 9.87652 10.4972 9.88877 10.6595C9.90102 10.8219 9.97612 10.973 10.0981 11.0808C10.2201 11.1886 10.3793 11.2446 10.5419 11.2368C10.7045 11.229 10.8576 11.1581 10.9688 11.0391C11.9275 9.9541 12.7563 8.83781 13.34 7.47026C13.925 6.10022 14.25 4.51391 14.25 2.50009ZM20.5 8.4878V12.8455C20.5 14.618 19.045 16.0531 17.25 16.0531H11.5125L6.49875 19.7544C6.23254 19.9503 5.90015 20.0339 5.57296 19.9874C5.24576 19.9409 4.94985 19.768 4.74875 19.5057C4.58786 19.2934 4.50054 19.0345 4.5 18.7682V16.0531H3.75C1.955 16.0531 0.5 14.6168 0.5 12.8455V4.45766C0.5 2.68634 1.955 1.25004 3.75 1.25004H8.2125C8.075 1.64131 8 2.06257 8 2.50009H3.75C2.63 2.50009 1.75 3.39137 1.75 4.45766V12.8455C1.75 13.9117 2.63 14.803 3.75 14.803H5.75V18.7507H5.75375L5.75625 18.7494L11.1012 14.803H17.25C18.37 14.803 19.25 13.9117 19.25 12.8455V10.5254C19.7125 9.90035 20.1375 9.22908 20.5 8.4878ZM18 6.96169e-08C18.663 6.96169e-08 19.2989 0.263401 19.7678 0.732259C20.2366 1.20112 20.5 1.83702 20.5 2.50009C20.5 4.51266 20.175 6.10022 19.59 7.47026C19.0062 8.83781 18.1775 9.9541 17.2188 11.0391C17.1644 11.1007 17.0985 11.151 17.0247 11.187C16.951 11.2231 16.8708 11.2443 16.7889 11.2494C16.7069 11.2545 16.6248 11.2434 16.5471 11.2168C16.4695 11.1902 16.3978 11.1485 16.3362 11.0941C16.2119 10.9844 16.1363 10.8298 16.126 10.6643C16.1157 10.4988 16.1715 10.3359 16.2812 10.2116C17.1975 9.17657 17.9313 8.17404 18.4412 6.98025C18.7225 6.32022 18.9413 5.5927 19.0788 4.75517C18.7402 4.91723 18.3695 5.00097 17.9941 5.00017C17.6188 4.99938 17.2484 4.91407 16.9105 4.75058C16.5727 4.5871 16.2759 4.34962 16.0424 4.05578C15.8088 3.76194 15.6444 3.41928 15.5614 3.05322C15.4783 2.68716 15.4788 2.30709 15.5627 1.94123C15.6466 1.57536 15.8118 1.23309 16.046 0.939799C16.2803 0.646509 16.5776 0.409733 16.9158 0.24704C17.2541 0.0843465 17.6246 -8.85057e-05 18 6.96169e-08Z"
                    fill="currentColor"
                />
            </svg>
            <div>Testimonials</div>
        </h2>

        {{-- Section Description --}}
        <p
            x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
            class="mx-auto max-w-xl pt-2 text-base/relaxed text-gray-600 opacity-0 dark:text-white/50"
        >
            Here's what folks are saying about NativePHP for mobile
        </p>
    </header>

    {{-- Testimonial List --}}
    <div
        x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $refAll('testimonial'),
                            {
                                scale: [0, 1],
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                                delay: motion.stagger(0.1),
                            },
                        )
                    })
                }
            "
        class="columns-1 pt-2 sm:columns-2 lg:columns-3"
        aria-label="Customer testimonials"
    >
        <x-testimonial
            quote="I have been enjoying NativePHP a lot!"
            author="John Doe"
            handle="@johndoe"
            avatar="https://i.pravatar.cc/200?img=3"
            content="I've been using NativePHP for a while now, and I have to say it's been a great experience. The community is fantastic, and the support is top-notch."
        />

        <x-testimonial
            quote="This framework changed how I build desktop apps!"
            author="Jane Smith"
            handle="@janesmith"
            avatar="https://i.pravatar.cc/200?img=5"
            content="Absolutely incredible tool for creating cross-platform applications with Laravel. The developer experience is top-notch."
        />

        <x-testimonial
            quote="So easy to use and powerful!"
            author="Alex Johnson"
            handle="@alexj"
            avatar="https://i.pravatar.cc/200?img=7"
            content="Finally, a solution that lets me build desktop apps using the Laravel skills I already have. Game changer!"
        />

        <x-testimonial
            quote="I can't wait to see what's next!"
            author="Sarah Brown"
            handle="@sarahb"
            avatar="https://i.pravatar.cc/200?img=9"
            content="NativePHP has been a game-changer for my development workflow. The ease of use and the community support are unparalleled."
        />

        <x-testimonial
            quote="This is the future of desktop app development!"
            author="Michael White"
            handle="@michaelw"
            avatar="https://i.pravatar.cc/200?img=11"
            content="NativePHP has revolutionized how I build desktop applications. The integration with Laravel is seamless and powerful."
        />

        <x-testimonial
            quote="A must-have for any developer!"
            author="Emily Clark"
            handle="@emilyc"
            avatar="https://i.pravatar.cc/200?img=19"
            content="The features and support provided by NativePHP are top-notch. It has significantly improved my productivity."
        />
    </div>
</section>
