---
title: System
order: 1500
---

## Overview

The System API provides access to basic system functions like flashlight control and platform detection.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\System;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { system } from '#nativephp';
// or import individual functions
import { isIos, isAndroid, isMobile } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `flashlight()` - Deprecated, see [Device](device)

Toggles the device flashlight (camera flash LED) on and off.

**Returns:** `void`

<x-snippet title="Toggle Flashlight">

<x-snippet.tab name="PHP">

```php
System::flashlight(); // Toggle flashlight state
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Use device.flashlight() instead
import { device } from '#nativephp';

await device.flashlight();
```

</x-snippet.tab>
</x-snippet>

### `isIos()`

Determines if the current device is running iOS.

**Returns:** `true` if iOS, `false` otherwise

<x-snippet title="Check iOS">

<x-snippet.tab name="PHP">

```php
if (System::isIos()) {
    // iOS-specific code
}
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const ios = await system.isIos();

if (ios) {
    // iOS-specific code
}
```

</x-snippet.tab>
</x-snippet>

### `isAndroid()`

Determines if the current device is running Android.

**Returns:** `true` if Android, `false` otherwise

<x-snippet title="Check Android">

<x-snippet.tab name="PHP">

```php
if (System::isAndroid()) {
    // Android-specific code
}
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const android = await system.isAndroid();

if (android) {
    // Android-specific code
}
```

</x-snippet.tab>
</x-snippet>

### `isMobile()`

Determines if the current device is running Android or iOS.

**Returns:** `true` if Android or iOS, `false` otherwise

<x-snippet title="Check Mobile Platform">

<x-snippet.tab name="PHP">

```php
if (System::isMobile()) {
    // Mobile-specific code
}
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const mobile = await system.isMobile();

if (mobile) {
    // Mobile-specific code
}
```

</x-snippet.tab>
</x-snippet>
