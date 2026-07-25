---
title: Floating Action Button
order: 222
---

## Overview

A Material-style floating action button that floats above your screen's content, pinned to a bottom corner. On the
wire a `<native:fab>` **is a [`pressable`](pressable)** — pre-styled as a FAB — so `@tap`, press feedback, and `url`
navigation all behave exactly like any other pressable, on both platforms.

@verbatim
```blade
<native:fab icon="add" @tap="createTask" />
```
@endverbatim

`@tap` names a public method on your component. Give it a `label` to get an extended FAB (icon + text pill):

@verbatim
```blade
<native:fab icon="edit" label="Compose" @tap="compose" />
```
@endverbatim

<aside>

A fab declared at the **top level** of your screen's Blade is hoisted out of the flex flow and floated above the
whole content area — including scroll views — no matter where in the file you declared it. A fab nested deeper in
the tree still renders, absolutely positioned within its nearest container. One top-level fab per screen is
hoisted.

</aside>

## Props

- `icon` - A named [icon](icon#icon-name-reference) — the cross-platform fallback (required for a visible fab unless a platform icon is given)
- `ios-icon` / `android-icon` - Per-platform icon overrides: an enum case (`App\Icons\Ios`, `App\Icons\Android`, `App\Icons\AndroidOutlined`) or a raw symbol string. `ios` / `android` are accepted as shorthand (optional). Same contract as [`<native:icon>`](icon#typed-icon-enums)
- `label` - Text label → renders as an extended FAB (optional)
- `url` - Navigate on tap, when no `@tap` handler is set (optional)
- `size` - `small`, `regular` (default), or `large` (optional)
- `position` - Horizontal corner: `end` (default) or `start` (optional)
- `bottom-offset` - Distance from the container bottom in dp (optional, default: `16`)
- `edge-offset` - Distance from the side edge in dp (optional, default: `16`)
- `corner-radius` - Override the default circular radius (optional)
- `container-color` - Background color. Hex string (optional, default: the theme `primary` token)
- `content-color` - Icon and label color. Hex string (optional, default: the theme `on-primary` token)
- `elevation` - Shadow elevation (optional, default: `6`)

By default the fab picks up your [theme tokens](../digging-deeper/theming) — `primary` for the container and
`on-primary` for its content — so it re-skins with the rest of the app. Only reach for `container-color` /
`content-color` when a fab genuinely shouldn't follow the theme.

## Platform icons

@verbatim
```blade
@use('App\Icons\Ios')
@use('App\Icons\Android')

<native:fab :ios-icon="Ios::Plus" :android-icon="Android::Add" @tap="createTask" />
```
@endverbatim

An enum-only fab (no shared `icon`) is fine — each platform resolves its own case.

## Positioning

@verbatim
```blade static
{{-- Bottom-left, lifted clear of a bottom bar --}}
<native:fab icon="mic" position="start" bottom-offset="88" @tap="record" />
```
@endverbatim

## Chrome, not layout

The fab is **inline-only chrome** — there is no layout builder for it. It composes freely with the rest of the
chrome system: an inline [`<native:top-bar>`](top-bar) or a layout's [`TabBar`](../the-basics/layouts) render
around it, and the fab floats above the screen's content either way.
