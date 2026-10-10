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

## Orientation

Read the orientation of the app window:

```php
System::orientation();  // 'portrait' | 'landscape'
System::isPortrait();   // bool
System::isLandscape();  // bool
```

When it changes, the `OrientationChanged` event fires. `$orientation` is `'portrait'` or `'landscape'`:

```php
use Native\Mobile\Attributes\On;
use Native\Mobile\Events\System\OrientationChanged;

#[On(OrientationChanged::class)]
public function rotated(string $orientation): void
{
    $this->columns = $orientation === 'landscape' ? 4 : 2;
}
```

`OrientationChanged` [broadcasts globally](events#broadcasting-globally). Next to the `#[On]` methods of the live
screen, `Event::listen` hears it anywhere in your app.

<aside>

`System::flashlight()` <x-docs.version-badge removed="4.1" /> has been removed — use
[`Device::flashlight()`](device#flashlight) instead.

</aside>
