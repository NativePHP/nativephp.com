---
title: Bottom Navigation
order: 100
---

## Overview

A bottom navigation bar with up to 5 items for primary navigation.

@verbatim
```blade
<x-native:bottom-nav label-visibility="labeled">
    <x-native:bottom-nav-item
        id="home"
        icon="home"
        label="Home"
        url="/home"
        :active="true"
    />
    <x-native:bottom-nav-item
        id="profile"
        icon="person"
        label="Profile"
        url="/profile"
        badge="3"
        badge-color="#FF0000"
    />
</x-native:bottom-nav>
```
@endverbatim

## BottomNav Props

- `label-visibility` - `"labeled"` (default), `"selected"`, or `"unlabeled"`
- `dark` - Force dark mode styling (optional)

## BottomNavItem Props

- `id` - Unique identifier (required)
- `icon` - Material icon name (required)
- `label` - Display text (required)
- `url` - Navigation URL (required)
- `active` - Highlight as active (default: `false`)
- `badge` - Badge text/number (optional)
- `badge-color` - Badge background color hex (optional)
- `news` - Show "new" indicator dot (default: `false`)
