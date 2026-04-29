---
title: Row
order: 210
---

## Overview

A horizontal flex container that arranges its children from left to right. Use rows for side-by-side layouts, toolbars,
and inline groupings.

@verbatim
```blade
<native:row :gap="12" :align-items="1">
    <native:icon name="star" color="#FBBF24" />
    <native:text class="text-lg">4.8 Rating</native:text>
</native:row>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported. The most commonly used with rows:

- `gap` - Horizontal space between children in dp (optional, float)
- `align-items` - Vertical alignment of children: `0`=start, `1`=center, `2`=end, `3`=stretch (optional, default: `0`)
- `justify-content` - Horizontal distribution: `0`=start, `1`=center, `2`=end, `3`=space-between, `4`=space-around, `5`=space-evenly (optional, default: `0`)
- `padding` - Inner spacing, single value or array (optional)
- `bg` - Background color as hex (optional)

## Children

Accepts any EDGE elements as children. Children are arranged horizontally from left to right.

## Examples

### Toolbar with spacer

@verbatim
```blade
<native:row class="w-full px-4 py-2" :align-items="1">
    <native:text class="text-xl font-bold">Title</native:text>
    <native:spacer />
    <native:icon name="search" :size="24" />
</native:row>
```
@endverbatim

### Evenly spaced items

@verbatim
```blade
<native:row class="w-full" :justify-content="5">
    <native:text>One</native:text>
    <native:text>Two</native:text>
    <native:text>Three</native:text>
</native:row>
```
@endverbatim

### Inline label and value

@verbatim
```blade
<native:row class="w-full" :justify-content="3" :align-items="1">
    <native:text class="text-base text-slate-500">Status</native:text>
    <native:row :gap="4" :align-items="1">
        <native:icon name="check" color="#22C55E" :size="16" />
        <native:text class="text-base font-semibold" color="#22C55E">Active</native:text>
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
