---
title: Side Navigation
order: 400
---

## Overview

<div class="images-two-up not-prose">

![](/img/docs/edge-side-nav-ios.png)

![](/img/docs/edge-side-nav-android.png)

</div>

A slide-out navigation drawer with support for groups, headers, and dividers.

@verbatim
```blade
<native:side-nav gestures-enabled="true">
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

    <native:horizontal-divider />

    <native:side-nav-item
        id="help"
        label="Help"
        icon="help"
        url="https://help.example.com"
        open-in-browser="true"
    />
</native:side-nav>
```
@endverbatim

## Props

- `gestures-enabled` - Swipe to open (default: `false`) [Android]
- `dark` - Force dark mode (optional)

<aside>

On iOS, gesture support is always enabled for the side nav.

</aside>

## Children

### `<native:side-nav-header>`

- `title` - Title text (optional)
- `subtitle` - Subtitle text (optional)
- `icon` - A named [icon](icons) (optional)
- `background-color` - Background color. Hex code (optional)
- `show-close-button` - Show a close &times; (optional, default: `true`) [Android]
- `pinned` - Keep header visible when scrolling (optional, default: `false`)

### `<native:side-nav-item>`

- `id` - Unique identifier (required)
- `label` - Display text (required)
- `icon` - A named [icon](icons) (required)
- `url` - A URL to navigate to in the web view (required)
- `active` - Highlight this item as active (optional, default: `false`)
- `badge` - Badge text (optional)
- `badge-color` - Hex code or named color (optional)

<aside>

Any `url` that doesn't match the web view's domain will open in the user's default browser.

</aside>

### `<native:side-nav-group>`

- `heading` - The group's heading (required)
- `expanded` - Initially expanded (optional, default: `false`)
- `icon` - Material icon (optional)

### `<native:horizontal-divider>`

Add visual separators between navigation items. This item has no properties.
