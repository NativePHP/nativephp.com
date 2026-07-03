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

];
