---
title: Side Navigation
order: 400
---

## Overview

A slide-out navigation drawer with support for groups, headers, and dividers.

@verbatim
```blade
<x-native:side-nav gestures-enabled="true">
    <x-native:side-nav-header
        title="My App"
        subtitle="user@example.com"
        icon="person"
    />

    <x-native:side-nav-item
        id="home"
        label="Home"
        icon="home"
        url="/home"
        :active="true"
    />

    <x-native:side-nav-group heading="Account" :expanded="false">
        <x-native:side-nav-item
            id="profile"
            label="Profile"
            icon="person"
            url="/profile"
        />
        <x-native:side-nav-item
            id="settings"
            label="Settings"
            icon="settings"
            url="/settings"
        />
    </x-native:side-nav-group>

    <x-native:horizontal-divider />

    <x-native:side-nav-item
        id="help"
        label="Help"
        icon="help"
        url="https://help.example.com"
        open-in-browser="true"
    />
</x-native:side-nav>
```
@endverbatim

## SideNav Props

- `gestures-enabled` - Swipe to open (default: `false`)
- `label-visibility` - `"labeled"` (default), `"selected"`, or `"unlabeled"`
- `dark` - Force dark mode (optional)

## SideNavHeader Props

- `title` - Header title (optional)
- `subtitle` - Subtext (optional)
- `icon` - Material icon (optional)
- `image-url` - Header image URL (optional)
- `background-color` - Background hex (optional)
- `event` - Click event (optional)
- `show-close-button` - Show close X (default: `true`)
- `pinned` - Keep header visible when scrolling (default: `false`)

## SideNavItem Props

- `id` - Unique identifier (required)
- `label` - Display text (required)
- `icon` - Material icon (required)
- `url` - Navigation URL (required)
- `active` - Highlight as active (default: `false`)
- `badge` - Badge text (optional)
- `badge-color` - Badge color hex (optional)
- `open-in-browser` - Open URL externally (default: `false`)

## SideNavGroup Props

- `heading` - Group title (required)
- `expanded` - Initially expanded (default: `false`)
- `icon` - Material icon (optional)

## HorizontalDivider

Add visual separators between navigation items.

@verbatim
```blade
<x-native:horizontal-divider />
```
@endverbatim
