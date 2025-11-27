---
title: Top Bar
order: 50
---

## Overview

A top app bar with title and action buttons.

@verbatim
```blade
<x-native:top-bar title="Dashboard" subtitle="Welcome back">
    <x-native:top-bar-action
        id="search"
        icon="search"
        event="search-clicked"
    />
    <x-native:top-bar-action
        id="settings"
        icon="settings"
        url="/settings"
    />
</x-native:top-bar>
```
@endverbatim

## TopBar Props

- `title` - Bar title (required)
- `subtitle` - Secondary text (optional)
- `show-navigation-icon` - Show back/menu button (default: `true`)
- `background-color` - Background hex color (optional)
- `text-color` - Text hex color (optional)
- `elevation` - Shadow depth 0-24 (optional)

## TopBarAction Props

- `id` - Unique identifier (required)
- `icon` - Material icon name (required)
- `label` - Accessibility label (optional)
- `url` - Navigation URL (optional)
- `event` - Event to dispatch (optional)
