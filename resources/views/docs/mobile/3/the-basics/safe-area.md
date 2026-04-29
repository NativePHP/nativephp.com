---
title: Safe Area
order: 180
---

## Overview

Safe areas are the regions of the screen not obscured by system UI — the notch and status bar at the top, the home
indicator or gesture bar at the bottom. The framework lets you inset content past those regions with a single
attribute.

`safe_area` is encoded as a single bitmask field on the layout struct, so you can request both edges, only the top,
or only the bottom.

## Tailwind classes

The simplest way to apply a safe-area inset is with a class:

@verbatim
```blade
<native:column class="w-full h-full safe-area">
    {{-- Insets both top and bottom --}}
</native:column>

<native:column class="w-full h-full safe-area-top">
    {{-- Insets only the status-bar / notch --}}
</native:column>

<native:column class="w-full h-full safe-area-bottom">
    {{-- Insets only the home-indicator zone --}}
</native:column>
```
@endverbatim

- `safe-area` - Inset both top and bottom edges
- `safe-area-top` - Inset only the top edge (status bar / notch)
- `safe-area-bottom` - Inset only the bottom edge (home indicator)

## PHP API

The same variants are available as fluent methods when building elements in PHP:

```php
use Native\Mobile\Edge\Elements\Column;

Column::make()->fill()->safeArea();        // both edges
Column::make()->fill()->safeAreaTop();     // top only
Column::make()->fill()->safeAreaBottom();  // bottom only
```

## Layouts already handle this for you

When a screen is wrapped by a [layout](layouts), the framework's `wrapWithChrome` flow picks the right safe-area
variant for the wrapper based on which chrome is present:

| Chrome           | Wrapper inset    | Why                                                  |
|------------------|------------------|------------------------------------------------------|
| TabBar present   | `safeAreaTop()`  | The tab bar handles its own bottom inset internally  |
| NavBar only      | `safeAreaBottom()` | The nav bar handles its own top inset              |
| No chrome        | `safeArea()`     | Wrapper handles both edges                           |
| Both bars        | (neither)        | Each bar handles its own edge                        |

<aside>

Don't apply `safe-area-bottom` to a child of a chrome-wrapped screen — the chrome already handles it, and stacking
the insets pushes content up by ~34pt on iPhones with a home indicator.

Use `safe-area*` directly on standalone full-screen views without chrome (e.g. a login page or a custom modal).

</aside>

## Common pattern: standalone full-screen view

A login screen with no nav bar and no tab bar should apply `safe-area` itself:

@verbatim
```blade
<native:column class="w-full h-full bg-white safe-area items-center justify-center p-6">
    <native:text class="text-3xl font-bold">Sign in</native:text>
    {{-- ... --}}
</native:column>
```
@endverbatim
