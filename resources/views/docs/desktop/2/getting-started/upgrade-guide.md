---
title: Upgrade Guide
order: 1200
---

### High Impact Changes

- [New package name `nativephp/desktop`](#upgrading-to-20-from-1x)
- [Root namespace updated to `Native\Desktop`](#update-class-imports)
- [Drop macOS **_Catalina_** and **_Big Sur_** support](#macos-support)

### Medium Impact Changes

- [Serve command renamed](#renamed-codenativeservecode-command)
- [Node integration disabled by default](#security-defaults)

## Upgrading To 2.0 From 1.x

NativePHP for Desktop v2 is a significant architecture overhaul & security release. The package has moved to a new repository with a new name; `nativephp/desktop`.

Please replace `nativephp/electron` from your `composer.json` with the new package.

```json
"require": {
    "nativephp/electron": "^1.3", // [tl! remove]
    "nativephp/laravel": "^1.3", // [tl! remove]
    "nativephp/desktop": "^2.0" // [tl! add]
}
```

If you're requiring `nativephp/laravel` as well, please remove that too.

Then run `composer update` & `php artisan native:install` and you're good to go.

Afterwards the `native:install` script will be automatically installed as a `post-update-cmd`, so you won't have to manually run it after a composer update.

## Update class imports

With the package rename the root namespace has also changed. Please update all occurances of `Native\Laravel` to `Native\Desktop`.

```php
use Native\Laravel\Facades\Window; // [tl! remove]
use Native\Desktop\Facades\Window; // [tl! add]
```

## MacOS support

v2 drops support for macOS **_Catalina_** and **_Big Sur_**. This change comes from the Electron v38 upgrade and aligns with Apple's supported OS versions. Most users should be unaffected, but please check your deployment targets before upgrading.

- <a href="https://www.electronjs.org/docs/latest/breaking-changes#removed-macos-1015-support" target="_blank" rel="noopener">Electron Catalina support dropped</a>
- <a href="https://www.electronjs.org/docs/latest/breaking-changes#removed-macos-11-support" target="_blank" rel="noopener">Electron Big Sur support dropped</a>

## Renamed `native:serve` command

The `artisan native:serve` command has been renamed to `artisan native:dev` for better symmetry with the mobile package.
Please update the `composer native:dev` command to use the new name.

## Security defaults

`nodeIntegration` is now disabled by default. While this improves security, it may affect applications that rely on this functionality. You can easily re-enable it using `Window::webPreferences()` where needed.
