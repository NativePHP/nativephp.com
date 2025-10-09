---
title: Upgrade Guide
order: 1200
---

### High Impact Changes

- [New package name `nativephp/desktop`](#upgrading-to-20-from-1x)
- [Root namespace updated to `Native\Desktop`](#update-class-imports)
- [Dropped macOS **_Catalina_** and **_Big Sur_** support](#macos-support)

### Medium Impact Changes

- [Serve command renamed](#renamed-codenativeservecode-command)
- [Node integration disabled by default](#security-defaults)

### Low Impact Changes

- [Modifying the Electron backend](#modifying-the-electron-backend)
- [New build output location][#new-dist-location]

## Upgrading To 2.0 From 1.x

NativePHP for Desktop v2 is a significant architecture overhaul and security release. The package has moved to a new repository with a new name: `nativephp/desktop`.
Please replace `nativephp/electron` in your `composer.json` with the new package.

```json
"require": {
    "nativephp/electron": "^1.3", // [tl! remove]
    "nativephp/laravel": "^1.3", // [tl! remove]
    "nativephp/desktop": "^2.0" // [tl! add]
}
```

If you're requiring `nativephp/laravel` as well, please remove that too.

Then update the package:

```sh
composer update
php artisan native:install
```

After installation, the `native:install` script will be automatically registered as a `post-update-cmd`, so you won't have to manually run it after a composer update.

## Update class imports

With the package rename, the root namespace has also changed. Please update all occurrences of `Native\Laravel` to `Native\Desktop`.

```php
use Native\Laravel\Facades\Window; // [tl! remove]
use Native\Desktop\Facades\Window; // [tl! add]
```

## macOS support

v2 drops support for macOS **_Catalina_** and **_Big Sur_**. This change comes from the Electron v38 upgrade and aligns with Apple's supported OS versions. Most users should be unaffected, but please check your deployment targets before upgrading.

- <a href="https://www.electronjs.org/docs/latest/breaking-changes#removed-macos-1015-support" target="_blank" rel="noopener">Electron Catalina support dropped</a>
- <a href="https://www.electronjs.org/docs/latest/breaking-changes#removed-macos-11-support" target="_blank" rel="noopener">Electron Big Sur support dropped</a>

## Renamed `native:serve` command

The `artisan native:serve` command has been deprecated and renamed to `artisan native:run` for better symmetry with the mobile package.
Please update the `composer native:dev` script to reference the new run command.

## New `dist` location

The build output has moved to `nativephp/electron/dist`

## Security defaults

`nodeIntegration` is now disabled by default. While this improves security, it may affect applications that rely on this functionality. You can easily re-enable it using `Window::webPreferences()` where needed.

## Modifying the Electron backend

If you need to make any specific adjustments to the underlying Electron app, you can publish it using `php artisan native:install --publish`. This will export the Electron project to `{project-root}/nativephp/electron` and allow you to fully control all of NativePHP's inner workings.

Additionally, this will modify your `post-update-cmd` script to keep your project up to date, but note that you may need to cherry-pick any adjustments you've made after a `composer update`.
