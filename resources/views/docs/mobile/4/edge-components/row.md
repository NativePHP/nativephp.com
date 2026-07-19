---
title: Row
order: 320
---

## Overview

A horizontal flex container that arranges its children from left to right. Use rows for side-by-side layouts, toolbars,
and inline groupings.

@verbatim
```blade
<native:row class="gap-3 items-center">
    <native:icon name="star" color="#FBBF24" />
    <native:text class="text-lg text-theme-on-surface">4.8 Rating</native:text>
</native:row>
```
@endverbatim

## Children

Accepts any EDGE elements as children. Children are arranged horizontally from left to right.

## Supported Tailwind classes

Row inherits the full class set documented at [Layout & Styling](layout#supported-tailwind-classes). The classes that
shape how a row behaves specifically:

| Class | Effect on a row |
|---|---|
| `gap-N` | **Horizontal** spacing between children |
| `items-*` | **Vertical** (cross-axis) alignment of children: `items-start`, `items-center`, `items-end`, `items-stretch` |
| `justify-*` | **Horizontal** (main-axis) distribution: `justify-start`, `justify-center`, `justify-end`, `justify-between`, `justify-around`, `justify-evenly` |
| `flex-1` | Fills remaining space in the parent flex container |

Everything else from the shared list applies the same as on any element (`w-*`, `h-*`, `p-*`, `m-*`, `bg-*`,
`rounded-*`, `shadow-*`, `dark:*`, `ios:*` / `android:*`, `glass:*`, alpha suffix `/N`, arbitrary `prefix-[value]`).

## Examples

### Toolbar with spacer

@verbatim
```blade
<native:row class="w-full px-4 py-2 items-center">
    <native:text class="text-xl font-bold text-theme-on-surface">Title</native:text>
    <native:spacer />
    <native:icon name="search" :size="24" class="text-theme-on-surface" />
</native:row>
```
@endverbatim

### Evenly spaced items

@verbatim
```blade
<native:row class="w-full justify-evenly">
    <native:text class="text-theme-on-surface">One</native:text>
    <native:text class="text-theme-on-surface">Two</native:text>
    <native:text class="text-theme-on-surface">Three</native:text>
</native:row>
```
@endverbatim

### Inline label and value

@verbatim
```blade
<native:row class="w-full justify-between items-center">
    <native:text class="text-base text-theme-on-surface-variant">Status</native:text>
    <native:row class="gap-1 items-center">
        <native:icon name="check" color="#22C55E" :size="16" />
        <native:text class="text-base font-semibold text-green-500">Active</native:text>
    </native:row>
</native:row>
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Row;
use Native\Mobile\Edge\Elements\Text;

Row::make(
    Text::make('Left'),
    Text::make('Right'),
)->gap(8)->alignItems(1);
```

- `make(Element ...$children)` - Create a row with children. Layout / style fluent methods are inherited from the
  base `Element` class — see [Layout & Styling](layout)
