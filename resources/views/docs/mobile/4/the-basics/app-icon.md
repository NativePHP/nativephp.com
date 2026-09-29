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
- Background: Must not contain any transparencies.
- GD PHP extension must be enabled, ensure it has enough memory (~2GB should be enough)

This image will be automatically resized for all Android densities and used as the base iOS app icon.
You must have the GD extension installed in your development machine's PHP environment for this to work.

<aside>

If you do not provide a custom app icon, a default one will be used.

</aside>
