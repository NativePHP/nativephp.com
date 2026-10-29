<x-layout title="Contributing">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl md:mt-14"
        aria-labelledby="article-title"
    >
        <header class="relative grid place-items-center text-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-slate-500/50"
                aria-hidden="true"
            ></div>

            {{-- Primary Heading --}}
            <h1
                id="article-title"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-5, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mt-8 text-3xl font-extrabold will-change-transform sm:text-4xl"
            >
                Contributing to NativePHP
            </h1>

            {{-- Subtitle --}}
            <p class="max-w-xl pt-4 text-gray-600 dark:text-gray-400">
                Bug fixes, new features, documentation and bug reports all help
                make NativePHP better. Thank you for helping out.
            </p>
        </header>

        {{-- Divider --}}
        <x-divider />

        {{-- Content --}}
        <article
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [5, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="prose mt-2 max-w-none text-gray-600 will-change-transform dark:text-gray-400 dark:prose-headings:text-white"
            aria-labelledby="article-title"
        >
            <h2>Where to contribute</h2>

            <p>
                NativePHP is split across a few repositories on GitHub. Pick the
                one your change belongs to:
            </p>

            <ul>
                <li>
                    <strong>NativePHP for Mobile</strong>
                    lives in
                    <a href="https://github.com/NativePHP/mobile-air">
                        NativePHP/mobile-air
                    </a>
                </li>
                <li>
                    <strong>NativePHP for Desktop</strong>
                    lives in
                    <a href="https://github.com/NativePHP/desktop">
                        NativePHP/desktop
                    </a>
                </li>
                <li>
                    <strong>The documentation and this website</strong>
                    live in
                    <a href="https://github.com/NativePHP/nativephp.com">
                        NativePHP/nativephp.com
                    </a>
                </li>
                <li>
                    <strong>Official plugins</strong>
                    that are open source each have their own repository in the
                    <a href="https://github.com/NativePHP">NativePHP</a>
                    organization
                </li>
            </ul>

            <h2>How to contribute</h2>

            <ol>
                <li>
                    <strong>Fork the repository</strong>
                    to your own GitHub account.
                </li>
                <li>
                    <strong>Clone your fork</strong>
                    locally:
                    <pre><code class="torchlight"><div class="line">git clone git@github.com:your-username/mobile-air.git</div><div class="line">cd mobile-air</div></code></pre>
                </li>
                <li>
                    <strong>Create a branch</strong>
                    with a name that describes your change:
                    <pre><code class="torchlight"><div class="line">git checkout -b feature/my-new-feature</div></code></pre>
                </li>
                <li>
                    <strong>Make your changes</strong>
                    and follow the code style of the repository you're working
                    in.
                </li>
                <li>
                    <strong>Test your changes</strong>
                    to make sure they work as intended and don't break anything
                    else.
                </li>
                <li>
                    <strong>Commit your changes</strong>
                    with clear messages that explain what changed and why.
                </li>
                <li>
                    <strong>Push your branch</strong>
                    to your fork:
                    <pre><code class="torchlight"><div class="line">git push origin feature/my-new-feature</div></code></pre>
                </li>
                <li>
                    <strong>Open a pull request</strong>
                    against the
                    <code>main</code>
                    branch of the original repository. Describe the change, the
                    problem it solves and anything a reviewer should know.
                </li>
                <li>
                    <strong>Respond to review.</strong>
                    A maintainer will review your pull request and may ask for
                    changes. Once it's approved, it gets merged.
                </li>
            </ol>

            <h2>Pull request guidelines</h2>

            <ul>
                <li>
                    Keep each pull request focused. Send unrelated changes as
                    separate pull requests.
                </li>
                <li>Write clear, descriptive commit messages.</li>
                <li>Include tests for any new functionality.</li>
                <li>Make sure all existing tests pass before you submit.</li>
                <li>
                    Update the documentation if your change affects the public
                    API.
                </li>
            </ul>

            <h2>Code style and tests</h2>

            <p>
                Follow the conventions of the code around your change. The
                mobile and desktop packages use
                <a href="https://laravel.com/docs/pint">Laravel Pint</a>
                for formatting, PHPStan for static analysis and Pest for tests.
                You can run all three before you push:
            </p>

            <pre><code class="torchlight"><div class="line">composer qa</div></code></pre>

            <p>Each step also has its own command:</p>

            <ul>
                <li><code>composer format</code></li>
                <li><code>composer analyse</code></li>
                <li><code>composer test</code></li>
            </ul>

            <h2>Reporting bugs</h2>

            <p>
                If you find a bug, open an issue on the repository it affects.
                The
                <a href="https://github.com/NativePHP/mobile-air/issues">
                    mobile
                </a>
                and
                <a href="https://github.com/NativePHP/desktop/issues">
                    desktop
                </a>
                issue trackers are where most reports go. Include as much detail
                as you can:
            </p>

            <ul>
                <li>A clear, descriptive title.</li>
                <li>Steps to reproduce the issue.</li>
                <li>What you expected to happen and what actually happened.</li>
                <li>
                    Your environment: OS, PHP version, Laravel version and
                    NativePHP version.
                </li>
            </ul>

            <p>
                Questions and ideas for improvements are welcome as issues too.
            </p>

            <h2>Security vulnerabilities</h2>

            <p>
                Please
                <strong>don't</strong>
                open a public issue for a security vulnerability. See our
                <a href="{{ route('security') }}">security policy</a>
                for how to report it privately.
            </p>

            <h2>License</h2>

            <p>
                By contributing, you agree that your contributions will be
                licensed under the same license as the repository you're
                contributing to.
            </p>

            <h2>Code of Conduct</h2>

            <p>
                The NativePHP community is dedicated to providing a welcoming
                and inclusive experience for everyone. We expect all
                participants to follow these standards.
            </p>

            <h3>Our pledge</h3>

            <p>
                We as members, contributors, and leaders pledge to make
                participation in our community a harassment-free experience for
                everyone, regardless of age, body size, visible or invisible
                disability, ethnicity, sex characteristics, gender identity and
                expression, level of experience, education, socio-economic
                status, nationality, personal appearance, race, caste, color,
                religion, or sexual identity and orientation.
            </p>

            <p>
                We pledge to act and interact in ways that contribute to an
                open, welcoming, diverse, inclusive, and healthy community.
            </p>

            <h3>Our standards</h3>

            <p>
                Examples of behavior that contributes to a positive environment:
            </p>

            <ul>
                <li>Using welcoming and inclusive language</li>
                <li>
                    Being respectful of differing viewpoints and experiences
                </li>
                <li>Gracefully accepting constructive criticism</li>
                <li>Focusing on what is best for the community</li>
                <li>Showing empathy towards other community members</li>
            </ul>

            <p>Examples of unacceptable behavior:</p>

            <ul>
                <li>
                    The use of sexualized language or imagery, and sexual
                    attention or advances of any kind
                </li>
                <li>
                    Trolling, insulting or derogatory comments, and personal or
                    political attacks
                </li>
                <li>Public or private harassment</li>
                <li>
                    Publishing others' private information without explicit
                    permission
                </li>
                <li>
                    Other conduct which could reasonably be considered
                    inappropriate in a professional setting
                </li>
            </ul>

            <h3>Enforcement</h3>

            <p>
                Instances of abusive, harassing, or otherwise unacceptable
                behavior may be reported to the project team at
                <a href="mailto:support@nativephp.com">support@nativephp.com</a>
                and will be reviewed and investigated promptly and fairly.
            </p>

            <p>
                Project maintainers who do not follow or enforce the Code of
                Conduct in good faith may face temporary or permanent
                repercussions as determined by other members of the project's
                leadership.
            </p>

            <p>
                The
                <a
                    href="https://www.contributor-covenant.org/version/2/1/code_of_conduct/"
                >
                    Contributor Covenant
                </a>
                version 2.1 is the basis for this Code of Conduct.
            </p>

            <h2>Thank you</h2>

            <p>
                Contributors play a big part in making NativePHP what it is.
                Once your pull request is merged, you'll show up in the
                repository's list of contributors on GitHub.
            </p>
        </article>
    </section>
</x-layout>
