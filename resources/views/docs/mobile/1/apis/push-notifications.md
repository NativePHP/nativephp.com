---
title: PushNotifications
order: 600
---

## Overview

The PushNotifications API handles device registration for Firebase Cloud Messaging to receive push notifications.

```php
use Native\Mobile\Facades\PushNotifications;
```

## Methods

### `enrollForPushNotifications()`

Requests permission and enrolls the device for push notifications.

**Returns:** `void`

### `getPushNotificationsToken()`

Retrieves the current FCM token for this device.

**Returns:** `string|null` - The FCM token, or `null` if not available

## Events

### `TokenGenerated`

Fired when a push notification token is successfully generated.

**Payload:** `string $token` - The FCM token for this device

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\PushNotification\TokenGenerated;

#[On('native:'.TokenGenerated::class)]
public function handlePushToken(string $token)
{
    // Send token to your backend
    $this->sendTokenToServer($token);
}
```

## Permission Flow

1. User taps "Enable Notifications"
2. App calls `enrollForPushNotifications()`
3. System shows permission dialog
4. If granted, FCM generates token
5. `TokenGenerated` event fires with token
6. App sends token to backend
7. Backend stores token for user
8. Server can now send notifications to this device

## Best Practices

- Request permission at the right time (not immediately on app launch)
- Explain the value of notifications to users
- Handle permission denial gracefully
