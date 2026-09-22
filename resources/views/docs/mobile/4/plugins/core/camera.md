---
title: Camera
order: 300
---

## Overview

The Camera plugin captures photos, records videos, and picks media from the device gallery.

All three operations are asynchronous. The native camera or picker opens, and the result arrives later as an event, so
register a listener before you start the operation.

## Installation

```shell
composer require nativephp/mobile-camera
php artisan native:plugin:register nativephp/mobile-camera
```

## Methods

### `getPhoto()`

Opens the native camera to capture a photo. Returns a `PendingPhotoCapture` builder.

**Parameters:**

- `array $options` - Capture options (default: `[]`)

**Returns:** `PendingPhotoCapture`

```php
use Native\Mobile\Facades\Camera;

Camera::getPhoto();

// Track this capture with an identifier
Camera::getPhoto()->id('profile-photo');
```

```js
import { Camera } from '#nativephp';

await Camera.getPhoto();

await Camera.getPhoto().id('profile-photo');
```

### `recordVideo()`

Opens the native camera to record a video. Returns a `PendingVideoRecorder` builder.

**Parameters:**

- `array $options` - Recording options (default: `[]`)
- `int maxDuration` - Maximum recording duration in seconds

**Returns:** `PendingVideoRecorder`

```php
Camera::recordVideo(['maxDuration' => 30]);

Camera::recordVideo()
    ->maxDuration(60)
    ->id('intro-video');
```

```js
await Camera.recordVideo().maxDuration(60).id('intro-video');
```

### `pickImages()`

Opens the gallery picker to select existing media. Returns a `PendingMediaPicker` builder.

**Parameters:**

- `string $media_type` - `'image'`, `'video'`, or `'all'` (default: `'all'`)
- `bool $multiple` - Allow multiple selection (default: `false`)
- `int $max_items` - Maximum items when `$multiple` is `true` (default: `10`)

**Returns:** `PendingMediaPicker`

```php
// Single image
Camera::pickImages('image');

// Up to five images
Camera::pickImages('image', true, 5);

// Any media type
Camera::pickImages('all', true);
```

```js
await Camera.pickImages().images();

await Camera.pickImages().images().multiple().maxItems(5);

await Camera.pickImages().videos();
```

## Builders

Every method returns a fluent builder. The operation starts when the builder is started or destroyed, so calling
`start()` is optional.

### `PendingPhotoCapture`

- `id(string $id)` - Correlate the result with this capture
- `event(string $eventClass)` - Dispatch a custom event instead of `PhotoTaken`
- `remember()` - Flash the capture ID to the session
- `lastId()` - Read the remembered capture ID
- `start()` - Start the capture

### `PendingVideoRecorder`

- `maxDuration(int $seconds)` - Maximum recording duration
- `id(string $id)` - Correlate the result with this recording
- `event(string $eventClass)` - Dispatch a custom event instead of `VideoRecorded`
- `remember()` / `lastId()` - Session helpers, as above
- `start()` - Start the recording

### `PendingMediaPicker`

- `images()` / `videos()` / `all()` - Restrict the media type
- `mediaType(string $type)` - Set the media type directly
- `multiple(bool $multiple = true, int $maxItems = 10)` - Allow multiple selection
- `single()` - Restrict to a single item
- `id(string $id)` - Correlate the result with this pick
- `event(string $eventClass)` - Dispatch a custom event instead of `MediaSelected`
- `remember()` / `lastId()` - Session helpers, as above
- `start()` - Start the picker

## Capture callbacks

Instead of listening for events, you can chain a callback onto the builder. The callback receives the event object.

```php
Camera::getPhoto()
    ->id('avatar')
    ->photoTaken(fn ($event) => $this->storeAvatar($event->path))
    ->photoCancelled(fn () => $this->dispatch('capture-cancelled'))
    ->permissionDenied(fn ($event) => $this->dispatch('camera-denied', action: $event->action));
```

Each builder exposes the callbacks for its own outcomes:

