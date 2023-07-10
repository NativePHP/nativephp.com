<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>NativePHP | Baking Delicious Native Apps</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link
            href="https://fonts.bunny.net/css?family=be-vietnam-pro:700|inter:400,500,600|rubik:400,700"
            rel="stylesheet"
        />

        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>
    <body class="w-full h-screen text-white bg-[#4d547e]">
        <main id="app">
            <header class="2xl:min-h-screen md:py-12 flex flex-col items-center justify-center text-center">
                <img src="/icon.png" alt="NativePHP logo" class="w-48">
                <h1 class="sr-only">NativePHP</h1>
                <h2 class="lg:text-8xl md:text-6xl drop-shadow-2xl md:mt-9 mt-6 text-3xl font-bold">
                    Go <em>native</em>... with PHP!
                </h2>
                <h3 class="lg:text-2xl md:text-xl px-6 mt-6 text-lg leading-tight">
                    NativePHP is a new way to build native applications,
                    <br class="sm:block hidden">
                    using the tools you already know.
                </h3>
                <div class="sm:flex-row sm:space-x-6 flex flex-col items-center mt-6">
                    <a href="/docs/" class="sm:w-auto focus:outline-none w-full px-12 py-4 text-lg font-bold text-gray-900 bg-white border border-white rounded-lg">
                        Get started
                    </a>
                    <a href="https://github.com/nativephp/laravel" target="_blank" class="sm:w-auto focus:outline-none sm:mt-0 w-full px-12 py-4 mt-3 text-lg font-bold text-white bg-transparent border border-white rounded-lg">
                        Source code
                    </a>
                </div>

                <h4 class="mt-12 text-lg font-semibold text-gray-100">
                    Awesome Sponsors
                </h4>
                <ul class="lg:gap-12 drop-shadow lg:px-0 flex flex-wrap items-center justify-center gap-3 px-6 mt-6">
                    <li>
                        <a href="https://beyondco.de/?ref=nativephp" target="_blank" rel="noopener" class="table">
                            <img src="/sponsors/beyondcode.svg" class="w-auto h-8">
                        </a>
                    </li>
                </ul>
            </header>

            <section class="sm:py-24 md:w-full sm:w-2/3 container max-w-5xl py-12 text-center">
                <h4 class="md:text-3xl text-2xl font-bold text-center">Why you should use NativePHP</h4>
                <ul class="md:flex md:mt-12 md:space-3 md:px-0 px-6 mt-6">
                    <li class="md:w-1/2">
                        <div class="border-gray-700/50 md:w-40 md:h-40 flex items-center justify-center w-32 h-32 p-6 mx-auto border rounded-full">
                            <div class="from-gray-500 to-black bg-gradient-to-br md:w-32 md:h-32 container flex items-center justify-center w-24 h-24 max-w-3xl rotate-45 rounded-full select-none" style="padding:1px">
                                <figure class="md:w-32 md:h-32 flex items-center justify-center w-24 h-24 bg-black rounded-full">
                                    <svg viewBox="0 0 24 24" fill="none" class="stroke-pink w-12 h-12 -rotate-45">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"></path>
                                    </svg>
                                </figure>
                            </div>
                        </div>
                        <h4 class="lg:text-2xl mt-6 text-xl font-bold">
                            Desktop Domination
                        </h4>
                        <p class="lg:text-lg mt-3 leading-relaxed text-gray-400">
                            With NativePHP you can build native desktop applications for Windows, macOS, and Linux.
                            Turn your existing PHP web apps into cross-platform desktop apps in <em>minutes</em>.
                        </p>
                    </li>
                    <li class="md:w-1/2 md:mt-0 mt-12">
                        <div class="border-gray-700/50 md:w-40 md:h-40 flex items-center justify-center w-32 h-32 p-6 mx-auto border rounded-full">
                            <div class="from-gray-500 to-black bg-gradient-to-br md:w-32 md:h-32 container flex items-center justify-center w-24 h-24 max-w-3xl rotate-45 rounded-full select-none" style="padding:1px">
                                <figure class="md:w-32 md:h-32 flex items-center justify-center w-24 h-24 bg-black rounded-full">
                                    <svg viewBox="0 0 24 24" fill="none" class="stroke-green w-12 h-12 -rotate-45">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path>
                                    </svg>
                                </figure>
                            </div>
                        </div>
                        <h4 class="lg:text-2xl mt-6 text-xl font-bold">
                            Code in Comfort
                        </h4>
                        <p class="lg:text-lg mt-3 leading-relaxed text-gray-400">
                            Use the tools you already know and love. NativePHP will have drivers
                            for all major PHP frameworks, including Laravel, Symfony, and more.
                            Build for the web, bundle for desktop. Done.
                        </p>
                    </li>
                </ul>

                <p class="md:text-2xl mt-16 text-xl font-bold text-center">
                    No new languages. One simple library
                </p>
                <div class="md:px-0 px-6">
                    <div class="from-gray-700 via-black to-black bg-gradient-to-bl flex items-center justify-center mb-6 rounded-[0.55rem] select-none mt-6 container max-w-3xl" style="padding:1px">
                            <pre class="md:pb-9 md:px-12 md:pt-8 w-full px-6 pt-5 pb-6 overflow-x-auto leading-8 text-left bg-black rounded-lg"><span class="text-gray-400">&lt;?php</span>

    <span class="text-pink">it</span>(<span class="text-green">'has a welcome page'</span>, <span class="text-cyan">function</span> () {
        $response = <span class="text-pink">$this</span><span class="text-gray-400">-&gt;</span><span class="text-cyan">get</span>(<span class="text-green">'/'</span>);

        <span class="text-cyan">expect</span>($response<span class="text-gray-400">-&gt;</span><span class="text-cyan">status</span>())<span class="text-gray-400">-&gt;</span><span class="text-cyan">toBe</span>(<span class="text-pink">200</span>);
    });</pre></div>
                </div>
            </section>

            <section class="md:py-12 md:px-0 md:space-y-24 container max-w-4xl px-6 py-6 space-y-12 text-center">
                <h4 class="md:text-3xl text-2xl font-bold text-center">What they say…</h4>

                <blockquote class="sm:text-3xl md:text-6xl text-2xl font-semibold text-center">
                    <p>“NativePHP blew my mind!”</p>
                    <footer class="md:mt-9 md:text-lg mt-6 text-base">
                        <b class="inline-flex items-center">
                            <span>Taylor Otwell</span>
                            <svg viewBox="0 0 24 24" class="fill-white w-4 h-4 ml-1"><path d="M23.334 11.96c-.713-.726-.872-1.829-.393-2.727.342-.64.366-1.401.064-2.062-.301-.66-.893-1.142-1.601-1.302-.991-.225-1.722-1.067-1.803-2.081-.059-.723-.451-1.378-1.062-1.77-.609-.393-1.367-.478-2.05-.229-.956.347-2.026.032-2.642-.776-.44-.576-1.124-.915-1.85-.915-.725 0-1.409.339-1.849.915-.613.809-1.683 1.124-2.639.777-.682-.248-1.44-.163-2.05.229-.61.392-1.003 1.047-1.061 1.77-.082 1.014-.812 1.857-1.803 2.081-.708.16-1.3.642-1.601 1.302s-.277 1.422.065 2.061c.479.897.32 2.001-.392 2.727-.509.517-.747 1.242-.644 1.96s.536 1.347 1.17 1.7c.888.495 1.352 1.51 1.144 2.505-.147.71.044 1.448.519 1.996.476.549 1.18.844 1.902.798 1.016-.063 1.953.54 2.317 1.489.259.678.82 1.195 1.517 1.399.695.204 1.447.072 2.031-.357.819-.603 1.936-.603 2.754 0 .584.43 1.336.562 2.031.357.697-.204 1.258-.722 1.518-1.399.363-.949 1.301-1.553 2.316-1.489.724.046 1.427-.249 1.902-.798.475-.548.667-1.286.519-1.996-.207-.995.256-2.01 1.145-2.505.633-.354 1.065-.982 1.169-1.7s-.135-1.443-.643-1.96zm-12.584 5.43l-4.5-4.364 1.857-1.857 2.643 2.506 5.643-5.784 1.857 1.857-7.5 7.642z"></path></svg>
                        </b>
                        &middot;
                        <span class="text-pink">
                            Creator of <a href="https://laravel.com?ref=nativephp" target="_blank" class="hover:underline">Laravel</a></span>
                    </footer>
                </blockquote>

                <blockquote class="sm:text-3xl md:text-5xl text-2xl font-semibold text-center">
                    <p>“I never thought we'd build desktop apps with PHP.<br> I still can't believe we can”</p>
                    <footer class="md:text-lg mt-6 text-base">
                        Freek Van der Herten
                        &middot;
                        <span class="text-cyan">Developer at <a href="https://spatie.be?ref=nativephp" target="_blank" class="hover:underline">Spatie</a></span>
                    </footer>
                </blockquote>
            </section>

            <section class="md:pt-12 md:px-0 container max-w-3xl px-6">
                <h4 class="md:text-5xl text-3xl font-bold text-center">FAQs</h4>
                <div class="from-gray-700 via-black to-black bg-gradient-to-bl flex items-center justify-center rounded-[0.55rem] select-none container max-w-3xl mt-12" style="padding:1px">
                    <ul class="md:p-12 md:text-left p-6 mx-auto space-y-12 text-center bg-black rounded-lg">
                        <li>
                            <h5 class="text-2xl font-bold">How!?</h5>
                            <p class="mt-3 md:mt-1.5 text-gray-200">
                                <b class="text-green">NativePHP bundles PHP with your app</b>, so you can distribute it
                                as a single file. The latest, greatest and most secure version of PHP, statically built
                                so you can run your existing app with almost no changes to your code. It then bundles
                                your app inside a browser runtime like Electron or Tauri. Your users don't need to
                                install PHP or any other dependencies, just your app.
                            </p>
                            <hr class="from-transparent md:from-white/50 via-white/25 md:via-transparent to-transparent bg-gradient-to-r h-[2px] rounded border-0 mt-6">
                        </li>
                        <li>
                            <h5 class="text-2xl font-bold">Is it safe?</h5>
                            <p class="mt-3 md:mt-1.5 text-gray-200">
                                <b class="text-green">NativePHP is in alpha right now.</b> We're building apps with it
                                today, but we're not ready to recommend it for production use just yet. It's already
                                very secure and we're working on a number of features to make it even more secure.
                                The main consideration right now is that your PHP code is not obfuscated, encrypted
                                or compiled in any way.
                            </p>
                            <hr class="from-transparent md:from-white/50 via-white/25 md:via-transparent to-transparent bg-gradient-to-r h-[2px] rounded border-0 mt-6">
                        </li>
                        <li>
                            <h5 class="text-2xl font-bold">Is it really free?</h5>
                            <p class="mt-1.5 text-gray-200">
                                <b class="text-green">Yes</b>, the core NativePHP libraries and documentation are fully
                                open-source and free to use and remix however you like. NativePHP is released under the
                                <a href="https://https://opensource.org/licenses/MIT" class="decoration-gray-600 hover:decoration-white decoration-2 underline">MIT license</a>,
                                which means you can even use it in commercial projects.
                            </p>
                            <hr class="from-transparent md:from-white/50 via-white/25 md:via-transparent to-transparent bg-gradient-to-r h-[2px] rounded border-0 mt-6">
                        </li>
                        <li>
                            <h5 class="text-2xl font-bold">Who maintains NativePHP?</h5>
                            <p class="mt-3 md:mt-1.5 text-gray-200">
                                <b class="text-green">
                                    NativePHP is a project by
                                    <a href="https://twitter.com/mpociot" target="_blank">Marcel Pociot</a> and
                                    <a href="https://twitter.com/simonhamp" target="_blank">Simon Hamp</a>
                                </b>
                                who are committed to developing and enhancing the framework. We are always open to feedback and
                                <a href="https://github.com/nativephp/laravel/blob/main/contributions.md" target="_blank">contributions</a>
                                from the community.
                            </p>
                        </li>
                    </ul>
                </div>
            </section>

            <footer class="md:px-0 p-12">
                <small class="text-white/75 md:text-xs block text-sm text-center">© <span x-data="" x-text="(new Date()).getFullYear()">2023</span> NativePHP</small>
            </footer>

        </main>

    </body>
</html>
