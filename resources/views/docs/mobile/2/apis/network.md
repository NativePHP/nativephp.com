---
title: Network
order: 400
---

## Overview

The Network API provides access to the device's current network status and connection information. You can check whether the device is connected, determine the connection type, and detect metered or low-bandwidth conditions.

```php
use Native\Mobile\Facades\Network;
```

## Methods

### `status()`

Gets the current network status of the device.

**Returns:** `object|null` - Network status object or `null` if unavailable

The returned object contains the following properties:

- `connected` (bool) - Whether the device is connected to a network
- `type` (string) - The type of connection: `"wifi"`, `"cellular"`, `"ethernet"`, or `"unknown"`
- `isExpensive` (bool) - Whether the connection is metered/cellular (iOS only, always `false` on Android)
- `isConstrained` (bool) - Whether Low Data Mode is enabled (iOS only, always `false` on Android)

```php
$status = Network::status();

if ($status) {
    echo $status->connected; // true/false
    echo $status->type; // "wifi", "cellular", "ethernet", or "unknown"
    echo $status->isExpensive; // true/false (iOS only)
    echo $status->isConstrained; // true/false (iOS only)
}
```

## Notes

- **iOS-specific properties:** The `isExpensive` and `isConstrained` properties are only meaningful on iOS. On Android, these values will always be `false`.

- **Android behavior:** Android reports the basic connection state (connected/type). For Android 10+, the connection type detection is more accurate due to API improvements.

- **Permissions:** Network status monitoring requires the `network_state` permission, which is enabled by default in your NativePHP configuration (`config/nativephp.php`).

- **Snapshot, not stream:** The `status()` method returns a snapshot of the current network state. It's not a real-time stream. Call it whenever you need the current status.

- **No events:** Unlike other APIs, Network doesn't provide events. Call `status()` directly when you need to check the connection or perform periodic checks from your component lifecycle.

- **Real-time monitoring:** For monitoring changes, consider calling `status()` periodically or in response to user actions.
