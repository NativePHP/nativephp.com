---
title: Top Bar
order: 50
---

## Overview
<div class="images-two-up not-prose">

![](/img/docs/edge-top-bar-ios.png)

![](/img/docs/edge-top-bar-android.png)

</div>

A top bar with title and action buttons. This renders at the top of the screen.

@verbatim
```blade
<native:top-bar title="Dashboard" subtitle="Welcome back">
    <native:top-bar-action
        id="search"
        label="Search"
        icon="search"
        :url="route('search')"
    />
    <native:top-bar-action
        id="settings"
        icon="settings"
        label="Settings"
        url="https://yourapp.com/my-account"
    />
</native:top-bar>
```
@endverbatim

## Props

- `title` - The title text (required)
- `show-navigation-icon` - Show back/menu button (optional, default: `true`)
- `label` - If more than 5 actions, iOS will display an overflow menu and the labels assigned to each item (optional)
- `background-color` - Background color. Hex code (optional)
- `text-color` - Text color. Hex code (optional)
- `elevation` - Shadow depth 0-24 (optional) [Android]

## Children

A `<native:top-bar>` can contain up to 10 `<native:top-bar-action>` elements. These populate the trailing edge,
collapsing to a menu if there are too many.

### Props
- `id` - Unique identifier (required)
- `icon` - A named [icon](icons) (required)
- `label` - Accessibility label (optional)
- `url` - A URL to navigate to in the web view (optional)

<aside>

Any `url` that doesn't match the web view's domain will open in the user's default browser.

</aside>
