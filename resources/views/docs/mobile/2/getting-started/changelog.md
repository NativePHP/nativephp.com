---
title: Changelog
order: 2
---

## v2.1.1

### Foreground permissions
Prevent removal of FOREGROUND_SERVICE and POST_NOTIFICATIONS permissions when they're needed by camera features

### Symlink fix
Exclude public/storage from build to prevent symlink conflicts

### iOS Push Notifications
Handles push notification APNS flow differently, fires off the native event as soon as the token is received from FCM vs assuming the AppDelegate will ahndle it.

### Fix Missing $id param on some events
Some events were missing an `$id` parameter, which would cause users to experience errors when trying to receive an ID from the event.

## v2.1.0

### Cleaner Console Output
The `native:run` command now provides cleaner, more readable output, making it easier to follow what's happening during development.

### Improved Windows Support
Better compatibility and smoother development experience for Windows users.
<aside>

#### Note to all users
Internally, Gradle has been upgraded, the first time you run an Android build it will take several minutes longer to download and install the new dependencies.

</aside>

### Blade Directives
New Blade directives for conditional rendering based on platform:

```blade
Only rendered in mobile apps
@mobile / @endmobile

Only rendered in web browsers 
@web / @endweb

Only rendered on iOS
@ios / @endios

Only rendered on Android
@android / @endandroid
```

### Improved File Watcher
The file watcher has been completely overhauled, switching from fswatch to [Watchman](https://facebook.github.io/watchman/) for better performance and reliability. The watcher is now combined with Vite HMR for a unified development experience.

### Common URL Schemes
NativePHP now automatically handles common URL schemes, opening them in the appropriate native app:
- `tel:` - Phone calls
- `mailto:` - Email
- `sms:` - Text messages
- `geo:` - Maps/location
- `facetime:` - FaceTime video calls
- `facetime-audio:` - FaceTime audio calls

### Android Deep Links
Support for custom deep links and app links on Android, allowing other apps and websites to link directly into your app.

### Other Changes
- `System::appSettings()` to open your app's settings screen in the OS Settings app
- `Edge::clear()` to remove all EDGE components
- Added `Native.shareUrl()` to the JavaScript library
- `native:install`: Added `--fresh` and `-F` as aliases of `--force`
- `native:install`: Increased timeout for slower networks

### Bug Fixes
- Fixed Scanner permissions
- Fixed Android edge-to-edge display
- Fixed `Browser::auth` on iOS
- Fixed text alignment in native top-bar component on iOS
- Fixed plist issues on iOS
- Fixed `NATIVEPHP_START_URL` configuration
- Fixed camera cancelled events on Android
- Fixed bottom-nav values not updating dynamically

## v2.0.0

### JavaScript/TypeScript Library
A brand-new JavaScript bridge library with full TypeScript declarations for Vue, React, Inertia, and vanilla JS apps.
This enables calling native device features directly from your frontend code. Read more about it
[here](../the-basics/native-functions#run-from-anywhere).

### EDGE - Element Definition and Generation Engine
A new native UI system for rendering navigation components natively on device using Blade. Read more about it [here](../edge-components/introduction).

### Laravel Boost Support
Full integration with Laravel Boost for AI-assisted development. Read more about it [here](../getting-started/development#laravel-boost).

### Hot Module Replacement (HMR) Overhauled
Full Vite HMR for rapid development. Read more about it [here](../getting-started/development#hot-reloading).

Features:
- Custom Vite plugin
- Automatic HMR server configuration for iOS/Android
- PHP protocol adapter for axios on iOS (no more `patch-inertia` command!)
- Works over the network even without a physical device plugged in!

###  Fluent Pending API (PHP)
All [Asynchronous Methods](../the-basics/events#understanding-async-vs-sync) now implement a fluent API for better IDE support and ease of use.

<x-snippet title="Fluent APIs">

<x-snippet.tab name="PHP">

```php
Dialog::alert('Confirm', 'Delete this?', ['Cancel', 'Delete'])
    ->remember()
    ->show();
```

</x-snippet.tab>
<x-snippet.tab name="Vue">

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

</x-snippet.tab>
</x-snippet>

###  `#[OnNative]` Livewire Attribute
Forget the silly string concatenation of yesterday; get into today's fashionable attribute usage with this drop-in
replacement:

```php
use Livewire\Attributes\OnNative; // [tl! remove]
use Native\Mobile\Attributes\OnNative; // [tl! add]

#[On('native:'.ButtonPressed::class)] // [tl! remove]
#[OnNative(ButtonPressed::class)] // [tl! add]
public function handle()
```

###  Video Recording
Learn more about the new Video Recorder support [here](../apis/camera#coderecordvideocode).

###  QR/Barcode Scanner
Learn more about the new QR/Barcode Scanner support [here](../apis/scanner).

###  Microphone
Learn more about the new Microphone support [here](../apis/microphone).

###  Network Detection
Learn more about the new Network Detection support [here](../apis/network).

###  Background Audio Recording
Just update your config and record audio even while the device is locked!

```php
// config/nativephp.php
'permissions' => [
    'microphone' => true,
    'microphone_background' => true,
],
```
###  Push Notifications API
New fluent API for push notification enrollment:

<x-snippet title="Push Notifications">

<x-snippet.tab name="PHP">
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
</x-snippet.tab>
<x-snippet.tab name="Vue">
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
</x-snippet.tab>
</x-snippet>

**Deprecated Methods:**
- `enrollForPushNotifications()` → use `enroll()`
- `getPushNotificationsToken()` → use `getToken()`

###  Platform Improvements

#### iOS
- **Platform detection** - `nativephp-ios` class on body
- **Keyboard detection** - `keyboard-visible` class when keyboard shown
- **iOS 26 Liquid Glass** support
- **Improved device selector** on `native:run` showing last-used device
- **Load Times** dramatically improved. Now 60-80% faster!

#### Android
- **Complete Android 16+ 16KB page size** compatibility
- **Jetpack Compose UI** - Migrated from XML layouts
- **Platform detection** - `nativephp-android` class on body
- **Keyboard detection** - `keyboard-visible` class when keyboard shown
- **Parallel zip extraction** for faster installations
- **Load Times** dramatically improved. ~40% faster!
- **Page Load Times** dramatically decreased by ~40%!
---

###  Configuration

#### New Options
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

#### Custom Permission Reasons (iOS)
```php
'camera' => 'We need camera access to scan membership cards.',
'location' => 'Location is used to find nearby stores.',
```

###  New Events

- `Camera\VideoRecorded`, `Camera\VideoCancelled`, `Camera\PhotoCancelled`
- `Microphone\MicrophoneRecorded`, `Microphone\MicrophoneCancelled`
- `Scanner\CodeScanned`

### Custom Events

Many native calls now accept custom event classes!

```php
Dialog::alert('Confirm', 'Delete this?', ['Cancel', 'Delete'])
    ->event(MyCustomEvent::class)
```

###  Better File System Support
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

### Bug Fixes

- Fixed infinite recursion during bundling in some Laravel setups
- Fixed iOS toolbar padding for different device sizes
- Fixed Android debug mode forcing `APP_DEBUG=true`
- Fixed orientation config key case sensitivity (`iPhone` vs `iphone`)

### Breaking Changes

- None
