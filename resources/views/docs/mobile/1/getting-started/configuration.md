---
title: Configuration
order: 200
---

## Overview

NativePHP for Mobile is designed so that most configuration happens **inside your Laravel application**, without
requiring you to manually open Xcode or Android Studio.

This page explains the key configuration points you can control through Laravel.

## The `nativephp.php` Config File

The `config/nativephp.php` config file contains a number of useful options. 

NativePHP uses sensible defaults and makes several assumptions based on default installations for tools required to
build and run apps from your computer. 

You can override these defaults by editing the `nativephp.php` config file in your Laravel project, and in many case
simply by changing environment variables.

```dotenv
NATIVEPHP_APP_VERSION 
NATIVEPHP_APP_VERSION_CODE 
NATIVEPHP_APP_ID 
NATIVEPHP_DEEPLINK_SCHEME 
NATIVEPHP_DEEPLINK_HOST 
NATIVEPHP_APP_AUTHOR 
NATIVEPHP_GRADLE_PATH 
NATIVEPHP_ANDROID_SDK_LOCATION
```

## `NATIVEPHP_APP_ID`

You must set your app ID to something unique. A common practice is to use a reverse-DNS-style name, e.g.
`com.yourcompany.yourapp`.

Your app ID (also known as a *Bundle Identifier*) is a critical piece of identification across both Android and iOS
platforms. Different app IDs are treated as separate apps.

And it is often referenced across multiple services, such as Apple Developer Center and the Google Play Console.

So it's not something you want to be changing very often.

## `NATIVEPHP_APP_VERSION`

The `NATIVEPHP_APP_VERSION` environment variable controls your app's versioning behavior.

When your app is compiling, NativePHP first copies the relevant Laravel files into a temporary directory, zips them up,
and embeds the archive into the native application.

When your app boots, it checks the embedded version against the previously installed version to see if it needs to
extract the bundled Laravel application.

If the versions match, the app uses the existing files without re-extracting the archive.

To force your application to always install the latest version of your code - especially useful during development -
set this to `DEBUG`:

```dotenv
NATIVEPHP_APP_VERSION=DEBUG
```

Note that this will make your application's boot up slightly slower as it must unpack the zip every time it loads.

But this ensures that you can iterate quickly during development, while providing a faster, more stable experience for
end users once an app is published.

## Cleanup `env` keys

The `cleanup_env_keys` array in the config file allows you to specify keys that should be removed from the `.env` file before bundling. 
This is useful for removing sensitive information like API keys or other secrets.

## Cleanup `exclude_files`

The `cleanup_exclude_files` array in the config file allows you to specify files and folders that should be removed before bundling. 
This is useful for removing files like logs or other temporary files.

## Permissions
In general, the app stores don't want your app to have permissions (a.k.a entitlements) it doesn't need. 

By default, all optional permissions are disabled.

You may enable the features you intend to use simply by changing the value of the appropriate permission to `true`:

```php

    'permissions' => [

        'biometric' => true,

    ],
```

### Available permissions

- `biometric` - Allows your application to use fingerprint or face-recognition hardware (with a fallback to PIN code)
    to secure parts of your application.
- `camera` - Allows your application to request access to the device's camera, if present. Note that the user may deny 
    access and any camera functions will then result in a no-op.
- `nfc` - Allows your application to request access to the device's NFC reader, if present.
- `push_notifications` - Allows your application to request permissions to send push notifications. Note that the user
    may deny this and any push notification functions will then result in a no-op.
- `location` - Allows your application to request access to the device's GPS receiver, if present. Note that the user
    may deny this and any location functions will then result in a no-op.
- `vibrate` - In modern Android devices this is a requirement for most haptic feedback.
- `storage_read` - Grants your app access to read from device storage locations.
- `storage_write` - Allows your app to write to device storage.

## Orientation

NativePHP (as of v1.10.3) allows users to custom specific orientations per device through the config file. The config allows for granularity for iPad, iPhone and Android devices. Options for each device can be seen below.

NOTE: if you want to disable iPad support completely simply apply `false` for each option.

```php
'orientation' => [
    'iPhone' => [
        'portrait' => true,
        'upside_down' => false,
        'landscape_left' => false,
        'landscape_right' => false,
    ],
    'iPad' => [
        'portrait' => true,
        'upside_down' => false,
        'landscape_left' => false,
        'landscape_right' => false,
    ],
    'android' => [
        'portrait' => true,
        'upside_down' => false,
        'landscape_left' => false,
        'landscape_right' => false,
    ],
],
```

