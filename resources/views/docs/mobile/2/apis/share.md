---
title: Share
order: 1400
---

## Overview

The Share API enables users to share content from your app using the native share sheet. On iOS, this opens the native share menu with options like Messages, Mail, and social media apps. On Android, it launches the system share intent with available apps.

```php
use Native\Mobile\Facades\Share;
```

## Methods

### `url()`

Share a URL using the native share dialog.

**Parameters:**
- `string $title` - Title/subject for the share
- `string $text` - Text content or message to share
- `string $url` - URL to share

**Returns:** `void`

```php
Share::url(
    title: 'Check this out',
    text: 'I found something interesting',
    url: 'https://example.com/article'
);
```

### `file()`

Share a file using the native share dialog.

**Parameters:**
- `string $title` - Title/subject for the share
- `string $text` - Text content or message to share
- `string $filePath` - Absolute path to the file to share

**Returns:** `void`

```php
Share::file(
    title: 'Share Document',
    text: 'Check out this PDF',
    filePath: '/path/to/document.pdf'
);
```

## Examples

### Sharing a Website Link

Share a link to your app's website or external content.

```php
Share::url(
    title: 'My Awesome App',
    text: 'Download my app today!',
    url: 'https://myapp.com'
);
```

### Sharing Captured Photos

Share a photo that was captured with the camera.

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Camera\PhotoTaken;

#[OnNative(PhotoTaken::class)]
public function handlePhotoTaken(string $path)
{
    Share::file(
        title: 'My Photo',
        text: 'Check out this photo I just took!',
        filePath: $path
    );
}
```

## Notes

- The native share sheet opens, allowing users to choose which app to share with (Messages, Email, social media, etc.)
- The file path must be absolute and the file must exist before calling the share method
- File paths should be verified to exist before attempting to share to avoid errors
- The Share API works with any file type (PDF, images, videos, documents, etc.)
- There is no way to determine which app the user selected or whether they cancelled the share
- No events are dispatched by the Share API
- The `url()` method works with any URL format (http, https, deep links, etc.)
