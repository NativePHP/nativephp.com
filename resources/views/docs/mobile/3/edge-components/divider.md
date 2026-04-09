---
title: Divider
order: 340
---

## Overview

A horizontal line separator used to visually divide sections of content. Renders as a thin native divider line.

@verbatim
```blade
<native:divider />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported. The most useful for dividers:

- `bg` - Line color as hex string (optional, default: platform default separator color)
- `opacity` - Line opacity from 0.0 to 1.0 (optional)
- `margin` - Spacing around the divider (optional)

<aside>

`<native:divider />` is a self-closing element. It does not accept children.

</aside>

## Examples

### Basic separator

@verbatim
```blade
<native:column class="w-full gap-4 p-4">
    <native:text class="text-lg font-bold">Section One</native:text>
    <native:text>Some content here.</native:text>
    <native:divider />
    <native:text class="text-lg font-bold">Section Two</native:text>
    <native:text>More content here.</native:text>
</native:column>
```
@endverbatim

### Colored divider with margin

@verbatim
```blade
<native:divider bg="#E2E8F0" :margin="[8, 16]" />
```
@endverbatim

### In a list

@verbatim
```blade
<native:column class="w-full">
    @foreach($items as $item)
        <native:column class="w-full p-4">
            <native:text class="text-base">{{ $item->name }}</native:text>
        </native:column>
        @unless($loop->last)
            <native:divider />
        @endunless
    @endforeach
</native:column>
```
@endverbatim
