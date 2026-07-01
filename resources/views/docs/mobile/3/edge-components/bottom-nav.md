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
- `active-color` - Color of the active tab's icon and label. Hex string (optional)
- `background-color` - Bar background color. Hex string. Wins over `dark`'s default (optional)
- `text-color` - Color of inactive tab icons and labels. Hex string. Active tabs use `active-color` (optional)

<aside>

The bar handles its own bottom safe-area inset internally — the home-indicator zone on iOS, the gesture-bar zone on
Android. Don't add your own padding for it. The bar's background extends to the screen edge while its content stays
above the indicator, mirroring iOS `UITabBar`.

</aside>

## Children

A `<native:bottom-nav>` can contain up to 5 `<native:bottom-nav-item>` elements.

### Props

- `id` - Unique identifier (required)
- `icon` - A named [icon](icons) (required)
- `label` - Accessibility label (required)
- `url` - A URL to navigate to in the web view (required)
- `active` - Highlight this item as active (optional, default: `false`)
- `badge` - Badge text/number, e.g. `"2"` — small red pill anchored top-right of the icon (optional)
- `news` - Show a small red dot anchored top-right of the icon. Mutually exclusive with `badge` (optional, default: `false`)

<aside>

Tab taps use `replace` semantics — tapping a tab swaps the current screen rather than pushing onto the stack. The
back chevron pops the entire tabs section in one step instead of stepping through tab history.

Any `url` that doesn't match a registered native route will exit to the web view and load that URL there.

</aside>

### `badge` example

<div class="sm:w-1/2">

![](/img/docs/edge-bottom-nav-item-badge.png)

</div>

## Builder API

When a `<native:bottom-nav>` is supplied by a [layout](../the-basics/layouts), you build it fluently with the `TabBar`
and `Tab` builders rather than writing it in Blade.

```php
use Native\Mobile\Edge\Layouts\Builders\Tab;
use Native\Mobile\Edge\Layouts\Builders\TabBar;

TabBar::make()
    ->dark()
    ->activeColor('#0891b2')
    ->labelVisibility('labeled')
    ->backgroundColor('#0F172A')
    ->textColor('#94A3B8')
    ->add(Tab::link('Chats',   '/syncup',          icon: 'chat_bubble')->badge('2'))
    ->add(Tab::link('Friends', '/syncup/friends',  icon: 'person.3.fill')->news())
    ->add(Tab::link('Profile', '/syncup/profile',  icon: 'person')->active());
```

### `TabBar` methods

- `make()` - Create a new builder
- `dark(bool $dark = true)` - Force dark mode styling
- `activeColor(string $color)` - Color of the active tab's icon and label
- `backgroundColor(string $color)` - Bar background color (overrides `dark()`'s default)
- `textColor(string $color)` - Color of inactive tab icons and labels
- `labelVisibility(string $mode)` - `"labeled"`, `"selected"`, or `"unlabeled"`
- `add(Tab $tab)` - Append a tab item

### `Tab` methods

- `link(string $label, string $url, ?string $icon = null)` - Build a tab. The id defaults to the label slugified
- `id(string $id)` - Override the auto-generated id
- `icon(string $icon)` - A named [icon](icons)
- `badge(string $badge, ?string $color = null)` - Show a numeric/text badge
- `news(bool $news = true)` - Show a red dot indicator
- `active(bool $active = true)` - Mark this tab as active
