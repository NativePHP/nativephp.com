---
title: Upgrade Guide
order: 3
---

## Upgrading To 4.0 From 3.x

v4's headline is [SuperNative](../architecture/super-native) — fully native UI. Most of the release is additive,
but there is **one breaking change to your dependencies**: a handful of APIs that used to be separate plugins are
now core built-ins.

### Device, Dialog, File and System are now built in

`Device`, `Dialog`, `File`, and `System` now ship inside `nativephp/mobile` — their native bridge functions are
registered by core. `nativephp/mobile` v4 declares a Composer **conflict** with the four standalone plugins, so
`composer update` will refuse to resolve until you **remove them**.

Uninstall each one you have (this also unregisters it from your `NativeServiceProvider`):

```shell
php artisan native:plugin:uninstall nativephp/mobile-device
php artisan native:plugin:uninstall nativephp/mobile-dialog
php artisan native:plugin:uninstall nativephp/mobile-file
php artisan native:plugin:uninstall nativephp/mobile-system
```

Or remove them directly with Composer if they were never registered in your `NativeServiceProvider`:

```shell
composer remove nativephp/mobile-device nativephp/mobile-dialog nativephp/mobile-file nativephp/mobile-system
```

**No application code changes are required.** The `Native\Mobile\Facades\{Device, Dialog, File, System}` facades
and their events (`ButtonPressed`, etc.) are unchanged. Their docs now live in The Basics section:
[Device](../the-basics/device), [Dialog](../the-basics/dialogs), [File](../the-basics/file), and
[System](../the-basics/system).

### The Vite dev server is now opt-in

`native:run` and `native:watch` no longer start the Vite dev server automatically. If you rely on Vite HMR during
development (React/Vue/Tailwind, etc.), add the `--vite` flag:

```shell
php artisan native:watch --vite
php artisan native:run --watch --vite
```

The old `--no-vite` flag still exists but is now redundant — Vite is off unless you ask for it. If you have
`--no-vite` in your scripts, you can drop it.

### Update your dependency

```json
"require": {
    "nativephp/mobile": "~3.1.0" // [tl! remove]
    "nativephp/mobile": "~4.0.0" // [tl! add]
}
```

```sh
composer update
php artisan native:install --force
```

<aside>

The `--force` flag rebuilds the native project files with the v4 versions.

</aside>

### For Plugin Developers

Widen the constraint in your plugin's `composer.json` to allow the new release:

```json
"require": {
    "nativephp/mobile": "^3.0" // [tl! remove]
    "nativephp/mobile": "^3.0|^4.0" // [tl! add]
}
```

If your plugin depends on any of the conflicting plugins above, remove them from your `composer.json`'s `require`.
