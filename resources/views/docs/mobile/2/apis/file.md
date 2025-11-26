---
title: File
order: 250
---

## Overview

The File API provides utilities for managing files on the device. You can move files between directories or copy files to new locations. These operations execute synchronously and return a boolean indicating success or failure.

```php
use Native\Mobile\Facades\File;
```

## Methods

### `move(string $from, string $to)`

Moves a file from one location to another. The source file is removed from its original location after being moved successfully.

**Parameters:**
- `string $from` - Absolute path to the source file
- `string $to` - Absolute path to the destination file

**Returns:** `bool` - `true` on success, `false` on failure

```php
// Move a captured photo to the app's storage directory
$success = File::move(
    '/var/mobile/Containers/Data/tmp/photo.jpg',
    '/var/mobile/Containers/Data/Documents/photos/photo.jpg'
);

if ($success) {
    // File moved successfully
} else {
    // Move operation failed
}
```

### `copy(string $from, string $to)`

Copies a file to a new location. The source file remains in its original location.

**Parameters:**
- `string $from` - Absolute path to the source file
- `string $to` - Absolute path to the destination file

**Returns:** `bool` - `true` on success, `false` on failure

```php
// Copy a file to create a backup
$success = File::copy(
    '/var/mobile/Containers/Data/Documents/document.pdf',
    '/var/mobile/Containers/Data/Documents/backups/document.pdf'
);

if ($success) {
    // File copied successfully
} else {
    // Copy operation failed
}
```

## Examples

### Moving Captured Media

After capturing media with the camera or audio API, move it from the temporary directory to permanent storage:

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Facades\File;

#[OnNative(PhotoTaken::class)]
public function handlePhotoTaken(string $path)
{
    $destination = storage_path('app/photos/'.basename($path));

    if (File::move($path, $destination)) {
        $this->photo_path = $destination;
    } else {
        $this->error = 'Failed to save photo';
    }
}
```

### Copying Files for Backup

Create backups of important files:

```php
$original = storage_path('app/documents/contract.pdf');
$backup = storage_path('app/backups/contract_backup.pdf');

if (File::copy($original, $backup)) {
    // Backup created successfully
} else {
    // Handle backup failure
}
```

### Organizing Files by Date

Move files into date-based directories:

```php
use Native\Mobile\Facades\File;

$today = now()->format('Y-m-d');
$source = storage_path('app/uploads/file.jpg');
$destination = storage_path("app/uploads/{$today}/file.jpg");

File::move($source, $destination);
```

## Notes

- File paths must be absolute paths. Use Laravel's `storage_path()` helper to construct paths
- Both the source file and destination directory must exist and be accessible
- If the source file does not exist, the operation fails and returns `false`
- If the destination file already exists, the operation fails and returns `false`
- These operations are synchronous and block execution until completion
- Ensure your app has the necessary file system permissions to read from the source and write to the destination
- No events are dispatched by these operations; they return results directly
