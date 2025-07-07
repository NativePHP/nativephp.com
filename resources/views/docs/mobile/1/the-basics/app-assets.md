---
title: App Assets
order: 200
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

Note: This image will be automatically resized for all Android densities and used as the base iOS app icon. You must have the GD extension installed and active in your local PHP environment for this to work.

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
