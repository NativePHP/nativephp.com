---
title: Changelog
order: 2
---

## JavaScript/TypeScript Library
A brand-new JavaScript bridge library with full TypeScript declarations for Vue, React, Inertia, and vanilla JS apps.
This enables calling native device features directly from your frontend code. Read more about it
[here](../the-basics/native-functions#run-from-anywhere). 

## EDGE - Element Definition and Generation Engine
A new native UI system for rendering navigation components natively on device using Blade. Read more about it [here](../edge-components/introduction).

## Laravel Boost Support
Full integration with Laravel Boost for AI-assisted development. Read more about it [here](../getting-started/development#laravel-boost).

## Hot Module Replacement (HMR) Overhauled
Full Vite HMR for rapid development. Read more about it [here](../getting-started/development#hot-reloading).

Features:
- Custom Vite plugin
- Automatic HMR server configuration for iOS/Android
- PHP protocol adapter for axios on iOS (no more `patch-inertia` command!)
- Works over the network even without a physical device plugged in!

##  Fluent Pending API (PHP)
All [Asynchronous Methods](../the-basics/events#understanding-async-vs-sync) now implement a fluent API for better IDE support and ease of use.

### PHP
```php
Dialog::alert('Confirm', 'Delete this?', ['Cancel', 'Delete'])
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

##  `#[OnNative]` Livewire Attribute
Forget the silly string concatenation of yesterday; get into today's fashionable attribute usage with this drop-in
replacement:

```php
use Livewire\Attributes\OnNative; // [tl! remove]
use Native\Mobile\Attributes\OnNative; // [tl! add]

#[On('native:'.ButtonPressed::class)] // [tl! remove]
#[OnNative(ButtonPressed::class)] // [tl! add]
public function handle()
```

##  Video Recording
Learn more about the new Video Recorder support [here](../apis/camera#coderecordvideocode).

##  QR/Barcode Scanner
Learn more about the new QR/Barcode Scanner support [here](../apis/scanner).

##  Microphone
Learn more about the new Microphone support [here](../apis/microphone).

##  Network Detection
Learn more about the new Network Detection support [here](../apis/network).

##  Background Audio Recording
Just update your config and record audio even while the device is locked!

```php
// config/nativephp.php
'permissions' => [
    'microphone' => true,
    'microphone_background' => true,
],
```
##  Push Notifications API
New fluent API for push notification enrollment:

### PHP
```php
use Native\Mobile\Facades\PushNotifications;
use Native\Mobile\Events\PushNotification\TokenGenerated;

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

##  Platform Improvements

### iOS
- **Platform detection** - `nativephp-ios` class on body
- **Keyboard detection** - `keyboard-visible` class when keyboard shown
- **iOS 26 Liquid Glass** support
- **Improved device selector** on `native:run` showing last-used device
- **Load Times** dramatically improved. Now 60-80% faster!

### Android
- **Complete Android 16+ 16KB page size** compatibility
- **Jetpack Compose UI** - Migrated from XML layouts
- **Platform detection** - `nativephp-android` class on body
- **Keyboard detection** - `keyboard-visible` class when keyboard shown
- **Parallel zip extraction** for faster installations
- **Load Times** dramatically improved. ~40% faster!
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

##  New Events

- `Camera\VideoRecorded`, `Camera\VideoCancelled`, `Camera\PhotoCancelled`
- `Microphone\MicrophoneRecorded`, `Microphone\MicrophoneCancelled`
- `Scanner\CodeScanned`

## Custom Events

Many native calls now accept custom event classes!

```php
Dialog::alert('Confirm', 'Delete this?', ['Cancel', 'Delete'])
    ->event(MyCustomEvent::class)
```

##  Better File System Support
NativePHP now symlinks your filesystems! Persisted storage stays in storage but is symlinked to the public directory for
display in the web view! Plus a pre-configured `mobile_public` filesystem disk.

```dotenv
FILESYSTEM_DISK=mobile_public
```
```php
$imageUrl = Storage::url($path);
```
```html
<img :src="$imageurl" />
```

## Bug Fixes

- Fixed infinite recursion during bundling in some Laravel setups
- Fixed iOS toolbar padding for different device sizes
- Fixed Android debug mode forcing `APP_DEBUG=true`
- Fixed orientation config key case sensitivity (`iPhone` vs `iphone`)

## Breaking Changes

- None
