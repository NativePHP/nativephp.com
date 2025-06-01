---
title: App Assets
order: 400
---

## Customizing Your App Icon

NativePHP makes it easy to apply a custom app icon to your iOS and Android builds.

### Step 1: Provide Your Icon

Place a single high-resolution icon file at: `public/icon.png`


### Requirements:
- Format: PNG
- Size: 1024 × 1024 pixels
- Shape: Square
- Background: Transparent or solid — your choice

Note: This image will be automatically resized for all Android densities and used as the base iOS app icon.

---

## Compiling CSS and JavaScript

Your device behaves like a server, so assets must be compiled before deployment.

To ensure your latest styles and JavaScript are included, run: `npm run build` before running: `php artisan native:run`.

If you’ve made changes to your frontend, this step is required to see them reflected in the app.

---

## Using the --watch Flag (Experimental)

NativePHP includes an experimental `--watch` flag that enables automatic file syncing while the app is running:

php artisan native:run --watch

This is useful during development for quickly testing changes without rebuilding the entire app.

### Caveats

- This feature is currently best suited for **Blade** and **Livewire** applications.
- It does **not** currently detect or sync **compiled frontend assets**, such as those built with Vite or used by **Inertia.js**.
- If you're working with a JavaScript-heavy stack (Vue, React, Inertia), you should continue using `npm run build` before launching the app with `native:run`.

### Recommendation

Use `--watch` when you're iterating on Blade views or Livewire components. For all other use cases, treat this flag as experimental and optional.

---

## Optional: Installing with ICU Support

By default, NativePHP installs a smaller PHP runtime without ICU (International Components for Unicode) to keep app size minimal.

If your Laravel app uses features that rely on `intl` (such as number formatting, localized date handling, or advanced string collation), you’ll need ICU support enabled.

To include ICU during installation, select it when running: `php artisan native:install`.

This will install a version of PHP with full ICU support. Note that it increases the PHP binary size significantly (typically from ~16MB to ~44MB).

**Important:** If you plan to use [Filament](https://filamentphp.com/) in your app, you must enable this option. Filament relies on the `intl` extension for formatting and localization features.




