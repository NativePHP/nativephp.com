---
title: Share
order: 1400
---

## Overview

The Share API enables users to share content from your app using the native share sheet. On iOS, this opens the native share menu with options like Messages, Mail, and social media apps. On Android, it launches the system share intent with available apps.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Share;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { share } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `url()`

Share a URL using the native share dialog.

**Parameters:**
- `string $title` - Title/subject for the share
- `string $text` - Text content or message to share
- `string $url` - URL to share

**Returns:** `void`

<x-snippet title="Share URL">

<x-snippet.tab name="PHP">

```php
Share::url(
    title: 'Check this out',
    text: 'I found something interesting',
    url: 'https://example.com/article'
);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await share.url(
    'Check this out',
    'I found something interesting',
    'https://example.com/article'
);
```

</x-snippet.tab>
</x-snippet>

### `file()`

Share a file using the native share dialog.

**Parameters:**
- `string $title` - Title/subject for the share
- `string $text` - Text content or message to share
- `string $filePath` - Absolute path to the file to share

**Returns:** `void`

<x-snippet title="Share File">

<x-snippet.tab name="PHP">

```php
Share::file(
    title: 'Share Document',
    text: 'Check out this PDF',
    filePath: '/path/to/document.pdf'
);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await share.file(
    'Share Document',
    'Check out this PDF',
    '/path/to/document.pdf'
);
```

</x-snippet.tab>
</x-snippet>

## Examples

### Sharing a Website Link

Share a link to your app's website or external content.

<x-snippet title="Share Website Link">

<x-snippet.tab name="PHP">

```php
Share::url(
    title: 'My Awesome App',
    text: 'Download my app today!',
    url: 'https://myapp.com'
);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await share.url(
    'My Awesome App',
    'Download my app today!',
    'https://myapp.com'
);
```

</x-snippet.tab>
</x-snippet>

### Sharing Captured Photos

Share a photo that was captured with the camera.

<x-snippet title="Share Captured Photo">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { share, on, off, Events } from '#nativephp';
import { onMounted, onUnmounted } from 'vue';

const handlePhotoTaken = (payload) => {
    share.file(
        'My Photo',
        'Check out this photo I just took!',
        payload.path
    );
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
import { share, on, off, Events } from '#nativephp';
import { useEffect } from 'react';

const handlePhotoTaken = (payload) => {
    share.file(
        'My Photo',
        'Check out this photo I just took!',
        payload.path
    );
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

## Notes

- The native share sheet opens, allowing users to choose which app to share with (Messages, Email, social media, etc.)
- The file path must be absolute and the file must exist before calling the share method
- File paths should be verified to exist before attempting to share to avoid errors
- The Share API works with any file type (PDF, images, videos, documents, etc.)
- There is no way to determine which app the user selected or whether they cancelled the share
- No events are dispatched by the Share API
- The `url()` method works with any URL format (http, https, deep links, etc.)
