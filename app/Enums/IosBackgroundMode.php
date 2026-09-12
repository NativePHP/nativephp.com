<?php

declare(strict_types=1);

namespace App\Enums;

enum IosBackgroundMode: string
{
    case Audio = 'audio';
    case Fetch = 'fetch';
    case Processing = 'processing';
    case Location = 'location';
    case RemoteNotification = 'remote-notification';
    case BluetoothCentral = 'bluetooth-central';
    case BluetoothPeripheral = 'bluetooth-peripheral';

    public function label(): string
    {
        return match ($this) {
            self::Audio => 'Background Audio',
            self::Fetch => 'Background Fetch',
            self::Processing => 'Background Processing',
            self::Location => 'Background Location',
            self::RemoteNotification => 'Push Notifications',
            self::BluetoothCentral => 'Bluetooth (Central)',
            self::BluetoothPeripheral => 'Bluetooth (Peripheral)',
        };
    }
}
