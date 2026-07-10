---
title: System
order: 140
---

## Overview

The `System` API covers system-level concerns — platform detection and opening the app's settings screen.

It's a **core built-in**: the facade resolves with nothing to install or register.

```php
use Native\Mobile\Facades\System;
```

## Platform detection

```php
System::isIos();      // true on iOS
System::isAndroid();  // true on Android
System::isMobile();   // true on either platform
```

Use these to branch behavior or conditionally render UI for a specific platform.

Each is also available as a global helper function — `isIos()`, `isAndroid()`, `isMobile()` — for terse use in
Blade and components:

```php
if (isAndroid()) {
    // ...
}
```

In Blade, the same checks are available as conditional directives:

@verbatim
```blade
@ios
    {{-- iOS-only markup --}}
@endios

@android
    {{-- Android-only markup --}}
@endandroid

@mobile
    {{-- running inside the native app (iOS or Android) --}}
@endmobile

@web
    {{-- running in a browser / outside the native app --}}
@endweb
```
@endverbatim

Each also supports the usual `@@else…` and `@@unless…` forms — `@@elseios`, `@@unlessandroid`, and so on.

## App settings

Open the app's page in the device Settings app — useful for sending a user to re-grant a permission they
previously denied:

```php
System::appSettings();
```

## Appearance (light / dark)

Reading the current appearance and reacting to theme changes lives with the rest of theming — see
[Theming → Appearance in PHP](../digging-deeper/theming#appearance-in-php) for `System::appearance()`, `isDarkMode()`,
`isLightMode()`, the `isDark()` / `theme()` helpers, and the `AppearanceChanged` event.

<aside>

`System::flashlight()` is **deprecated** — use [`Device::flashlight()`](device#flashlight) instead.

</aside>
