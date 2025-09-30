---
title: Clipboard
order: 700
---

## Working with the Clipboard

NativePHP allows you to easily read from and write to the system clipboard using just PHP, thanks to the `Clipboard`
facade.

```php
use Native\Desktop\Facades\Clipboard;
```

### Reading from the Clipboard

You can read `text`, `html` or `image` data from the clipboard using the appropriate method:

```php
Clipboard::text();
Clipboard::html();
Clipboard::image();
```

### Writing to the Clipboard

You can write `text`, `html` or `image` data to the clipboard using the appropriate method:

```php
Clipboard::text('Some copied text');
Clipboard::html('<div>Some copied HTML</div>');
Clipboard::image('path/to/image.png');
```

Note that the `image()` method expects a path to an image, not the image data itself. NativePHP will take care of
serializing the image data for you.

### Clearing the Clipboard

You may also programmatically clear the clipboard using the `clear()` method.

```php
Clipboard::clear();
```

This is useful if you need the contents of the clipboard to expire after a certain amount of time.
