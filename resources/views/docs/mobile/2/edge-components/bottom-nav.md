---
title: Bottom Navigation
order: 100
---

## Overview

<div class="images-two-up not-prose">

![](/img/docs/edge-bottom-nav-ios.png)

![](/img/docs/edge-bottom-nav-android.png)

</div>

A bottom navigation bar with up to 5 items. Used for your app's primary navigation.

@verbatim
```blade
<native:bottom-nav label-visibility="labeled">
    <native:bottom-nav-item
        id="home"
        icon="home"
        label="Home"
        url="/home"
        :active="true"
    />
    <native:bottom-nav-item
        id="profile"
        icon="person"
        label="Profile"
        url="/profile"
        badge="3"
    />
</native:bottom-nav>
```
@endverbatim

## Props

- `label-visibility` - `labeled`, `selected`, or `unlabeled` (optional, default: `labeled`)
- `dark` - Force dark mode styling (optional)

## Children

A `<native:bottom-nav>` can contain up to 5 `<native:bottom-nav-item>` elements.

- `id` - Unique identifier
- `icon` - A named [icon](icons)
- `label` - Accessibility label (optional)
- `url` - A URL to navigate to in the web view (optional)
- `active` - Highlight this item as active (optional, default: `false`)
- `badge` - Badge text/number (optional)
- `news` - Show "new" indicator dot (optional, default: `false`)

<aside>

Any `url` that doesn't match the web view's domain will open in the user's default browser.

</aside>

### `badge` example
<div class="sm:w-1/2">

![](/img/docs/edge-bottom-nav-item-badge.png)

</div>