| Builder | Callbacks |
| --- | --- |
| `PendingPhotoCapture` | `photoTaken()`, `photoCancelled()`, `permissionDenied()` |
| `PendingVideoRecorder` | `videoRecorded()`, `videoCancelled()`, `permissionDenied()` |
| `PendingMediaPicker` | `mediaSelected()` |

For a custom event class, use the generic `on()` method:

```php
Camera::getPhoto()
    ->event(MyCaptureEvent::class)
    ->on(MyCaptureEvent::class, fn ($event) => $this->handle($event));
```

## Events

### `PhotoTaken`

Fired when a photo is captured.

**Payload:**

- `string $path` - File path to the captured photo
- `string $mimeType` - Photo MIME type (default: `'image/jpeg'`)
- `?string $id` - Capture ID when set via `id()`

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Camera\PhotoTaken;

#[OnNative(PhotoTaken::class)]
public function handlePhotoTaken(string $path, string $mimeType, ?string $id = null)
{
    $this->store($path);
}
```

```js
import { On, Off, Events } from '#nativephp';
import { onMounted, onUnmounted } from 'vue';

const handlePhotoTaken = (payload) => {
    console.log(payload.path);
};

onMounted(() => {
    On(Events.Camera.PhotoTaken, handlePhotoTaken);
});

onUnmounted(() => {
    Off(Events.Camera.PhotoTaken, handlePhotoTaken);
});
```

### `VideoRecorded`

Fired when a video is recorded.

**Payload:**

- `string $path` - File path to the recorded video
- `string $mimeType` - Video MIME type (default: `'video/mp4'`)
- `?string $id` - Recording ID when set via `id()`

### `MediaSelected`

Fired when media is picked from the gallery.

**Payload:**

- `bool $success` - Whether the picker completed successfully
- `array $files` - Selected files, each with `path`, `mimeType`, `extension`, and `type`
- `int $count` - Number of selected files
- `?string $error` - Error message when processing failed
- `bool $cancelled` - `true` when the user dismissed the picker
- `?string $id` - Pick ID when set via `id()`

```php
use Native\Mobile\Events\Gallery\MediaSelected;

#[OnNative(MediaSelected::class)]
public function handleMediaSelected(bool $success, array $files = [], int $count = 0)
{
    if (! $success) {
        return;
    }

    foreach ($files as $file) {
        $this->store($file['path']);
    }
}
```

```js
import { On, Events } from '#nativephp';

const handleMediaSelected = ({ success, files }) => {
    if (! success) {
        return;
    }

    files.forEach((file) => console.log(file.path));
};

On(Events.Gallery.MediaSelected, handleMediaSelected);
```

### `PhotoCancelled` and `VideoCancelled`

Fired when the user cancels the camera.

**Payload:**

- `bool $cancelled` - Always `true`
- `?string $id` - Capture or recording ID when set

### `PermissionDenied`

Fired when the camera permission is denied.

**Payload:**

- `string $action` - The attempted action: `'photo'` or `'video'`
- `?string $id` - Capture or recording ID when set

## Storage Locations

**Photos:**

- **Android:** App cache directory at `{cache}/captured.jpg`
- **iOS:** Application Support at `~/Library/Application Support/Photos/captured.jpg`

**Videos:**

- **Android:** App cache directory at `{cache}/video_{timestamp}.mp4`
- **iOS:** Application Support at `~/Library/Application Support/Videos/captured_video_{timestamp}.mp4`

## Notes

- **Permissions:** The plugin declares its own permissions in `nativephp.json` and they are merged into your app
  during the build. iOS usage-description strings can be customised in the `permissions` block of
  `config/nativephp.php`.
- Camera permission covers photos, videos, and QR/barcode scanning.
- When permission is denied, the operation does not run and `PermissionDenied` is dispatched.
- Photos are written as JPEG and videos as MP4.
- Video quality and camera selection are controlled by the native camera app.
- JavaScript usage requires the `#nativephp` import alias. See [Native Functions](/docs/mobile/4/the-basics/native-functions).
