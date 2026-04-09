---
title: Layout & Styling
order: 150
---

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

Any element can respond to press and long-press gestures. Use `@press` and `@longPress` directives to bind Livewire
methods.

@verbatim
```blade
<native:column @press="handleTap" @longPress="handleLongPress">
    <native:text>Tap or long press me</native:text>
</native:column>
```
@endverbatim

- `@press` - Livewire method to call on tap
- `@longPress` - Livewire method to call on long press

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

- `safe-area` - Inset content to avoid system UI (boolean)

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

| Category | Classes |
|----------|---------|
| Width | `w-full`, `w-{n}`, `w-1/2`, `w-1/3`, `w-[100]` |
| Height | `h-full`, `h-{n}`, `h-[100]` |
| Padding | `p-{n}`, `px-{n}`, `py-{n}`, `pt-{n}`, `pr-{n}`, `pb-{n}`, `pl-{n}` |
| Margin | `m-{n}`, `mx-{n}`, `my-{n}`, `mt-{n}`, `mr-{n}`, `mb-{n}`, `ml-{n}` |
| Gap | `gap-{n}` |
| Background | `bg-{color}-{shade}`, `bg-white`, `bg-black`, `bg-[#hex]` |
| Text color | `text-{color}-{shade}`, `text-white`, `text-black`, `text-[#hex]` |
| Text size | `text-xs`, `text-sm`, `text-base`, `text-lg`, `text-xl` ... `text-6xl` |
| Font weight | `font-thin`, `font-light`, `font-normal`, `font-medium`, `font-semibold`, `font-bold`, `font-extrabold` |
| Border | `border`, `border-{n}`, `border-{color}-{shade}`, `border-[#hex]` |
| Rounded | `rounded`, `rounded-sm` ... `rounded-full`, `rounded-[16]` |
| Shadow | `shadow`, `shadow-sm` ... `shadow-2xl`, `shadow-none` |
| Opacity | `opacity-{0-100}` |
| Alignment | `items-start`, `items-center`, `items-end`, `items-stretch` |
| Justify | `justify-start`, `justify-center`, `justify-end`, `justify-between`, `justify-around`, `justify-evenly` |
| Self | `self-start`, `self-center`, `self-end`, `self-stretch` |
| Flex | `flex-1`, `flex-grow`, `flex-grow-0`, `flex-shrink`, `flex-shrink-0` |
| Safe area | `safe-area` |
| Text align | `text-left`, `text-center`, `text-right` |

<aside>

Tailwind classes and explicit attributes can be mixed freely. Explicit attributes take precedence over classes when
both set the same property.

</aside>
