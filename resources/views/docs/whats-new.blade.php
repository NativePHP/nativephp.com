<x-layout title="What's New - {{ \App\Support\DocsLabels::productName() }} v{{ $version }}">
    <section class="mx-auto mt-10 max-w-3xl px-5 md:mt-20">
        <header class="mb-12 text-center">
            <h1 class="text-4xl font-bold md:text-5xl dark:text-white/90">What's New</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-white/60">
                {{ \App\Support\DocsLabels::productName() }} v{{ $version }} &mdash; every documented change,
                gathered from the version labels across these docs.
            </p>
        </header>

        @if (empty($badges))
            <p class="text-center text-gray-500 dark:text-gray-400">
                Nothing labelled yet for this version. As pages pick up
                <code>&lt;x-docs.version-badge&gt;</code> labels, they'll show up here.
            </p>
        @else
            <div class="space-y-12">
                @foreach ($badges as $minor => $types)
                    <div>
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h2 class="text-2xl font-semibold dark:text-white/90">{{ $minor }}</h2>

                            @if ($link = $changelogLinks[$minor] ?? null)
                                <a
                                    href="{{ $link['url'] }}"
                                    class="text-sm text-gray-500 underline underline-offset-4 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                                >
                                    {{ $link['version'] }} in the {{ $link['label'] }}
                                </a>
                            @endif
                        </div>

                        <div class="mt-4 space-y-6">
                            @foreach (\App\Services\DocsChangelogService::TYPE_LABELS as $type => $label)
                                @continue(empty($types[$type]))
                                <div>
                                    <h3 class="text-sm font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">{{ $label }}</h3>
                                    <ul class="mt-2 space-y-1">
                                        @foreach ($types[$type] as $page)
                                            <li>
                                                <a
                                                    href="{{ $page['path'] }}"
                                                    class="text-indigo-600 underline underline-offset-4 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                >
                                                    {{ $page['title'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-layout>
