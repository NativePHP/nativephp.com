---
title: Column
order: 200
---

## Overview

A vertical flex container that stacks its children from top to bottom. This is the most commonly used layout element
and serves as the foundation for most screen layouts.

@verbatim
```blade
<native:column :padding="16" :gap="12" fill>
    <native:text>First item</native:text>
    <native:text>Second item</native:text>
    <native:text>Third item</native:text>
</native:column>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported. The most commonly used with columns:

- `gap` - Vertical space between children in dp (optional, float)
- `align-items` - Horizontal alignment of children: `0`=start, `1`=center, `2`=end, `3`=stretch (optional, default: `0`)
- `justify-content` - Vertical distribution: `0`=start, `1`=center, `2`=end, `3`=space-between, `4`=space-around, `5`=space-evenly (optional, default: `0`)
- `safe-area` - Respect device safe area insets (optional, boolean)
- `padding` - Inner spacing, single value or array (optional)
- `bg` - Background color as hex (optional)

## Children

Accepts any EDGE elements as children. Children are arranged vertically from top to bottom.

## Examples

### Full-screen layout with safe area

@verbatim
```blade
<native:column fill safe-area bg="#FFFFFF">
    <native:text class="text-2xl font-bold">My App</native:text>
    <native:spacer />
    <native:button label="Get Started" @press="start" />
</native:column>
```
@endverbatim

### Centered content

@verbatim
```blade
<native:column fill center>
    <native:activity-indicator />
    <native:text>Loading...</native:text>
</native:column>
```
@endverbatim

### Card-style layout

@verbatim
```blade
<native:column
    class="w-full p-4 rounded-2xl gap-3"
    bg="#FFFFFF"
    :border-width="1"
    border-color="#E2E8F0"
>
    <native:text class="text-lg font-bold">Card Title</native:text>
    <native:text class="text-base text-slate-500">Card description goes here.</native:text>
    <native:row :gap="8" :justify-content="2">
        <native:button label="Cancel" @press="cancel" />
        <native:button label="Confirm" @press="confirm" color="#7C3AED" label-color="#FFFFFF" />
    </native:row>
</native:column>
```
@endverbatim

### Space-between distribution

@verbatim
```blade
<native:column fill :justify-content="3" :padding="16">
    <native:text>Top</native:text>
    <native:text>Middle</native:text>
    <native:text>Bottom</native:text>
</native:column>
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Column;
use Native\Mobile\Edge\Elements\Text;

Column::make(
    Text::make('First'),
    Text::make('Second'),
)->fill()->padding(16)->gap(12);
```

- `make(Element ...$children)` - Create a column with children. Layout / style fluent methods are inherited from
  the base `Element` class — see [Layout & Styling](layout)
