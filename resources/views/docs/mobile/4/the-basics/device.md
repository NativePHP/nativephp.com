---
title: Device
order: 110
---

## Overview

The `Device` API exposes device hardware and information — vibration, the flashlight, a stable device
identifier, and device + battery details.

It's a **core built-in**: the facade resolves with nothing to install or register.

```php
use Native\Mobile\Facades\Device;
```

## Vibrate

Trigger a short haptic tap:

```php
$fired = Device::vibrate(); // bool — true if the haptic actually fired
```

On Android this uses the `VIBRATE` permission, which ships in the app manifest automatically. iOS needs no
permission.

## Flashlight

Toggle the rear flashlight on or off and read back the new state:

```php
$result = Device::flashlight();
// ['success' => true, 'state' => true]   state: true = on, false = off
```

| Key | Type | Description |
| --- | --- | --- |
| `success` | bool | Whether the toggle succeeded |
| `state` | bool | The flashlight state after toggling (`true` = on) |

On Android this uses the `FLASHLIGHT` permission, included in the manifest automatically.

## Device identifier

```php
$id = Device::getId(); // ?string
```

A stable per-install identifier — `identifierForVendor` on iOS, `ANDROID_ID` on Android. Returns `null` when
called off-device (e.g. in tests or the web preview).

## Device info

`getInfo()` returns a JSON **string**, so decode it before use:

```php
$info = json_decode(Device::getInfo(), true);

$info['platform'];   // 'ios' | 'android'
$info['model'];      // e.g. 'iPhone15,3'
```

| Field | Description |
| --- | --- |
| `name` | Device name |
| `model` | Device model identifier |
| `platform` | `'ios'` or `'android'` |
| `operatingSystem` | OS name |
| `osVersion` | OS version string |
| `manufacturer` | Device manufacturer |
| `language` | Device language as a BCP 47 tag (e.g. `en-US`) |
| `isVirtual` | Whether running in a simulator/emulator |
| `memUsed` | Memory usage in bytes |
| `webViewVersion` | WebView version |

<aside>

`System::isIos()`, `isAndroid()`, and `isMobile()` are thin conveniences over `getInfo()['platform']` — reach
for those when you only need platform detection. See [System](system).

</aside>

## Battery

`getBatteryInfo()` also returns a JSON string:

```php
$battery = json_decode(Device::getBatteryInfo(), true);

$battery['batteryLevel']; // 0.0 – 1.0
$battery['isCharging'];    // bool
```

| Field | Description |
| --- | --- |
| `batteryLevel` | Battery level from `0.0` to `1.0` |
| `isCharging` | Whether the device is charging |

## Permissions

| Platform | Permissions |
| --- | --- |
| Android | `VIBRATE`, `FLASHLIGHT` — merged into your manifest automatically |
| iOS | None |

<aside>

These functions are also callable from JavaScript in a web view via the `Native` library — see
[Native Functions](../the-basics/native-functions).

</aside>
