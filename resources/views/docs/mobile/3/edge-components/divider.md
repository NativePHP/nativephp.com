---
title: Divider
order: 340
---

## Overview

A thin horizontal line separator. Renders as a 1pt rule. Color resolves from `border-color` if set, otherwise the
platform separator color.

@verbatim
```blade
<native:divider />
```
@endverbatim

`<native:horizontal-divider />` is an alias of `<native:divider />` exposed for use inside [side
navigation](side-nav).

## Props

All [shared layout and style attributes](layout) are supported. The most useful for dividers:

- `border-color` - Line color as hex string (optional, default: platform separator color)
- `opacity` - Line opacity from 0.0 to 1.0 (optional)
- `margin` - Spacing around the divider (optional)

<aside>

`<native:divider />` is a self-closing element. It does not accept children.

The line is always 1pt high. To change thickness, use a styled `<native:column>` instead.

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
<native:divider border-color="#E2E8F0" :margin="[8, 16]" />
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

## Element

```php
use Native\Mobile\Edge\Elements\Divider;

Divider::make()->borderColor('#E2E8F0');
```

- `make()` - Create a divider
