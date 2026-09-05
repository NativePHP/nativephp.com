<?php

use App\Enums\PluginCategory;

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Expansion Mode
    |--------------------------------------------------------------------------
    |
    | Controls what happens when a plugin version declares native permissions,
    | entitlements, capabilities, or background modes that its previous version
    | didn't have. See App\Enums\PermissionExpansionMode.
    |
    | "flag" — the new version is logged on the plugin's activity timeline and
    |          published immediately, same as today.
    | "gate" — the new version is withheld from being the plugin's current/
    |          visible version until an admin approves it in Filament.
    |
    */

    'permission_expansion_mode' => env('PLUGIN_PERMISSION_EXPANSION_MODE', 'flag'),

    /*
    |--------------------------------------------------------------------------
    | Supported NativePHP Mobile major versions
    |--------------------------------------------------------------------------
    |
    | The NativePHP Mobile major versions plugins can declare support for via
    | their `mobile_min_version`. Drives the marketplace directory's version
    | filter options, newest first.
    |
    */

    'mobile_major_versions' => [4, 3, 2, 1],

    /*
    |--------------------------------------------------------------------------
    | Category classification keywords
    |--------------------------------------------------------------------------
    |
    | Keywords matched case-insensitively against a plugin's name, description,
    | and repository URL to guess its category for `plugins:backfill-categories`.
    | Checked in the order listed below; the first category with a matching
    | keyword wins.
    |
    */

    'category_keywords' => [
        PluginCategory::Media->value => [
            'camera', 'photo', 'image', 'gallery', 'video', 'audio', 'music',
            'player', 'media', 'barcode', 'qr code', 'scanner', 'speech',
            'text-to-speech', 'text to speech',
        ],
        PluginCategory::Security->value => [
            'biometric', 'face id', 'touch id', 'fingerprint', 'authentication',
            'secure', 'security', 'encryption', 'keychain', 'password',
            'local-auth', 'local auth',
        ],
        PluginCategory::Connectivity->value => [
            'bluetooth', 'nfc', 'wifi', 'wi-fi', 'websocket', 'network',
            'connectivity', 'beacon', 'deep link', 'deep-link',
        ],
        PluginCategory::Notifications->value => [
            'push notification', 'push-notification', 'notification', 'alert',
            'reminder', 'fcm', 'apns',
        ],
        PluginCategory::Payments->value => [
            'payment', 'stripe', 'in-app purchase', 'in app purchase', 'iap',
            'billing', 'checkout', 'subscription', 'paypal', 'wallet',
            'admob', 'advertis',
        ],
        PluginCategory::Analytics->value => [
            'analytics', 'crashlytics', 'sentry', 'firebase', 'tracking',
            'metrics', 'telemetry',
        ],
        PluginCategory::System->value => [
            'device info', 'battery', 'sensor', 'haptic', 'clipboard',
            'keyboard', 'orientation', 'brightness', 'status bar',
            'status-bar', 'splash screen', 'splash-screen', 'app icon',
            'app-icon', 'widget', 'background task', 'background-task',
            'offline sync', 'offline-sync', 'file picker', 'file-picker',
            'contacts', 'calendar', 'geolocation', 'maps', 'health',
        ],
    ],

];
