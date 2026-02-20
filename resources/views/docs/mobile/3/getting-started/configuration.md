---
title: Configuration
order: 200
---

## Overview

NativePHP for Mobile is designed so that most configuration happens **inside your Laravel application**, without
requiring you to open Xcode or Android Studio and manually update config files.

This page explains the key configuration points you can control through Laravel.

## The `nativephp.php` Config File

The `config/nativephp.php` config file contains a number of useful options. 

NativePHP uses sensible defaults and makes several assumptions based on default installations for tools required to
build and run apps from your computer. 

You can override these defaults by editing the `nativephp.php` config file in your Laravel project, and in many cases
simply by changing environment variables.

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

The `cleanup_env_keys` array in the config file allows you to specify keys that should be removed from the `.env` file
before bundling. This is useful for removing sensitive information like API keys or other secrets.

## Cleanup `exclude_files`

The `cleanup_exclude_files` array in the config file allows you to specify files and folders that should be removed
before bundling. This is useful for removing files like logs or other temporary files that aren't required for your app
to function and bloat your downloads.

## Orientation

NativePHP (as of v1.10.3) allows users to custom specific orientations per device through the config file. The config
allows for granularity for iPad, iPhone and Android devices. Options for each device can be seen below.

NOTE: if you want to disable iPad support completely simply apply `false` for each option.

```php
'orientation' => [
    'iphone' => [
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

Regardless of these orientation settings, if your app supports iPad, it will be available in all orientations.

## iPad Support

With NativePHP, your app can work on iPad too! If you wish to support iPad, simply set the `ipad` config option to `true`:

```php
'ipad' => true,
```

Using standard CSS responsive design principles, you can make your app work beautifully across all screen sizes. 

<aside>

#### Once iPad, Always iPad

Once you've published an app with iPad support, it cannot be undone. If you wish to remove iPad support, you
will need to change your `NATIVEPHP_APP_ID` and publish the app under a new App Store listing.

</aside>

## Android SDK Versions

The `android` section of your config file lets you control which Android SDK versions are used when building your app.
These are nested under the `android` key in `config/nativephp.php`:

```php
'android' => [
    'compile_sdk' => env('NATIVEPHP_ANDROID_COMPILE_SDK', 36),
    'min_sdk' => env('NATIVEPHP_ANDROID_MIN_SDK', 33),
    'target_sdk' => env('NATIVEPHP_ANDROID_TARGET_SDK', 36),
],
```

- `compile_sdk` - The SDK version used to compile your app. This determines which Android APIs are available to you
    at build time. (default: `36`)
- `min_sdk` - The minimum Android version your app supports. Devices running an older version won't be able to install
    your app. (default: `33`, Android 13)
- `target_sdk` - The SDK version your app is designed and tested against. Google Play uses this to apply appropriate
    compatibility behaviors. (default: `36`)

You can also set these via environment variables:

```dotenv
NATIVEPHP_ANDROID_COMPILE_SDK=36
NATIVEPHP_ANDROID_MIN_SDK=33
NATIVEPHP_ANDROID_TARGET_SDK=36
```

<aside>

Most apps won't need to change these defaults. Only adjust them if you have a specific reason, such as supporting
older devices or targeting a newer API level. Always ensure that `compile_sdk` >= `target_sdk` >= `min_sdk`.

The lowest supported `min_sdk` is `29` (Android 10). Setting it lower than this is not supported.

</aside>
