---
title: Changelog
order: 2
---

## JavaScript/TypeScript Library
A brand-new JavaScript bridge library with full TypeScript declarations for Vue, React, Inertia, and vanilla JS apps. This enables calling native device features directly from your frontend code. Read more about it [here](). 

---

## EDGE - Element Definition and Generation Engine
A new native UI system for rendering navigation components natively on device using Blade. Read more about it [here](../edge-components/introduction).

---

## Laravel Boost Support
Full integration with Laravel Boost for AI-assisted development. Read more about it [here](../getting-started/development#laravel-boost-support).

---
## Hot Module Replacement (HMR) Overhauled
Full Vite HMR for rapid development. Read more about it [here](../getting-started/development#hot-reloading).

Features:
- Custom vite plugin
- Automatic HMR server configuration for iOS/Android
- PHP protocol adapter for axios on iOS (no more inertia patch command!)
- Automatic WebView reload fallback when Vite isn't running
- Works over the network even without a physical device plugged in!

---

##  Fluent Pending API (PHP)
All [Asynchronous Methods](../the-basics/events#understanding-async-vs-sync) now implement a fluent api for better IDE support and ease of use.

### PHP
```php
// Alerts with correlation IDs
Dialog::alert('Confirm', 'Delete this?', ['Cancel', 'Delete'])
    ->id('delete-confirm')
    ->event(MyCustomEvent::class)
    ->remember()
    ->show();
```

### JS
```js
import { dialog, on, off, Events } from '#nativephp';
const label = ref('');


const openAlert = async () => {
    await dialog.alert()
        .title('Alert')
        .message('This is an alert dialog.')
        .buttons(['OK', 'Cool', 'Cancel']);
};

const buttonPressed = (payload: any) => {
    label.value = payload.label;
};

onMounted(() => {
    on(Events.Alert.ButtonPressed, buttonPressed);
});
```

---

##  `#[OnNative]` Livewire Attribute
Learn more about the new OnNative support [here]().

---

##  Video Recording
Learn more about the new Video Recorder support [here]().

---
##  QR/Barcode Scanner
Learn more about the new QR/Barcode Scanner support [here]().

---
##  Microhone
Learn more about the new Microphone support [here]().

---
##  Network Detection
Learn more about the new Network Detection support [here]().

---
##  Background Audio Recording
Just update your config and record audio even while the device is locked!

```php
// config/nativephp.php
'permissions' => [
    'microphone' => true,
    'microphone_background' => true, // NEW
],
```
---
##  Push Notifications API
New fluent API for push notification enrollment with ID correlation and session tracking:

### PHP
```php
use Native\Mobile\Facades\PushNotifications;
use Native\Mobile\Events\PushNotification\TokenGenerated;

// Simple enrollment (auto-executes)
PushNotifications::enroll();

#[OnNative(TokenGenerated::class)]
public function handlePushNotificationsToken($token)
{
    $this->token = $token;
}
```

### JS
```js
import { pushNotifications, on, off, Events } from '#nativephp';

const token = ref('');

const promptForPushNotifications = async () => {
    await pushNotifications.enroll();
};

const handlePushNotificationsToken = (payload: any) => {
    token.value = payload.token;
};

onMounted(() => {
    on(Events.PushNotification.TokenGenerated, handlePushNotificationsToken);
});

onUnmounted(() => {
    off(Events.PushNotification.TokenGenerated, handlePushNotificationsToken);
});
```

**Deprecated Methods:**
- `enrollForPushNotifications()` → use `enroll()`
- `getPushNotificationsToken()` → use `getToken()`

---

##  Platform Improvements

### iOS
- **Platform detection** - `nativephp-ios` class on body
- **Keyboard detection** - `keyboard-visible` class when keyboard shown
- **iOS 26 Liquid Glass** support
- **Improved device selector** showing last-used device
- **Load Times** dramatically decreased by 60-80%!

### Android
- **Android 16+ 16KB page size** compatibility
- **Jetpack Compose UI** - Migrated from XML layouts
- **Platform detection** - `nativephp-android` class on body
- **Parallel zip extraction** for faster installations
- **Load Times** dramatically decreased by ~40%!
- **Page Load Times** dramatically decreased by ~40%!
---

##  Configuration

### New Options
```php
'start_url' => env('NATIVEPHP_START_URL', '/'),

'permissions' => [
    'microphone' => false,
    'microphone_background' => false,
    'scanner' => false,
    'network_state' => true, // defaults to true
],

'ipad' => false,

'orientation' => [
    'iphone' => [...],
    'android' => [...],
],
```

### Custom Permission Reasons (iOS)
```php
'camera' => 'We need camera access to scan membership cards.',
'location' => 'Location is used to find nearby stores.',
```

---

##  New Events

- `Camera\VideoRecorded`, `Camera\VideoCancelled`, `Camera\PhotoCancelled`
- `Microphone\MicrophoneRecorded`, `Microphone\MicrophoneCancelled`
- `Scanner\CodeScanned`

All events now include optional `$id` parameter for correlation.

---

##  Better File System Support
NativePHP now symlinks your filesystems! Persisted storage stays in storage but is symlinked to the public dir for display in the webview! Plus a pre-configured `mobile_public` filesystem disk.

```dotenv
FILESYSTEM_DISK=mobile_public
```
```php
$imageUrl = Storage::url($path);
```
```html
<img :src="$imageurl" />
```

---


## Bug Fixes

- Fixed infinite recursion in some Laravel setups
- Fixed iOS toolbar padding for different device sizes
- Fixed Android debug mode forcing `APP_DEBUG=true`
- Fixed orientation config key case sensitivity (`iPhone` vs `iphone`)

---

## Breaking Changes

- None
