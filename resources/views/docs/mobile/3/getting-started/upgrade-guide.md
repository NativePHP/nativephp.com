---
title: Upgrade Guide
order: 3
---

## Upgrading To 3.1 From 3.0

v3.1 is a drop-in upgrade with no breaking changes. The headline feature is a **persistent PHP runtime** that
dramatically improves request performance.


### Update your dependency

```json
"require": {
    "nativephp/mobile": "~3.0.0" // [tl! remove]
    "nativephp/mobile": "~3.1.0" // [tl! add]
}
```

```sh
composer update
php artisan native:install --force
```

<aside>

The `--force` flag is important — it ensures the native project files, PHP binaries, and config are all updated
to the v3.1 versions.

</aside>


## Android 8+ Support

In order to support Android API 26 add the following to your `config/nativephp.php`:

```php
'android' => [
    'compile_sdk' => env('NATIVEPHP_ANDROID_COMPILE_SDK', 36),
    'min_sdk' => env('NATIVEPHP_ANDROID_MIN_SDK', 33),
    'target_sdk' => env('NATIVEPHP_ANDROID_TARGET_SDK', 36),
],
```

No code changes are required — this is handled entirely at the build level.

## ICU/Intl Support on iOS

iOS builds now include full **ICU support**, which means the PHP `intl` extension works on both platforms.
This was previously only available on Android.

This is a big deal — packages like [Filament](https://filamentphp.com) depend on `intl` for number formatting,
date formatting, and pluralization. With v3.1, **Filament works on both iOS and Android** out of the box.

ICU support remains optional via the `--with-icu` / `--without-icu` flags during installation it adds ~30MB to your app on Android and ~100MB on iOS.
---

## Upgrading To 3.0 From 2.x

NativePHP for Mobile v3 introduces a plugin-based architecture that makes the entire native layer extensible.
All core functionality continues to work as before, but the underlying system is now modular and open to
third-party plugins.

## Remove the NativePHP Composer Repository

v3 no longer requires the private Composer repository or license authentication. Remove the `nativephp.composer.sh`
repository from your `composer.json`:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://nativephp.composer.sh"
    }
]
```

Delete the entire `repositories` block above (or just the NativePHP entry if you have other repositories).

You can also remove any stored credentials for `nativephp.composer.sh` from your `auth.json` if you have one.

Then update your version constraint and run the upgrade:

```json
"require": {
    "nativephp/mobile": "~2.0.0" // [tl! remove]
    "nativephp/mobile": "~3.0.0" // [tl! add]
}
```

```sh
composer update
php artisan native:install --force
```

## Plugin Architecture

v3 introduces a comprehensive plugin system. Native functionality is now delivered through plugins — including all
the official core APIs you already use (Camera, Biometrics, Scanner, etc.). These continue to work exactly as before;
you don't need to change how you call them.

The difference is that **third-party developers can now create plugins** that add new native functionality to your
app. Plugins are standard Composer packages that include Swift (iOS) and Kotlin (Android) code alongside their
PHP interface.

Read more about the plugin system in the [Plugins documentation](../plugins/introduction), or browse
ready-made plugins on the [NativePHP Plugin Marketplace](https://nativephp.com/plugins).

## NativeServiceProvider

v3 introduces a `NativeServiceProvider` for registering third-party plugins. Publish it with:

```shell
php artisan vendor:publish --tag=nativephp-plugins-provider
```

This creates `app/Providers/NativeServiceProvider.php`. Any third-party plugins you install must be registered
here before their native code is compiled into your app. This is a security measure to prevent transitive
dependencies from automatically including native code without your consent.

```shell
php artisan native:plugin:register vendor/some-plugin
```

Core APIs provided by `nativephp/mobile` do not need to be registered manually — they are included automatically.

Read more about [Using Plugins](../plugins/using-plugins).

## Core APIs Are Now Plugins

All core APIs (Camera, Biometrics, Dialog, Scanner, Geolocation, etc.) are now implemented as plugins internally.
The PHP facades and events you use remain the same — no changes to your application code are needed.

Browse the full list of available core plugins in the [Plugins documentation](../plugins/introduction).

## Plugin Management Commands

v3 adds several new Artisan commands for working with plugins:

| Command | Description |
|---------|-------------|
| `native:plugin:create` | Scaffold a new plugin |
| `native:plugin:register` | Register a plugin in your NativeServiceProvider |
| `native:plugin:list` | List installed plugins |
| `native:plugin:uninstall` | Remove a plugin |
| `native:plugin:validate` | Validate plugin structure |
| `native:plugin:make-hook` | Create a lifecycle hook |

## Bridge Functions

Plugins communicate with native code through **bridge functions** — a standardized pattern for calling
Swift and Kotlin code from PHP via `nativephp_call()`. Each plugin declares its bridge functions in a
`nativephp.json` manifest.

If you've been using the core APIs through their facades, nothing changes for you. Bridge functions are primarily
relevant if you're building your own plugins.

Read more in the [Bridge Functions documentation](../plugins/bridge-functions).

## Plugin Marketplace

Find ready-made plugins for common use cases, or get the Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace](https://nativephp.com/plugins).

## Command Reference

v3 includes a comprehensive [Command Reference](commands) documenting all `native:*` Artisan commands
with their options and usage.

## Rebuild Required

After upgrading, you must rebuild your native application:

```shell
php artisan native:install --force
php artisan native:run
```

The `--force` flag ensures the `nativephp` directory is completely rebuilt with the v3 native project files.
