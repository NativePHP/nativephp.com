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

## Persistent Runtime

v3.1 introduces a persistent PHP runtime that boots Laravel once and reuses the kernel across all subsequent requests.
This dramatically improves performance — from ~200-300ms per request down to ~5-30ms.

The `runtime` section controls this behavior:

```php
'runtime' => [
    'mode' => env('NATIVEPHP_RUNTIME_MODE', 'persistent'), // [tl! highlight]
    'reset_instances' => true,
    'gc_between_dispatches' => false,
],
```

- `mode` — Set to `persistent` (default) to reuse the Laravel kernel, or `classic` to boot/shutdown per request.
  If persistent boot fails, it falls back to classic mode automatically.
- `reset_instances` — Whether to clear resolved facade instances between dispatches. (default: `true`)
- `gc_between_dispatches` — Whether to run garbage collection between dispatches. Enable this if you notice memory
  growth over time. (default: `false`)

<aside>

The persistent runtime handles Livewire state, router state, and facade instances automatically. You only need
`onReset()` if you have custom static state that accumulates between requests.

</aside>

## Deep Links

Configure deep linking to allow URLs to open your app directly:

```php
'deeplink_scheme' => env('NATIVEPHP_DEEPLINK_SCHEME'),
'deeplink_host' => env('NATIVEPHP_DEEPLINK_HOST'),
```

The `deeplink_scheme` enables custom URL schemes (e.g. `myapp://some/path`), while `deeplink_host` enables
verified HTTPS links and NFC tags (e.g. `https://your-host.com/path`).

See the [Deep Links](../concepts/deep-links) documentation for full details.

## Start URL

Set the initial path that loads when your app starts:

```php
'start_url' => env('NATIVEPHP_START_URL', '/'),
```

This is useful if you want to land users on a specific page like `/dashboard` or `/onboarding` instead of the root.

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
    your app. (default: `26`, Android 8)
- `target_sdk` - The SDK version your app is designed and tested against. Google Play uses this to apply appropriate
    compatibility behaviors. (default: `36`)

You can also set these via environment variables:

```dotenv
NATIVEPHP_ANDROID_COMPILE_SDK=36
NATIVEPHP_ANDROID_MIN_SDK=26
NATIVEPHP_ANDROID_TARGET_SDK=36
```

<aside>

Most apps won't need to change these defaults. Only adjust them if you have a specific reason, such as supporting
older devices or targeting a newer API level. Always ensure that `compile_sdk` >= `target_sdk` >= `min_sdk`.

The lowest supported `min_sdk` is `26` (Android 8). Setting it lower than this is not supported.

</aside>

## Android Build Configuration

Fine-tune your Android build process with these options under the `android.build` key:

```php
'android' => [
    'build' => [
        'minify_enabled' => env('NATIVEPHP_ANDROID_MINIFY_ENABLED', false),
        'shrink_resources' => env('NATIVEPHP_ANDROID_SHRINK_RESOURCES', false),
        'obfuscate' => env('NATIVEPHP_ANDROID_OBFUSCATE', false),
        'debug_symbols' => env('NATIVEPHP_ANDROID_DEBUG_SYMBOLS', 'FULL'),
        'parallel_builds' => env('NATIVEPHP_ANDROID_PARALLEL_BUILDS', true),
        'incremental_builds' => env('NATIVEPHP_ANDROID_INCREMENTAL_BUILDS', true),
    ],
],
```

- `minify_enabled` — Enable R8/ProGuard code shrinking. (default: `false`)
- `shrink_resources` — Remove unused resources from the APK. (default: `false`)
- `obfuscate` — Obfuscate class and method names. (default: `false`)
- `debug_symbols` — Include debug symbols. Set to `FULL` for symbolicated crash reports. (default: `FULL`)
- `parallel_builds` / `incremental_builds` — Gradle build performance options. (default: `true`)

<aside>

For production builds uploaded to the Play Store, consider enabling `minify_enabled` and `shrink_resources` to
reduce your APK size. Test thoroughly after enabling these options.

</aside>

## Android Status Bar Style

Control the color of the status bar and navigation bar icons:

```php
'android' => [
    'status_bar_style' => env('NATIVEPHP_ANDROID_STATUS_BAR_STYLE', 'auto'),
],
```

Options: `auto` (detect from system theme), `light` (white icons), or `dark` (dark icons).

## Development Server

Configure the development server used by `native:jump` and `native:watch`:

```php
'server' => [
    'http_port' => env('NATIVEPHP_HTTP_PORT', 3000),
    'ws_port' => env('NATIVEPHP_WS_PORT', 8081),
    'service_name' => env('NATIVEPHP_SERVICE_NAME', 'NativePHP Server'),
    'open_browser' => env('NATIVEPHP_OPEN_BROWSER', true),
],
```

- `http_port` — The port for serving your app during development. (default: `3000`)
- `ws_port` — The WebSocket port for hot reload communication. (default: `8081`)
- `service_name` — The mDNS service name advertised on your network. (default: `NativePHP Server`)
- `open_browser` — Automatically open a browser with a QR code when the server starts. (default: `true`)

## Hot Reload

Customize which files trigger hot reloads during development:

```php
'hot_reload' => [
    'watch_paths' => [
        'app',
        'resources',
        'routes',
        'config',
        'public',
    ],
    'exclude_patterns' => [
        '\.git',
        'storage',
        'node_modules',
    ],
],
```

## Development Team (iOS)

Set your Apple Developer Team ID for code signing:

```php
'development_team' => env('NATIVEPHP_DEVELOPMENT_TEAM'),
```

This is typically detected from your installed certificates, but you can override it here. Find your Team ID
in your Apple Developer account under Membership details.

## App Store Connect

Configure automated iOS uploads with the App Store Connect API:

```php
'app_store_connect' => [
    'api_key' => env('APP_STORE_API_KEY'),
    'api_key_id' => env('APP_STORE_API_KEY_ID'),
    'api_issuer_id' => env('APP_STORE_API_ISSUER_ID'),
    'app_name' => env('APP_STORE_APP_NAME'),
],
```

These credentials are used by `native:package --upload-to-app-store` to upload your IPA directly to
App Store Connect without opening Xcode.

<aside>

Store these values in your `.env` file — never commit API keys to version control. Add them to your
`cleanup_env_keys` array to ensure they're stripped from production builds.

</aside>
