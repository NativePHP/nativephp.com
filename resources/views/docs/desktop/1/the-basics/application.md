---
title: Application
order: 250
---

## Application

The `App` facade allows you to perform basic operations with the Electron app.

Note: Some methods are only available on specific operating systems and are labeled as such.

To use the `App` facade, add the following to the top of your file:

```php
use Native\Laravel\Facades\App;
```

### Quit the app

To quit the app, use the `quit` method:

```php
App::quit();
```

### Focus the app

To focus the app, use the `focus` method.

On Linux, it focuses on the first visible window. On macOS, it makes the application the active one. On Windows, it
focuses on the application's first window.

```php
App::focus();
```

### Hide the app
_Only available on macOS_

The `hide` method will hide all application windows without minimizing them. This method is only available on macOS.

```php
App::hide();
```

### Check if the app is hidden
_Only available on macOS_

To check if the app is hidden, use the `isHidden` method. This method is only available on macOS.

Returns a boolean: `true` if the application—including all its windows—is hidden (e.g., with Command-H), `false`
otherwise.

```php
$isHidden = App::isHidden();
```

### Current Version

To get the current app version, use the `version` method. The version is defined in the `config/nativephp.php` file.

```php
$version = App::version();
```

### App Badge Count
_Only available on macOS and Linux_

You can set the app's badge count.
On macOS, it shows on the dock icon. On Linux, it only works for Unity launcher.

To set the badge count, use the `badgeCount` method:

```php
App::badgeCount(5);
```

To remove the badge count, use the `badgeCount` method with `0` as the argument:

```php
App::badgeCount(0);
```

To get the badge count, use the `badgeCount` method without any arguments:

```php
$badgeCount = App::badgeCount();
``` 

### Recent documents list
_Only available on macOS and Windows_

The recent documents list is a list of files that the user has recently opened. This list is available on macOS and
Windows.

To add a document to the recent documents list, use the `addRecentDocument` method:

```php
App::addRecentDocument('/path/to/document');
```

To clear the recent documents list, use the `clearRecentDocuments` method:

```php
App::clearRecentDocuments();
```

### Open at login
_Only available on macOS and Windows_

To enable 'open at login', use the `openAtLogin` method:

```php
App::openAtLogin(true);
```

To disable open at login, use the `openAtLogin` method with `false` as the argument:

```php
App::openAtLogin(false);
```

To check if the app is set to open at login, use the `openAtLogin` method without any arguments:

```php
$isOpenAtLogin = App::openAtLogin();
```

