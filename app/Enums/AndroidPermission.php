<?php

declare(strict_types=1);

namespace App\Enums;

enum AndroidPermission: string
{
    case Camera = 'android.permission.CAMERA';
    case RecordAudio = 'android.permission.RECORD_AUDIO';
    case AccessFineLocation = 'android.permission.ACCESS_FINE_LOCATION';
    case AccessCoarseLocation = 'android.permission.ACCESS_COARSE_LOCATION';
    case AccessBackgroundLocation = 'android.permission.ACCESS_BACKGROUND_LOCATION';
    case ReadContacts = 'android.permission.READ_CONTACTS';
    case WriteContacts = 'android.permission.WRITE_CONTACTS';
    case ReadCalendar = 'android.permission.READ_CALENDAR';
    case WriteCalendar = 'android.permission.WRITE_CALENDAR';
    case BluetoothConnect = 'android.permission.BLUETOOTH_CONNECT';
    case BluetoothScan = 'android.permission.BLUETOOTH_SCAN';
    case ReadMediaImages = 'android.permission.READ_MEDIA_IMAGES';
    case ReadExternalStorage = 'android.permission.READ_EXTERNAL_STORAGE';
    case BodySensors = 'android.permission.BODY_SENSORS';
    case ActivityRecognition = 'android.permission.ACTIVITY_RECOGNITION';
    case Internet = 'android.permission.INTERNET';

    public function label(): string
    {
        return match ($this) {
            self::Camera => 'Camera',
            self::RecordAudio => 'Microphone',
            self::AccessFineLocation => 'Precise Location',
            self::AccessCoarseLocation => 'Approximate Location',
            self::AccessBackgroundLocation => 'Background Location',
            self::ReadContacts => 'Read Contacts',
            self::WriteContacts => 'Write Contacts',
            self::ReadCalendar => 'Read Calendar',
            self::WriteCalendar => 'Write Calendar',
            self::BluetoothConnect => 'Bluetooth',
            self::BluetoothScan => 'Bluetooth Scanning',
            self::ReadMediaImages => 'Photo Library',
            self::ReadExternalStorage => 'Device Storage',
            self::BodySensors => 'Body Sensors',
            self::ActivityRecognition => 'Motion & Fitness',
            self::Internet => 'Internet Access',
        };
    }

    public function iosUsageDescriptionKey(): ?string
    {
        return match ($this) {
            self::Camera => 'NSCameraUsageDescription',
            self::RecordAudio => 'NSMicrophoneUsageDescription',
            self::AccessFineLocation, self::AccessCoarseLocation => 'NSLocationWhenInUseUsageDescription',
            self::AccessBackgroundLocation => 'NSLocationAlwaysAndWhenInUseUsageDescription',
            self::ReadContacts, self::WriteContacts => 'NSContactsUsageDescription',
            self::ReadCalendar, self::WriteCalendar => 'NSCalendarsUsageDescription',
            self::BluetoothConnect, self::BluetoothScan => 'NSBluetoothAlwaysUsageDescription',
            self::ReadMediaImages, self::ReadExternalStorage => 'NSPhotoLibraryUsageDescription',
            self::BodySensors, self::ActivityRecognition => 'NSMotionUsageDescription',
            self::Internet => null,
        };
    }
}
