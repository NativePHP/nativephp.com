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

A slide-out navigation drawer with support for groups, headers, and dividers. Side navigation is hosted by the
[`Drawer` layout](../the-basics/layouts#drawer-navigation) — its content is a plain Blade view, so you build it from
the components below rather than a self-contained `<native:side-nav>` element:

```php
use Nativephp\NativeUi\Builders\Drawer;
use Nativephp\NativeUi\Concerns\HasLayoutDrawer;

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
<native:side-nav-header
    title="My App"
    subtitle="user@example.com"
    icon="person"
/>

<native:side-nav-item
    id="home"
    label="Home"
    icon="home"
    url="/home"
    :active="true"
/>

<native:side-nav-group heading="Account" :expanded="false">
    <native:side-nav-item
        id="profile"
        label="Profile"
        icon="person"
        url="/profile"
    />
    <native:side-nav-item
        id="settings"
        label="Settings"
        icon="settings"
        url="/settings"
    />
</native:side-nav-group>

<native:divider />

<native:side-nav-item
    id="help"
    label="Help"
    icon="help"
    url="https://help.example.com"
    open-in-browser="true"
/>
```
@endverbatim

See [Drawer navigation](../the-basics/layouts#drawer-navigation) for the `Drawer` builder itself — width, modal vs.
reveal presentation, per-screen overrides, and the built-in ☰ hamburger/edge-swipe/auto-close behavior.

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
