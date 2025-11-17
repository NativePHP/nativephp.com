---
title: System
order: 800
---

## Overview

The System API provides access to basic system functions like flashlight control, platform detection, and network status monitoring.

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

### `getNetworkStatus()`

Gets the current network connection status and details.

**Returns:** `?object` containing network information, or `null` if unavailable

```php
$status = System::getNetworkStatus();

if ($status) {
    // Check if connected
    if ($status->connected) {
        echo "Connected via: " . $status->type; // "wifi", "cellular", "ethernet", or "unknown"

        // Check if using expensive/metered connection
        if ($status->isExpensive) {
            echo "Using cellular data - consider limiting usage";
        }

        // Check if Low Data Mode is enabled (iOS only)
        if ($status->isConstrained) {
            echo "Low Data Mode is enabled";
        }
    } else {
        echo "No network connection";
    }
}
```

**Response Object Properties:**

- `connected` (bool) - Whether the device has network connectivity
- `type` (string) - Connection type: `"wifi"`, `"cellular"`, `"ethernet"`, or `"unknown"`
- `isExpensive` (bool) - Whether the connection is metered/cellular
- `isConstrained` (bool) - Whether Low Data Mode is enabled (iOS only, always `false` on Android)

**Configuration:**

Network state detection is enabled by default. You can disable it in `config/nativephp.php`:

```php
'permissions' => [
    'network_state' => true, // Set to false to disable
],
```

**Platform Notes:**

- **iOS:** Uses `NWPathMonitor` from the Network framework. No additional permissions required.
- **Android:** Requires `ACCESS_NETWORK_STATE` permission (automatically added when enabled)
