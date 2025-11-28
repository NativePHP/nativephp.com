---
title: Device
order: 400
---

## Overview

The Device API exposes internal information about the device, such as the model and operating system version, along with user information such as unique ids.
```php
use Native\Mobile\Facades\Device;
```

## Methods

### `getId()`

Return a unique identifier for the device.

Returns: `string`

### `getInfo()`

Return information about the underlying device/os/platform.

Returns JSON encoded: `string`


### `vibrate()`

Triggers device vibration for tactile feedback.

**Returns:** `void`

```php
Device::vibrate();
```

### `flashlight()`

Toggles the device flashlight (camera flash LED) on and off.

**Returns:** `void`

```php
Device::flashlight(); // Toggle flashlight state
```


### `getBatteryInfo()`

Return information about the battery.

Returns JSON encoded: `string`

## Device Info

| Prop | Type | Description
|---|---|---|---|
| name | string | The name of the device. For example, "John's iPhone". On iOS 16+ this will return a generic device name without the appropriate entitlements. 
| model | string | The device model. For example, "iPhone13,4". 
| platform | 'ios' \| 'android' | The device platform (lowercase). 
| operatingSystem | string | The operating system of the device. 
| osVersion | string | The version of the device OS. 
| iOSVersion | number | The iOS version number. Only available on iOS. Multi-part version numbers are crushed down into an integer padded to two-digits, e.g., "16.3.1" → `160301`. | 5.0.0 |
| androidSDKVersion | number | The Android SDK version number. Only available on Android. | 5.0.0 |
| manufacturer | string | The manufacturer of the device. 
| isVirtual | boolean | Whether the app is running in a simulator/emulator. 
| memUsed | number | Approximate memory used by the current app, in bytes. Divide by 1,048,576 to get MBs used. 
| webViewVersion | string | The web view browser version. 

## Battery Info

| Prop | Type | Description
|---|---|---|---|
| batteryLevel | number | A percentage (0 to 1) indicating how much the battery is charged.
| isCharging | boolean | Whether the device is charging.
