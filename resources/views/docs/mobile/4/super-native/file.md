---
title: File
order: 120
---

## Overview

The `File` API performs native file operations — moving and copying files — using each platform's file system
directly.

It's a **core built-in** in v4: the facade resolves with nothing to install or register.

```php
use Native\Mobile\Facades\File;
```

## Move

```php
$ok = File::move('/path/to/source.txt', '/path/to/destination.txt'); // bool
```

## Copy

```php
$ok = File::copy('/path/to/source.txt', '/path/to/copy.txt'); // bool
```

Both methods return a `bool` — `true` on success, `false` if the operation failed.

| Parameter | Type | Description |
| --- | --- | --- |
| `from` | string | Source file path |
| `to` | string | Destination file path |

## Behavior

- Parent directories are created automatically if they don't exist.
- An existing destination file is overwritten.
- File integrity is verified after a copy.
- On Android, if a rename fails across file systems, it falls back to copy + delete.

## Example

Move a recording out of temporary storage into a permanent location:

```php
use Native\Mobile\Facades\File;

$temp = sys_get_temp_dir().'/recording.m4a';
$permanent = storage_path('recordings/recording.m4a');

if (File::move($temp, $permanent)) {
    // saved
}
```

<aside>

These functions are also callable from JavaScript in a web view via the `Native` library — see
[Native Functions](../the-basics/native-functions).

</aside>
