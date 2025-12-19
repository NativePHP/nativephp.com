---
title: Device
order: 400
---

## Overview

The Device API exposes internal information about the device, such as the model and operating system version, along with user information such as unique ids.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Device;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { device } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `getId()`

Return a unique identifier for the device.

Returns: `string`

<x-snippet title="Get Device ID">

<x-snippet.tab name="PHP">

```php
$id = Device::getId();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await device.getId();
const deviceId = result.id;
```

</x-snippet.tab>
</x-snippet>

### `getInfo()`

Return information about the underlying device/os/platform.

Returns JSON encoded: `string`

<x-snippet title="Get Device Info">

<x-snippet.tab name="PHP">

```php
$info = Device::getInfo();
$deviceInfo = json_decode($info);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await device.getInfo();
const deviceInfo = JSON.parse(result.info);

console.log(deviceInfo.platform);  // 'ios' or 'android'
console.log(deviceInfo.model);     // e.g., 'iPhone13,4'
console.log(deviceInfo.osVersion); // e.g., '17.0'
```

</x-snippet.tab>
</x-snippet>

### `vibrate()`

Triggers device vibration for tactile feedback.

**Returns:** `void`

<x-snippet title="Vibrate">

<x-snippet.tab name="PHP">

```php
Device::vibrate();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await device.vibrate();
```

</x-snippet.tab>
</x-snippet>

### `flashlight()`

Toggles the device flashlight (camera flash LED) on and off.

**Returns:** `void`

<x-snippet title="Toggle Flashlight">

<x-snippet.tab name="PHP">

```php
Device::flashlight(); // Toggle flashlight state
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await device.flashlight();
console.log(result.state); // true = on, false = off
```

</x-snippet.tab>
</x-snippet>

### `getBatteryInfo()`

Return information about the battery.

Returns JSON encoded: `string`

<x-snippet title="Get Battery Info">

<x-snippet.tab name="PHP">

```php
$info = Device::getBatteryInfo();
$batteryInfo = json_decode($info);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await device.getBatteryInfo();
const batteryInfo = JSON.parse(result.info);

console.log(batteryInfo.batteryLevel); // 0-1 (e.g., 0.85 = 85%)
console.log(batteryInfo.isCharging);   // true/false
```

</x-snippet.tab>
</x-snippet>

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
