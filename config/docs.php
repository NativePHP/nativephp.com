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
        'mobile' => 3,
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
        'mobile' => [4],
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
