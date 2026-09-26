<?php

namespace App\Enums;

enum PluginCategory: string
{
    case Media = 'media';
    case Security = 'security';
    case Connectivity = 'connectivity';
    case Notifications = 'notifications';
    case Payments = 'payments';
    case Analytics = 'analytics';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Media => 'Media',
            self::Security => 'Security',
            self::Connectivity => 'Connectivity',
            self::Notifications => 'Notifications',
            self::Payments => 'Payments',
            self::Analytics => 'Analytics',
            self::System => 'System',
        };
    }

    /**
     * What a plugin in this category is for, so Jev can tell whether a plugin fits it.
     */
    public function description(): string
    {
        return match ($this) {
            self::Media => 'cameras, photos, video, audio, barcode and QR code scanning, or speech',
            self::Security => 'biometrics like Face ID and fingerprints, authentication, encryption, or secure storage like the keychain',
            self::Connectivity => 'Bluetooth, NFC, Wi-Fi, networking, websockets, beacons or deep links',
            self::Notifications => 'push or local notifications, alerts or reminders',
            self::Payments => 'in-app purchases, subscriptions, payment providers like Stripe and PayPal, wallets, or ads',
            self::Analytics => 'usage analytics, crash reporting, tracking, metrics or telemetry',
            self::System => 'device and operating system features like device info, battery, sensors, haptics, the clipboard, keyboard, screen orientation and brightness, the status bar, splash screen, app icon, widgets, background tasks, the file picker, contacts, calendar, geolocation, maps or health data',
        };
    }
}
