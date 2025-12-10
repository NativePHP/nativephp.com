---
title: Camera
order: 300
---

## Overview

The Camera API provides access to the device's camera for taking photos, recording videos, and selecting media from the gallery.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Camera;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { camera, on, off, Events } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `getPhoto()`

Opens the camera interface to take a photo.

<x-snippet title="Take Photo">

<x-snippet.tab name="PHP">

```php
Camera::getPhoto();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Basic usage
await camera.getPhoto();

// With identifier for tracking
await camera.getPhoto()
    .id('profile-pic');
```

</x-snippet.tab>
</x-snippet>

### `recordVideo()`

Opens the camera interface to record a video with optional configuration.

**Parameters:**
- `array $options` - Optional recording options (default: `[]`)

**Returns:** `PendingVideoRecorder` - Fluent interface for configuring video recording

<x-snippet title="Record Video">

<x-snippet.tab name="PHP">

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

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Basic video recording
await camera.recordVideo();

// With maximum duration
await camera.recordVideo()
    .maxDuration(60);

// With identifier for tracking
await camera.recordVideo()
    .maxDuration(30)
    .id('my-video-123');
```

</x-snippet.tab>
</x-snippet>

### `pickImages()`

Opens the gallery/photo picker to select existing images.

**Parameters:**
- `string $media_type` - Type of media to pick: `'all'`, `'images'`, `'videos'` (default: `'all'`)
- `bool $multiple` - Allow multiple selection (default: `false`)

**Returns:** `bool` - `true` if picker opened successfully

<x-snippet title="Pick Images">

<x-snippet.tab name="PHP">

```php
// Pick a single image
Camera::pickImages('images', false);

// Pick multiple images
Camera::pickImages('images', true);

// Pick any media type
Camera::pickImages('all', true);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Pick images using fluent API
await camera.pickImages()
    .images()
    .multiple()
    .maxItems(5);

// Pick only videos
await camera.pickImages()
    .videos()
    .multiple();

// Pick any media type
await camera.pickImages()
    .all()
    .multiple()
    .maxItems(10);

// Single image selection
await camera.pickImages()
    .images();
```

</x-snippet.tab>
</x-snippet>

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

<x-snippet title="PhotoTaken Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Camera\PhotoTaken;

#[OnNative(PhotoTaken::class)]
public function handlePhotoTaken(string $path)
{
    // Process the captured photo
    $this->processPhoto($path);
}
```

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const photoPath = ref('');

const handlePhotoTaken = (payload) => {
    photoPath.value = payload.path;
    processPhoto(payload.path);
};

onMounted(() => {
    on(Events.Camera.PhotoTaken, handlePhotoTaken);
});

onUnmounted(() => {
    off(Events.Camera.PhotoTaken, handlePhotoTaken);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [photoPath, setPhotoPath] = useState('');

const handlePhotoTaken = (payload) => {
    setPhotoPath(payload.path);
    processPhoto(payload.path);
};

useEffect(() => {
    on(Events.Camera.PhotoTaken, handlePhotoTaken);

    return () => {
        off(Events.Camera.PhotoTaken, handlePhotoTaken);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

### `VideoRecorded`

Fired when a video is successfully recorded.

**Payload:**
- `string $path` - File path to the recorded video
- `string $mimeType` - Video MIME type (default: `'video/mp4'`)
- `?string $id` - Optional identifier if set via `id()` method

<x-snippet title="VideoRecorded Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const videoPath = ref('');

const handleVideoRecorded = (payload) => {
    const { path, mimeType, id } = payload;
    videoPath.value = path;
    processVideo(path);

    if (id === 'my-upload-video') {
        uploadVideo(path);
    }
};

onMounted(() => {
    on(Events.Camera.VideoRecorded, handleVideoRecorded);
});

onUnmounted(() => {
    off(Events.Camera.VideoRecorded, handleVideoRecorded);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [videoPath, setVideoPath] = useState('');

const handleVideoRecorded = (payload) => {
    const { path, mimeType, id } = payload;
    setVideoPath(path);
    processVideo(path);

    if (id === 'my-upload-video') {
        uploadVideo(path);
    }
};

useEffect(() => {
    on(Events.Camera.VideoRecorded, handleVideoRecorded);

    return () => {
        off(Events.Camera.VideoRecorded, handleVideoRecorded);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

### `VideoCancelled`

Fired when video recording is cancelled by the user.

**Payload:**
- `bool $cancelled` - Always `true`
- `?string $id` - Optional identifier if set via `id()` method

<x-snippet title="VideoCancelled Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Camera\VideoCancelled;

#[OnNative(VideoCancelled::class)]
public function handleVideoCancelled(bool $cancelled, ?string $id = null)
{
    // Handle cancellation
    $this->notifyUser('Video recording was cancelled');
}
```

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { onMounted, onUnmounted } from 'vue';

const handleVideoCancelled = (payload) => {
    notifyUser('Video recording was cancelled');
};

onMounted(() => {
    on(Events.Camera.VideoCancelled, handleVideoCancelled);
});

onUnmounted(() => {
    off(Events.Camera.VideoCancelled, handleVideoCancelled);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useEffect } from 'react';

const handleVideoCancelled = (payload) => {
    notifyUser('Video recording was cancelled');
};

useEffect(() => {
    on(Events.Camera.VideoCancelled, handleVideoCancelled);

    return () => {
        off(Events.Camera.VideoCancelled, handleVideoCancelled);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

### `MediaSelected`

Fired when media is selected from the gallery.

**Payload:** `array $media` - Array of selected media items

<x-snippet title="MediaSelected Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const selectedFiles = ref([]);

const handleMediaSelected = (payload) => {
    const { success, files, count } = payload;

    if (success) {
        selectedFiles.value = files;
        files.forEach(file => processMedia(file));
    }
};

onMounted(() => {
    on(Events.Gallery.MediaSelected, handleMediaSelected);
});

onUnmounted(() => {
    off(Events.Gallery.MediaSelected, handleMediaSelected);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [selectedFiles, setSelectedFiles] = useState([]);

const handleMediaSelected = (payload) => {
    const { success, files, count } = payload;

    if (success) {
        setSelectedFiles(files);
        files.forEach(file => processMedia(file));
    }
};

useEffect(() => {
    on(Events.Gallery.MediaSelected, handleMediaSelected);

    return () => {
        off(Events.Gallery.MediaSelected, handleMediaSelected);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

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
