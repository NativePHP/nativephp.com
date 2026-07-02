---
title: Layout & Styling
order: 150
super_native: true
---

<x-docs.super-native-beta />

## Overview

Every EDGE element shares a common set of layout, styling, and event attributes. These are inherited from the base
`Element` class and can be applied to any native component -- containers, text, buttons, images, and more.

This page documents the shared attribute system that powers the layout engine across all EDGE elements.

## Sizing

Control element dimensions with width, height, and fill attributes.

@verbatim
```blade
{{-- Fixed dimensions (in dp) --}}
<native:column :width="200" :height="100">
    ...
</native:column>

{{-- Fill available space --}}
<native:column fill-width>
    ...
</native:column>

{{-- Fill both axes --}}
<native:column fill>
    ...
</native:column>

{{-- Percentage width --}}
<native:column width="50%">
    ...
</native:column>
```
@endverbatim

- `width` - Width in dp (float) or percentage string (e.g. `"50%"`)
- `height` - Height in dp (float) or percentage string
- `fill` - Fill both width and height of parent (boolean)
- `fill-width` - Fill parent width (boolean)
- `fill-height` - Fill parent height (boolean)
- `min-width` - Minimum width in dp (float)
- `max-width` - Maximum width in dp (float)
- `min-height` - Minimum height in dp (float)
- `max-height` - Maximum height in dp (float)
- `aspect-ratio` - Width-to-height ratio (float, e.g. `1.0` for square)

## Spacing

Padding and margin follow CSS shorthand conventions. Pass a single value for uniform spacing, or an array for
per-side control.

@verbatim
```blade
{{-- Uniform padding --}}
<native:column :padding="16">
    ...
</native:column>

{{-- Vertical | Horizontal --}}
<native:column :padding="[12, 16]">
    ...
</native:column>

{{-- Top | Right | Bottom | Left --}}
<native:column :padding="[8, 16, 24, 16]">
    ...
</native:column>

{{-- Uniform margin --}}
<native:column :margin="8">
    ...
</native:column>

{{-- Gap between children --}}
<native:column :gap="12">
    ...
</native:column>
```
@endverbatim

- `padding` - Inner spacing. Single value (float) or array of 2-4 values
- `margin` - Outer spacing. Single value (float) or array of 2-4 values
- `gap` - Space between children in dp (float)

## Flex Layout

The layout engine uses a Flexbox-based system. Containers (column, row) arrange children along a main axis, and flex
properties control how children grow, shrink, and align.

@verbatim
```blade
{{-- Grow to fill remaining space --}}
<native:column :flex-grow="1">
    ...
</native:column>

{{-- Prevent shrinking --}}
<native:row :flex-shrink="0">
    ...
</native:row>
```
@endverbatim

- `flex-grow` - How much this element grows relative to siblings (float, default: `0`)
- `flex-shrink` - How much this element shrinks when space is limited (float)
- `flex-basis` - Initial size before flex distribution (float or string)

<aside>

The Tailwind `flex-1` class is shorthand for `flex-grow: 1; flex-basis: 0` — the most common pattern for "fill the
remaining space along the parent's main axis."

</aside>

## Alignment

Alignment values are integers that map to standard flex alignment:

| Value | Meaning |
|-------|---------|
| `0` | start |
| `1` | center |
| `2` | end |
| `3` | stretch |
| `4` | baseline |

@verbatim
```blade
{{-- Center children on both axes --}}
<native:column center>
    ...
</native:column>

{{-- Cross-axis alignment (horizontal in a column) --}}
<native:column :align-items="1">
    <native:text>Centered text</native:text>
</native:column>

{{-- Main-axis distribution --}}
<native:row :justify-content="3">
    <native:text>Left</native:text>
    <native:text>Right</native:text>
</native:row>
```
@endverbatim

