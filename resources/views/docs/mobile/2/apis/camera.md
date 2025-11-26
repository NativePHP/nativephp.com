---
title: Camera
order: 200
---

## Overview

The Camera API provides access to the device's camera for taking photos, recording videos, and selecting media from the gallery.

```php
use Native\Mobile\Facades\Camera;
```

## Methods

### `getPhoto()`

Opens the camera interface to take a photo.

```php
Camera::getPhoto();
```

### `recordVideo()`

Opens the camera interface to record a video with optional configuration.

**Parameters:**
- `array $options` - Optional recording options (default: `[]`)

**Returns:** `PendingVideoRecorder` - Fluent interface for configuring video recording

```php
// Basic video recording
Camera::recordVideo();

// With maximum duration (30 seconds)
Camera::recordVideo(['maxDuration' => 30]);

// Using fluent API
Camera::recordVideo()
    ->maxDuration(60)
    ->id('my-video-123')
    ->start();
```

### `pickImages()`

Opens the gallery/photo picker to select existing images.

**Parameters:**
- `string $media_type` - Type of media to pick: `'all'`, `'images'`, `'videos'` (default: `'all'`)
- `bool $multiple` - Allow multiple selection (default: `false`)

**Returns:** `bool` - `true` if picker opened successfully

```php
// Pick a single image
Camera::pickImages('images', false);

// Pick multiple images
Camera::pickImages('images', true);

// Pick any media type
Camera::pickImages('all', true);
```

## PendingVideoRecorder

The fluent API returned by `recordVideo()` provides several methods for configuring video recording:

### `maxDuration(int $seconds)`

Set the maximum recording duration in seconds.

```php
Camera::recordVideo()
    ->maxDuration(30)
    ->start();
```

### `id(string $id)`

Set a unique identifier for this recording to correlate with events.

```php
Camera::recordVideo()
    ->id('user-upload-video')
    ->start();
```

### `getId()`

Get the recorder's unique identifier (auto-generates UUID if not set).

```php
$recorder = Camera::recordVideo();
$id = $recorder->getId(); // Returns UUID
```

### `event(string $eventClass)`

Set a custom event class to dispatch when recording completes.

```php
Camera::recordVideo()
    ->event(MyCustomVideoEvent::class)
    ->start();
```

### `remember()`

Store the recorder's ID in the session for later retrieval.

```php
Camera::recordVideo()
    ->remember()
    ->start();

// Later, in your event handler
$recorderId = PendingVideoRecorder::lastId();
```

### `start()`

Explicitly start the video recording. If not called, recording starts automatically.

```php
Camera::recordVideo()
    ->maxDuration(60)
    ->start();
```

## Events

### `PhotoTaken`

Fired when a photo is taken with the camera.

**Payload:** `string $path` - File path to the captured photo

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Camera\PhotoTaken;

#[OnNative(PhotoTaken::class)]
public function handlePhotoTaken(string $path)
{
    // Process the captured photo
    $this->processPhoto($path);
}
```

### `VideoRecorded`

Fired when a video is successfully recorded.

**Payload:**
- `string $path` - File path to the recorded video
- `string $mimeType` - Video MIME type (default: `'video/mp4'`)
- `?string $id` - Optional identifier if set via `id()` method

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Camera\VideoRecorded;

#[OnNative(VideoRecorded::class)]
public function handleVideoRecorded(string $path, string $mimeType, ?string $id = null)
{
    // Process the recorded video
    $this->processVideo($path);

    // Check if this is the video we're expecting
    if ($id === 'my-upload-video') {
        $this->uploadVideo($path);
    }
}
```

### `VideoCancelled`

Fired when video recording is cancelled by the user.

**Payload:**
- `bool $cancelled` - Always `true`
- `?string $id` - Optional identifier if set via `id()` method

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Camera\VideoCancelled;

#[OnNative(VideoCancelled::class)]
public function handleVideoCancelled(bool $cancelled, ?string $id = null)
{
    // Handle cancellation
    $this->notifyUser('Video recording was cancelled');
}
```

### `MediaSelected`

Fired when media is selected from the gallery.

**Payload:** `array $media` - Array of selected media items

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Gallery\MediaSelected;

#[OnNative(MediaSelected::class)]
public function handleMediaSelected($success, $files, $count)
{
    foreach ($files as $file) {
        // Process each selected media item
        $this->processMedia($file);
    }
}
```

## Storage Locations

Media files are stored in different locations depending on the platform:

**Photos:**
- **Android:** App cache directory at `{cache}/captured.jpg`
- **iOS:** Application Support at `~/Library/Application Support/Photos/captured.jpg`

**Videos:**
- **Android:** App cache directory at `{cache}/video_{timestamp}.mp4`
- **iOS:** Application Support at `~/Library/Application Support/Videos/captured_video_{timestamp}.mp4`

**Important Notes:**
- Android stores media in the cache directory (temporary, can be cleared by system)
- iOS stores media in Application Support (persistent, excluded from backups)
- iOS photo captures use a fixed filename `captured.jpg` (overwrites previous)
- iOS/Android videos use timestamped filenames (don't overwrite)

## Notes

- **Permissions:** You must enable the `camera` permission in `config/nativephp.php` to use camera features. Once enabled, camera permissions are handled automatically on both platforms, and users will be prompted for permission the first time your app requests camera access.
- If permission is denied, camera functions will fail silently
- Camera permission is required for photos, videos, AND QR/barcode scanning
- File formats: JPEG for photos, MP4 for videos (platform-dependent)
- Video quality and camera selection are controlled by the native camera app
