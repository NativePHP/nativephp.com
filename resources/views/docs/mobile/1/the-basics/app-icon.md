---
title: App Icons
order: 300
---

NativePHP makes it easy to apply a custom app icon to your iOS and Android apps.

## Supply your icon

Place a single high-resolution icon file at: `public/icon.png`.

### Requirements
- Format: PNG
- Size: 1024 × 1024 pixels
- Background: Transparent or solid — your choice

This image will be automatically resized for all Android densities and used as the base iOS app icon.
You must have the GD extension installed in your development machine's PHP environment for this to work.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

If you do not provide a custom app icon, a default one will be used.

</aside>
