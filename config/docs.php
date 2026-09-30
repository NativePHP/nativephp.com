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
            4 => ['4.0', '4.1', '4.2', '4.3', '4.4', '4.5'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Separately Versioned Packages
    |--------------------------------------------------------------------------
    |
    | Packages documented inside a platform's docs that ship on their own
    | release line, keyed by the slug a label passes as `package`. Their labels
    | name the package and are checked against the package's own released
    | versions in the test suite, rather than the platform's.
    |
    | Labels at or below the baseline don't render. The baseline is whatever
    | was current when the platform major shipped (mobile-ui 0.3.0 was current
    | when nativephp/mobile 4.0.0 shipped), the same way x.0 never renders for
    | the platform itself.
    |
    | Add the new entry here as part of shipping a release.
    |
    */

    'packages' => [
        'mobile-ui' => [
            'name' => 'nativephp/mobile-ui',
            'baseline' => '0.3',
            'released_versions' => ['0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7'],
        ],
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
