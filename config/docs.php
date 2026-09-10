<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Documentation Latest Versions
    |--------------------------------------------------------------------------
    |
    | This configuration defines the latest stable version for each
    | documentation platform. When a user views an older version, they will
    | see a notice prompting them to view the latest version. Unversioned
    | docs URLs redirect here.
    |
    */

    'latest_versions' => [
        'desktop' => 2,
        'mobile' => 4,
    ],

    /*
    |--------------------------------------------------------------------------
    | Documentation Pre-release Versions
    |--------------------------------------------------------------------------
    |
    | Versions listed here are published but still in beta. They are labelled
    | as beta in the version switcher, display a pre-release notice on every
    | page, and are never the default version users are redirected to.
    |
    */

    'prerelease_versions' => [
        'desktop' => [],
        'mobile' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Released Minor Versions
    |--------------------------------------------------------------------------
    |
    | Every minor release that exists, keyed by platform and then by major.
    | The version labels rendered by <x-docs.version-badge> are checked against
    | this list in the test suite, so a label can't quietly point at a version
    | that was never released — or at one belonging to a different major after
    | a page has been copied forward into a new version's tree.
    |
    | Add the new entry here as part of shipping a release.
    |
    */

    'released_versions' => [
        'desktop' => [
            1 => ['1.0'],
            2 => ['2.0', '2.1', '2.2'],
        ],
        'mobile' => [
            1 => ['1.0', '1.1'],
            2 => ['2.0'],
            3 => ['3.0', '3.1', '3.2', '3.3'],
            4 => ['4.0', '4.1', '4.2'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Screenshot Capture
    |--------------------------------------------------------------------------
    |
    | Where `docs:capture-screenshots` writes captured screenshots. Every run
    | writes to staging_path; `--publish` additionally copies them into
    | publish_path, overwriting the tracked images the docs pages reference.
    |
    | staging_path deliberately sits outside storage/app (the `local`
    | filesystem disk's root) — these are scratch files from a local dev
    | tool, not app-managed disk content.
    |
    | process_timeout bounds each `native:run` / `native:screenshot` call
    | (seconds) — a real simulator/emulator boot and build can take a while.
    |
    | crop_percent is how much of the image height a top/bottom-cropped
    | screenshot keeps (matches every existing top-bar/bottom-nav image in
    | public/img/docs, which are cropped tight rather than full-screen).
    | It's an approximation, not a per-device measurement — review a staged
    | screenshot before publishing and adjust here if a component's bar is
    | taller or shorter than this assumes. --full skips cropping entirely.
    |
    */

    'screenshots' => [
        'staging_path' => storage_path('docs-screenshots'),
        'publish_path' => public_path('img/docs'),
        'process_timeout' => 300,
        'crop_percent' => 0.25,
    ],

    /*
    |--------------------------------------------------------------------------
    | Jump
    |--------------------------------------------------------------------------
    |
    | Jump ships on its own cadence, so a feature can be released in NativePHP
    | and still not render when someone scans the QR code on a docs page.
    |
    | Pages and sections declare the Jump version they need; this value records
    | what Jump currently ships. Bump it when Jump catches up and every label it
    | now satisfies disappears on its own — no docs edits required.
    |
    */

    'jump' => [
        'current_version' => '3.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Renamed Documentation Pages
    |--------------------------------------------------------------------------
    |
    | Pages that were renamed in a given version, keyed by the version the
    | rename happened in, mapping the old path to the new path. Used to map
    | a page to its equivalent when moving between versions (in both
    | directions) and to redirect stale URLs within a version.
    |
    */

    'renamed_pages' => [
        'desktop' => [],
        'mobile' => [
            4 => [
                'the-basics/native-components' => 'the-basics/native-ui',
                'the-basics/dialog' => 'the-basics/dialogs',
                'the-basics/web-view' => 'edge-components/web-view',
                'getting-started/deployment' => 'publishing/introduction',
                'plugins/vibe' => 'digging-deeper/websockets',

                // Concepts section renamed to Digging Deeper
                'concepts/authentication' => 'digging-deeper/authentication',
                'concepts/databases' => 'digging-deeper/databases',
                'concepts/deep-links' => 'digging-deeper/deep-links',
                'concepts/push-notifications' => 'digging-deeper/push-notifications',
                'concepts/queues' => 'digging-deeper/queues',
                'concepts/security' => 'digging-deeper/security',

                // SuperNative overview page now lives in the Architecture section
                'super-native/introduction' => 'architecture/super-native',

                // Some SuperNative pages moved into The Basics
                'super-native/navigation' => 'the-basics/routing',
                'super-native/layouts' => 'the-basics/layouts',
                'super-native/events' => 'the-basics/events',

                // Remaining SuperNative pages flattened into the Digging Deeper section
                'super-native/lifecycle-hooks' => 'digging-deeper/lifecycle-hooks',
                'super-native/data-binding' => 'digging-deeper/data-binding',
                'super-native/reactivity' => 'digging-deeper/reactivity',
                'super-native/theming' => 'digging-deeper/theming',
                'super-native/gestures' => 'digging-deeper/gestures',
                'super-native/search' => 'digging-deeper/search',
                'super-native/accessibility' => 'digging-deeper/accessibility',
            ],
        ],
    ],

];
