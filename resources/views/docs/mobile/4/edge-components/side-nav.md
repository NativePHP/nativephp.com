---
title: Side Navigation
order: 370
---

## Overview

<x-docs.edge-preview
    ios="edge-side-nav-ios.png"
    android="edge-side-nav-android.png"
    source="resources/views/native/edge-components/side-nav-drawer.blade.php"
    alt="Side navigation drawer"
    edge="both"
    :sidebar-width-ios="85"
    :sidebar-width-android="87"
/>

A slide-out navigation drawer. Side navigation is hosted by the
[`Drawer` layout](../the-basics/layouts#drawer-navigation), and its content is any Blade view, so you build it from
ordinary EDGE components:

```php
use Native\Mobile\UI\Builders\Drawer;
use Native\Mobile\UI\Concerns\HasLayoutDrawer;

class AppLayout extends NativeLayout
{
    use HasLayoutDrawer;

    public function drawer(NativeComponent $screen): ?Drawer
    {
        return Drawer::make(view('native.sidebar'));
    }
}
```

`resources/views/native/sidebar.blade.php`:

@verbatim
```blade static
<native:scroll-view class="w-full h-full">
    <native:column class="w-full pb-6">
        <native:row class="w-full px-6 pt-6 pb-5 gap-4 items-center">
            <native:column class="w-12 h-12 rounded-full items-center justify-center bg-theme-primary">
                <native:icon name="person" :size="24" class="text-theme-on-primary" />
            </native:column>
            <native:column class="gap-1">
                <native:text class="text-lg font-semibold text-theme-on-surface">My App</native:text>
                <native:text class="text-sm text-theme-on-surface-variant">user@example.com</native:text>
            </native:column>
        </native:row>

        <native:divider />

        <native:pressable @navigate="/home" class="w-full px-3">
            <native:row class="w-full px-4 py-3 gap-4 items-center rounded-full bg-theme-primary/15">
                <native:icon name="home" :size="22" class="text-theme-primary" />
                <native:text class="text-base font-semibold text-theme-primary">Home</native:text>
            </native:row>
        </native:pressable>

        <native:pressable @navigate="/settings" class="w-full px-3">
            <native:row class="w-full px-4 py-3 gap-4 items-center">
                <native:icon name="settings" :size="22" class="text-theme-on-surface-variant" />
                <native:text class="text-base text-theme-on-surface">Settings</native:text>
            </native:row>
        </native:pressable>
    </native:column>
</native:scroll-view>
```
@endverbatim

See [Drawer navigation](../the-basics/layouts#drawer-navigation) for the `Drawer` builder itself — width, modal vs.
reveal presentation, per-screen overrides, and the built-in ☰ hamburger/edge-swipe/auto-close behavior.

<aside>

The `<native:side-nav-header>`, `<native:side-nav-item>` and `<native:side-nav-group>` elements below belong to the
self-contained `<native:side-nav>`, which has **no drawer host yet** — it compiles, but renders nothing on either
platform, and the same elements render nothing inside a `Drawer` either. Build drawer content from ordinary
components as shown above. The element reference is kept for when the inline drawer host lands.

</aside>

## Children

### `<native:side-nav-header>`

- `title` - Title text (optional)
- `subtitle` - Subtitle text (optional)
- `icon` - A named [icon](icon#icon-name-reference) (optional)
- `background-color` - Background color. Hex code (optional)
- `show-close-button` - Show a close &times; (optional, default: `true`) [Android]
- `pinned` - Keep header visible when scrolling (optional, default: `false`)

### `<native:side-nav-item>`

- `id` - Unique identifier (required)
- `label` - Display text (required)
- `icon` - A named [icon](icon#icon-name-reference) (required)
- `url` - A URL to navigate to in the web view (required)
- `open-in-browser` - Force the `url` to open in the device's default browser instead of the web view, whatever its domain (optional, default: `false`)
- `active` - Highlight this item as active (optional, default: `false`)
- `badge` - Badge text (optional)
- `badge-color` - Hex code or named color (optional)

<aside>

Any `url` that doesn't match the web view's domain will open in the user's default browser. Set `open-in-browser` to
force the browser even for a same-domain `url`.

</aside>

### `<native:side-nav-group>`

- `heading` - The group's heading (required)
- `expanded` - Initially expanded (optional, default: `false`)
- `icon` - Material icon (optional)

### `<native:divider>`

A visual separator between navigation items. See [Divider](divider) for its full reference.
