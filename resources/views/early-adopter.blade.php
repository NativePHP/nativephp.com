@php
    use Artesaos\SEOTools\Facades\SEOTools;
    SEOTools::setTitle('Early Adopter Program');
    SEOTools::setDescription('Access NativePHP on iOS and Android by becoming an Early Adopter.');
@endphp

<x-layout :backgroundPattern="true">
    <!-- Catch Phrase and CTA Section -->
    <div class="px-6 py-16 sm:py-28 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">

            <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 dark:text-gray-100 sm:text-5xl">
                NativePHP on
                <span class="underline decoration-purple-500 underline-offset-4">iOS</span>
                and
                <span class="underline decoration-purple-500 underline-offset-4">Android</span>
                is coming!
            </h2>
            <p class="mx-auto mt-6 max-w-xl text-pretty text-lg/8 text-gray-600 dark:text-gray-400">
                Development of NativePHP for iOS has already started and you can have an early access now! Become an Early Adopter by sponsoring Simon for $250 or more and get access to the iOS repo.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row  items-center justify-center gap-6">
                <a href="https://github.com/sponsors/simonhamp" class="rounded-md bg-purple-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">
                    Early access for $250
                </a>
                <a href="/newsletter" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-100">
                    and Sign up for the newsletter
                </a>
            </div>
        </div>
    </div>

    <!-- Rewards Section -->
    <div class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base/7 font-semibold text-teal-600 dark:text-gray-400">Being an Early Adopter</h2>
                <p class="mt-2 text-pretty text-4xl font-semibold tracking-tight text-gray-900 dark:text-gray-100 sm:text-5xl lg:text-balance">
                    What do you get ?
                </p>
                {{--                <p class="mt-6 text-lg/8 text-gray-600">Quis tellus eget adipiscing convallis sit sit eget aliquet quis. Suspendisse eget egestas a elementum pulvinar et feugiat blandit at. In mi viverra elit nunc.</p>--}}
            </div>
            <div class="mx-auto mt-16 max-w-2xl lg:max-w-none">
                <dl class="flex flex-col max-w-xl gap-8 mx-auto">
                    <div class="flex flex-col sm:flex-row">
                        <div class="font-mono p-1 text-2xl sm:text-5xl sm:text-right text-teal-600 dark:text-teal-400 font-semibold mr-10">
                            1.
                        </div>
                        <div class="border rounded p-4 bg-white dark:bg-gray-800 dark:border-gray-700">
                            <dt class="flex items-center gap-x-3 text-base/7 font-semibold text-gray-900 dark:text-gray-100">
                                <x-icons.discord class="size-5 flex-none text-teal-600"/>
                                Access to a private Discord
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base/7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">
                                    You get to decide the direction of the project and have a direct line of communication with Simon and other Early Adopters.
                                </p>
                            </dd>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row">
                        <div class="font-mono p-1 text-2xl sm:text-5xl sm:text-right text-teal-600 dark:text-teal-400 font-semibold mr-10">
                            2.
                        </div>
                        <div class="border rounded p-4 bg-white dark:bg-gray-800 dark:border-gray-700">
                            <dt class="flex items-center gap-x-3 text-base/7 font-semibold text-gray-900 dark:text-gray-100">
                                <x-icons.gift class="size-5 flex-none text-teal-600"/>
                                Lifetime rewards and discounts
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base/7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">
                                    Pay a one-time fee to receive a lifetime of rewards and discounts as NativePHP grows over the coming years.
                                </p>
                            </dd>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row">
                        <div class="font-mono p-1 text-2xl sm:text-5xl sm:text-right text-teal-600 dark:text-teal-400 font-semibold mr-10">
                            3.
                        </div>
                        <div class="border rounded p-4 bg-white dark:bg-gray-800 dark:border-gray-700">
                            <dt class="flex items-center gap-x-3 text-base/7 font-semibold text-gray-900 dark:text-gray-100">
                                <x-icons.book-open-text class="size-5 flex-none text-teal-600"/>
                                Your name in NativePHP history
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base/7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">
                                    Your will be part of the a revolution in PHP development and your name will be memorialized in NativePHP history.
                                </p>
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Why Join Section -->
    <section class="text-gray-800 py-16 sm:py-28">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="prose dark:prose-invert mx-auto">
                <h2 class="text-center">Why Join the Early Adopter Program?</h2>
                <p>Currently, NativePHP is available for Windows, Mac, and Linux, but we believe that the future lies in mobile development. That’s why we are reaching out to you!</p>
                <p>With <strong>significant progress</strong> already made towards enabling
                    <strong>NativePHP on iOS</strong>, we are excited about the possibilities that lie ahead. </p>
                <p>However, to make this vision a reality for both iOS and Android, we need your support. </p>
                <p>As an Early Adopter, you will have a voice in the development process and the opportunity to influence the direction of NativePHP. Your feedback and insights will be invaluable as we work towards making NativePHP a powerful solution for mobile developers.</p>
                <p>By becoming a sponsor, you are investing in the future of PHP development and ensuring that developers can leverage their PHP skills to create robust mobile applications. Imagine being part of a community that not only values open-source collaboration but also actively shapes the tools that will define the next generation of mobile applications. </p>
                <p>Moreover, your sponsorship comes with exclusive rewards including access to a private Discord channel where you can connect with Simon and fellow Early Adopters and lifetime discounts on future NativePHP offerings.</p>
                <p>Join us on this exciting journey to expand NativePHP to mobile platforms.</p>
                <p>Together, we can unlock new possibilities for PHP developers and create a vibrant community that thrives on collaboration and innovation. Your support is crucial, and we can’t wait to see what we can achieve together!</p>
                <p class="text-center">Ready to make a difference?</p>
            </div>

            <x-link-button class="my-4 mx-auto" href="https://github.com/sponsors/simonhamp">Sponsor Simon Now!</x-link-button>
        </div>
    </section>

    <x-footer/>
</x-layout>
