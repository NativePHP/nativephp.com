---
title: Shell
order: 850
---
# Shell operations

The `Shell` facade lets you perform some basic operations with files on the user's system in the context of the system's
default behaviour.

## Showing a file

The `showInFolder` method will attempt to open the given `$path` in the user's default file manager, e.g. File Explorer,
Finder etc.

```php
use Native\Laravel\Facades\Shell;

Shell::showInFolder($path);
```

## Opening a file

The `openFile` method will attempt to open the given `$path` using the default application associated with that file
type. If it was successful, this method will return an empty string (`""`); if unsuccessful, the returned string may
contain an error message.

```php
use Native\Laravel\Facades\Shell;

$result = Shell::openFile($path);
```

## Trashing a file

The `trashFile` method will attempt to send the given `$path` to the system's trash.

```php
use Native\Laravel\Facades\Shell;

Shell::trashFile($path);
```

## Open a URL

The `openExternal` method will attempt to open the given `$url` using the default handler registered for that URL's
scheme on the system's, e.g. the `http` and `https` schemes will most likely open the user's default web browser.

```php
use Native\Laravel\Facades\Shell;

Shell::openExternal($url);
```
