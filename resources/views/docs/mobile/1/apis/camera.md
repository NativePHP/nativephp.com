---
title: Camera
order: 300
---

## Overview

The Camera API provides access to the device's camera for taking photos and selecting images from the gallery.

```php
use Native\Mobile\Facades\Camera;
```

## Methods

### `getPhoto()`

Opens the camera interface to take a photo.

```php
Camera::getPhoto();
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

## Events

### `PhotoTaken`

Fired when a photo is taken with the camera.

**Payload:** `string $path` - File path to the captured photo

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Camera\PhotoTaken;

#[On('native:'.PhotoTaken::class)]
public function handlePhotoTaken(string $path)
{
    // Process the captured photo
    $this->processPhoto($path);
}
```

### `MediaSelected`

Fired when media is selected from the gallery.

**Payload:** `array $media` - Array of selected media items

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Gallery\MediaSelected;

#[On('native:'.MediaSelected::class)]
public function handleMediaSelected($success, $files, $count)
{
    foreach ($files as $file) {
        // Process each selected media item
        $this->processMedia($file);
    }
}
```

## Notes

- The first time your app requests camera access, users will be prompted for permission
- If permission is denied, camera functions will fail silently
- Captured photos are stored in the app's temporary directory
- File formats are platform-dependent (typically JPEG)
