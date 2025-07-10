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

```php
PushNotifications::enrollForPushNotifications();
```

### `getPushNotificationsToken()`

Retrieves the current FCM token for this device.

**Returns:** `string|null` - The FCM token, or `null` if not available

```php
$token = PushNotifications::getPushNotificationsToken();

if ($token) {
    // Send token to your server
    $this->registerTokenWithServer($token);
} else {
    // Token not available, enrollment may have failed
}
```

## Events

### `TokenGenerated`

Fired when a push notification token is successfully generated.

**Payload:** `string $token` - The FCM token for this device

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\PushNotification\TokenGenerated;

#[On('native:' . TokenGenerated::class)]
public function handlePushToken(string $token)
{
    // Send token to your backend
    $this->sendTokenToServer($token);
}
```

## Example Usage

```php
use Livewire\Component;
use Livewire\Attributes\On;
use Native\Mobile\Facades\PushNotifications;
use Native\Mobile\Events\PushNotification\TokenGenerated;

class NotificationManager extends Component
{
    public function promptForPushNotifications()
    {
        PushNotifications::getPushNotificationsToken();
    }

    #[On('native:'. TokenGenerated::class)]
    public function handlePushNotificationsToken(KitchenSinkService $service, $token)
    {
        $response = $service->sendForPushNotification($token);

        if ($response->successful()) {
           nativephp_alert('Push Notification Sent!',
                'Push notifications will not display while the app is open, close the app and wait one minute to see the notification.');
        }
    }

    public function render()
    {
        return view('livewire.notification-manager');
    }
}
```

## Configuration Requirements

### Firebase Setup

1. Create a Firebase project at [Firebase Console](https://console.firebase.google.com/)
2. Add your mobile app to the project
3. Download `google-services.json` (Android) and `GoogleService-Info.plist` (iOS)
4. Place these files in your Laravel project root
5. Enable push notifications in your NativePHP config:

```php
// config/nativephp.php
return [
    'permissions' => [
        'push_notifications' => true,
    ],
];
```

### Environment Variables

```bash
NATIVEPHP_APP_ID=com.yourcompany.yourapp
FIREBASE_PROJECT_ID=your-firebase-project-id
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

## Error Handling

```php
public function handleRegistrationFailure()
{
    // Common failure scenarios:
    
    // 1. User denied permission
    if (!$this->hasNotificationPermission()) {
        $this->showPermissionExplanation();
        return;
    }
    
    // 2. Network error
    if (!$this->hasNetworkConnection()) {
        $this->showNetworkError();
        return;
    }
    
    // 3. Firebase configuration missing
    if (!$this->hasFirebaseConfig()) {
        Log::error('Firebase configuration missing');
        return;
    }
    
    // 4. Backend API error
    $this->showGenericError();
}
```


## Best Practices

- Request permission at the right time (not immediately on app launch)
- Explain the value of notifications to users
- Handle permission denial gracefully
- Clean up invalid tokens on your backend
- Implement retry logic for network failures
- Log registration events for debugging
- Respect user preferences and provide opt-out