- `align-items` - Cross-axis alignment for children (int, 0-4)
- `justify-content` - Main-axis distribution (int, 0=start, 1=center, 2=end, 3=space-between, 4=space-around, 5=space-evenly)
- `align-self` - Override parent's `align-items` for this element (int, 0-4)
- `center` - Shorthand: sets both `align-items` and `justify-content` to center (boolean)

<aside>

A child with `w-full` (or `h-full`) overrides its parent's `items-center` along that axis — same semantics as CSS
`align-self: stretch`. This is the easiest way to make one row in a centered column span the full width.

</aside>

## Style

Visual styling attributes that apply to any element.

@verbatim
```blade
<native:column
    bg="#F0F0FF"
    :border-radius="12"
    :border-width="1"
    border-color="#E2E8F0"
    :opacity="0.9"
    :elevation="4"
>
    ...
</native:column>
```
@endverbatim

- `bg` - Background color as hex string (e.g. `"#FF0000"`, `"#80FF000080"` for alpha)
- `border-radius` - Corner rounding in dp (float)
- `border-width` - Border width in dp (float). Must be used together with `border-color`
- `border-color` - Border color as hex string. Must be used together with `border-width`
- `opacity` - Element opacity from 0.0 to 1.0 (float)
- `elevation` - Shadow depth (float). Maps to platform shadow/elevation

## Events

Any element can respond to press and long-press gestures. Use `@press` and `@longPress` directives to bind methods on
the route's PHP component class.

@verbatim
```blade
<native:column @press="handleTap" @longPress="handleLongPress">
    <native:text>Tap or long press me</native:text>
</native:column>
```
@endverbatim

- `@press` - PHP method to call on tap
- `@longPress` - PHP method to call on long press

## Safe Area

Respect the device's safe area insets (notch, home indicator, status bar) by adding the `safe-area` attribute. This is
typically applied to your outermost column.

@verbatim
```blade
<native:column fill safe-area>
    {{-- Content will not overlap the notch or home indicator --}}
</native:column>
```
@endverbatim

- `safe-area` - Inset content on both top and bottom edges (boolean)
- `safe-area-top` - Inset only the top edge (status bar / notch)
- `safe-area-bottom` - Inset only the bottom edge (home indicator)

See [Safe Area](../the-basics/safe-area) for the full picture, including how the framework's [layout](../the-basics/layouts)
chrome already handles safe-area insets for you.

## Visibility

Hide elements without removing them from the tree.

@verbatim
```blade
<native:column hidden>
    {{-- This element is not displayed --}}
</native:column>
```
@endverbatim

- `hidden` - Hide this element (boolean)

## Dark Mode

Override styles for dark mode using the `dark:` prefix with Tailwind classes, or pass a `dark` attribute array.

@verbatim
```blade
{{-- Tailwind dark mode --}}
<native:column class="bg-white dark:bg-slate-900">
    <native:text class="text-black dark:text-white">
        Adapts to dark mode
    </native:text>
</native:column>
```
@endverbatim

Dark mode overrides currently support `bg`, `color`, `border-color`, `opacity`, and `font-size`.

## Tailwind Classes

EDGE includes a built-in Tailwind CSS parser that converts familiar utility classes into native layout attributes. Use
the `class` attribute on any element.

@verbatim
```blade
<native:column class="w-full p-4 gap-3 bg-white rounded-xl shadow-md items-center">
    <native:text class="text-2xl font-bold text-slate-900">
        Styled with Tailwind
    </native:text>
</native:column>
```
@endverbatim

### Supported Tailwind classes

The parser recognizes the classes listed below.

