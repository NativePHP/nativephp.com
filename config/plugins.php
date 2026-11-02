<?php

use App\Enums\PluginCategory;

return [

    /*
    |--------------------------------------------------------------------------
    | Supported NativePHP Mobile major versions
    |--------------------------------------------------------------------------
    |
    | The NativePHP Mobile major versions that have plugins, newest first. A
    | plugin's composer.json is checked against each one to find the lowest
    | release it works with, and they're the marketplace's version filter
    | options. After adding a new major, run plugins:backfill-mobile-versions.
    |
    */

    'mobile_major_versions' => [4, 3],

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
