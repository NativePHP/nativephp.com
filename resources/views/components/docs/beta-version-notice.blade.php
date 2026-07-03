@props(['platform', 'version', 'page'])

@php
    $isPrerelease = in_array((int) $version, config("docs.prerelease_versions.{$platform}", []));

    if ($isPrerelease) {
        $stableVersion = config("docs.latest_versions.{$platform}");
        $stablePagePath = resource_path("views/docs/{$platform}/{$stableVersion}/{$page}.md");
        $targetPage = file_exists($stablePagePath) ? $page : 'getting-started/introduction';

        $stableUrl = route('docs.show', [
            'platform' => $platform,
            'version' => $stableVersion,
            'page' => $targetPage,
        ]);
    }
@endphp

@if ($isPrerelease)
    <div
        class="mb-6 flex items-start gap-3 rounded-2xl bg-yellow-50 px-5 py-4 text-yellow-900 ring-1 ring-yellow-200 dark:bg-yellow-950/40 dark:text-yellow-100 dark:ring-yellow-800/40"
        role="note"
    >
        <svg
            class="mt-0.5 size-5 shrink-0 text-yellow-600 dark:text-yellow-400"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            <path
                fill-rule="evenodd"
                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                clip-rule="evenodd"
            />
        </svg>
        <div class="text-sm leading-relaxed">
            <p class="font-semibold">You're viewing pre-release documentation &mdash; version {{ $version }}.x is in beta</p>
            <p class="mt-1 text-yellow-900/80 dark:text-yellow-100/80">
                Features, APIs and behaviour may change before the stable release.
                <a
                    href="{{ $stableUrl }}"
                    class="font-medium underline underline-offset-2 hover:text-yellow-950 dark:hover:text-yellow-50"
                >
                    View the stable version ({{ $stableVersion }}.x)
                </a>
            </p>
        </div>
    </div>
@endif
