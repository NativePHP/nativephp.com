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
- GD PHP extension must be enabled, ensure it has enough memory (~2GB should be enough)

This image will be automatically resized for all Android densities and used as the base iOS app icon.
You must have the GD extension installed in your development machine's PHP environment for this to work.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-gradient-to-tl from-transparent to-violet-100/75 px-5 ring-1 ring-black/5 dark:from-slate-900/30 dark:to-indigo-900/35">

If you do not provide a custom app icon, a default one will be used.

</aside>
