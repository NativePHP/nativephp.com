---
title: System
order: 1500
---

## Overview

The System API provides access to basic system functions like flashlight control and platform detection.

```php
use Native\Mobile\Facades\System;
```

## Methods

### `flashlight()`

Toggles the device flashlight (camera flash LED) on and off.

**Returns:** `void`

```php
System::flashlight(); // Toggle flashlight state
```

### `isIos()`

Determines if the current device is running iOS.

**Returns:** `true` if iOS, `false` otherwise

### `isAndroid()`

Determines if the current device is running Android.

**Returns:** `true` if Android, `false` otherwise
