---
title: PushNotifications
order: 1100
---

## Overview

The PushNotifications API handles device registration for Firebase Cloud Messaging to receive push notifications.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\PushNotifications;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { pushNotifications, on, off, Events } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `enroll()`

Requests permission and enrolls the device for push notifications.

**Returns:** `void`

<x-snippet title="Enroll for Push Notifications">

<x-snippet.tab name="PHP">

```php
PushNotifications::enroll();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Basic enrollment
await pushNotifications.enroll();

// With identifier for tracking
await pushNotifications.enroll()
    .id('main-enrollment')
    .remember();
```

</x-snippet.tab>
</x-snippet>

### `getToken()`

Retrieves the current push notification token for this device.

**Returns:** `string|null` - The FCM token, or `null` if not available

<x-snippet title="Get Push Token">

<x-snippet.tab name="PHP">

```php
$token = PushNotifications::getToken();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await pushNotifications.getToken();
const token = result.token; // APNS token on iOS, FCM token on Android
```

</x-snippet.tab>
</x-snippet>

## Events

### `TokenGenerated`

Fired when a push notification token is successfully generated.

**Payload:** `string $token` - The FCM token for this device

<x-snippet title="TokenGenerated Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\PushNotification\TokenGenerated;

#[OnNative(TokenGenerated::class)]
public function handlePushToken(string $token)
{
    // Send token to your backend
    $this->sendTokenToServer($token);
}
```

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { onMounted, onUnmounted } from 'vue';

const handleTokenGenerated = (payload) => {
    const { token } = payload;
    // Send token to your backend
    sendTokenToServer(token);
};

onMounted(() => {
    on(Events.PushNotification.TokenGenerated, handleTokenGenerated);
});

onUnmounted(() => {
    off(Events.PushNotification.TokenGenerated, handleTokenGenerated);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useEffect } from 'react';

const handleTokenGenerated = (payload) => {
    const { token } = payload;
    // Send token to your backend
    sendTokenToServer(token);
};

useEffect(() => {
    on(Events.PushNotification.TokenGenerated, handleTokenGenerated);

    return () => {
        off(Events.PushNotification.TokenGenerated, handleTokenGenerated);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

## Permission Flow

1. User taps "Enable Notifications"
2. App calls `enroll()`
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