| Category | Classes |
|----------|---------|
| Width | `w-full`, `w-N`, fractional (`w-1/2`, `w-1/3`, `w-2/3`, `w-1/4`, `w-3/4`, `w-1/5`…), arbitrary `w-[N]` |
| Height | `h-full`, `h-N`, arbitrary `h-[N]` |
| Padding | `p-N`, `px-N`, `py-N`, `pt-N`, `pr-N`, `pb-N`, `pl-N`, arbitrary `p-[N]` etc. |
| Margin | `m-N`, `mx-N`, `my-N`, `mt-N`, `mr-N`, `mb-N`, `ml-N`, arbitrary `m-[N]` etc. |
| Gap | `gap-N`, `gap-[N]` (uniform — no `gap-x-*` or `gap-y-*`) |
| Position | `absolute`, `relative`, `top-N`, `right-N`, `bottom-N`, `left-N`, arbitrary `top-[N]` etc. |
| Flex | `flex-1`, `flex-grow`, `flex-grow-0`, `flex-shrink`, `flex-shrink-0` |
| Items (cross-axis) | `items-start`, `items-center`, `items-end`, `items-stretch` |
| Justify (main-axis) | `justify-start`, `justify-center`, `justify-end`, `justify-between`, `justify-around`, `justify-evenly` |
| Self | `self-start`, `self-center`, `self-end`, `self-stretch` |
| Background | `bg-{palette}-{shade}` (e.g. `bg-red-500`), `bg-white`, `bg-black`, `bg-transparent`, `bg-[#hex]`, `bg-theme-{token}` |
| Text color | `text-{palette}-{shade}`, `text-white`, `text-black`, `text-transparent`, `text-[#hex]`, `text-theme-{token}` |
| Border color | `border-{palette}-{shade}`, `border-white`, `border-black`, `border-transparent`, `border-[#hex]`, `border-theme-{token}` |
| Border width | `border` (1dp), `border-2`, `border-4`, `border-8` |
| Rounded | `rounded` (4dp), `rounded-sm`, `rounded-md`, `rounded-lg`, `rounded-xl`, `rounded-2xl`, `rounded-3xl`, `rounded-full`, `rounded-[N]` |
| Shadow | `shadow`, `shadow-sm`, `shadow-md`, `shadow-lg`, `shadow-xl`, `shadow-2xl`, `shadow-inner`, `shadow-none` |
| Opacity | `opacity-{0..100}`, arbitrary `opacity-[0.5]` |
| Text size | `text-xs`, `text-sm`, `text-base`, `text-lg`, `text-xl`, `text-2xl`, `text-3xl`, `text-4xl`, `text-5xl`, `text-6xl`, arbitrary `text-[N]` |
| Font weight | `font-thin`, `font-extralight`, `font-light`, `font-normal`, `font-medium`, `font-semibold`, `font-bold`, `font-extrabold`, `font-black` |
| Text align | `text-left`, `text-center`, `text-right` |
| Safe area | `safe-area` (top + bottom), `safe-area-top`, `safe-area-bottom` |
| Liquid Glass | `glass`, `glass:prominent`, `glass:interactive`, `glass:clear` (compose: `glass:clear:interactive`) |

**Variants** — prepend any class:

| Prefix | Effect |
|----------|---------|
| `dark:` | Applies in dark mode (e.g. `dark:bg-zinc-900`) |
| `ios:` | Applies on iOS only — drops silently on Android |
| `android:` | Applies on Android only — drops silently on iOS |

Variants compose freely: `ios:dark:bg-zinc-800`, `dark:ios:bg-zinc-800` — both work.

**Alpha suffix** — append `/N` to any color class for opacity (Tailwind v3+ syntax):

```
bg-purple-500/40      bg-[#FF0000]/60      text-white/80
border-theme-outline/50
```

**Arbitrary values** — `prefix-[value]` for the prefixes shown above: `w`, `h`, `p`/`px`/`py`/`pt`/`pr`/`pb`/`pl`,
`m`/`mx`/`my`/`mt`/`mr`/`mb`/`ml`, `gap`, `bg`, `text`, `border`, `rounded`, `opacity`, `top`, `right`, `bottom`, `left`.

<aside>

Tailwind classes are the canonical styling API. The element-level attributes documented above (`bg`, `border-radius`,
`align-items`, etc.) exist as the underlying primitives — use them when building elements fluently in PHP. In Blade,
prefer classes.

</aside>

