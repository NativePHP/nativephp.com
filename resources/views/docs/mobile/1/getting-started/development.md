---
title: Development
order: 300
---


## The `nativephp` Directory

After running:

```bash
php artisan native:install
```

you’ll see a new `nativephp` directory at the root of your Laravel project as well as a nativephp.php config file in your config folder in the Laravel root.

This folder contains all the native project files that NativePHP generates for you.

You should not need to manually open or edit any native project files under normal circumstances.
NativePHP handles the heavy lifting for you.

## NATIVEPHP_APP_VERSION

The NATIVEPHP_APP_VERSION environment variable controls your app's versioning behavior.

When building for Android, NativePHP first copies the relevant Laravel files into a temporary directory, zips them, and embeds the archive into the Android project. When the app boots, it checks the embedded version against the previously installed version.

If the versions match, the app uses the existing files without re-extracting the archive.

If the versions differ — or if the version is set to ***DEBUG*** — the app updates itself by re-extracting the new bundle.

This mechanism ensures developers can iterate quickly during development, while providing a faster, more stable experience for end users once an app is published.

> Rule of Thumb:
> During development, keep NATIVEPHP_APP_VERSION set to DEBUG to always refresh the app.
> When preparing a new release, update it to a semantic version (e.g., 1.2.3) to enable versioned updates for your users.


