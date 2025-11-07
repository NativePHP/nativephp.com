---
title: Floating Action Button
order: 300
---

## Overview

A floating action button (FAB) that sits above the content.

@verbatim
```blade
<x-native:fab
    icon="add"
    label="Create"
    event="create-item"
    size="regular"
    position="end"
/>
```
@endverbatim

## Props

- `icon` - Material icon name (required)
- `label` - Button text (optional, shows extended FAB)
- `url` - Navigation URL (optional)
- `event` - Event to dispatch (optional)
- `size` - `"small"`, `"regular"` (default), or `"large"`
- `position` - `"start"`, `"center"`, or `"end"` (default)
- `bottom-offset` - Distance from bottom in pixels (optional)
- `elevation` - Shadow depth (optional)
- `corner-radius` - Border radius in pixels (optional)
- `container-color` - Background hex (optional)
- `content-color` - Icon/text hex (optional)

## Material Icons

All components use Material Symbols icons. Common icons include:

`home`, `person`, `settings`, `search`, `menu`, `add`, `edit`, `delete`, `favorite`, `share`, `notifications`, `inbox`, `shopping_cart`, `help`, `info`, `arrow_back`, `close`, `check`, `more_vert`

Browse all icons at [Google Fonts Icons](https://fonts.google.com/icons).
