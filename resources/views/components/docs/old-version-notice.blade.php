@props(['platform', 'version', 'page'])

@php
    $latestVersion = config("docs.latest_versions.{$platform}");
    $isOldVersion = $latestVersion && (int) $version < (int) $latestVersion;

    if ($isOldVersion) {
        $latestPagePath = resource_path("views/docs/{$platform}/{$latestVersion}/{$page}.md");

        // Handle renamed paths (e.g., apis/* moved to plugins/core/*)
        $remappedPage = $page;
        if (str_starts_with($page, 'apis/')) {
            $remappedPage = 'plugins/core/' . substr($page, 5);
            $remappedPath = resource_path("views/docs/{$platform}/{$latestVersion}/{$remappedPage}.md");
            if (file_exists($remappedPath)) {
                $latestPagePath = $remappedPath;
                $page = $remappedPage;
            }
        }

        $targetPage = file_exists($latestPagePath) ? $page : 'getting-started/introduction';

        $latestUrl = route('docs.show', [
            'platform' => $platform,
            'version' => $latestVersion,
            'page' => $targetPage,
        ]);
    }
@endphp

@if ($isOldVersion)
    <div
        class="mb-6 flex items-center gap-3 rounded-xl bg-amber-50 p-4 text-amber-800 ring-1 ring-amber-200 dark:bg-amber-950/50 dark:text-amber-200 dark:ring-amber-800/50"
        role="alert"
    >
        <svg
            class="size-5 shrink-0"
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
        <p class="text-sm">
            You're viewing an older version of this documentation.
            <a
                href="{{ $latestUrl }}"
                class="font-medium underline underline-offset-2 hover:text-amber-900 dark:hover:text-amber-100"
            >
                View the latest version ({{ $latestVersion }}.x)
            </a>
        </p>
    </div>
@endif
