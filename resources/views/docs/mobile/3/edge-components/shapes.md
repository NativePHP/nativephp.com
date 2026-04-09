---
title: Shapes
order: 600
---

## Overview

Shape elements are used for drawing basic geometric forms. They are typically placed inside a `<native:canvas>` or
used standalone for simple visual elements like colored backgrounds and decorative accents.

## Canvas

A container for shape elements. Provides a drawing surface for rects, circles, and lines.

@verbatim
```blade
<native:canvas :width="200" :height="200">
    <native:rect :width="100" :height="100" bg="#3B82F6" :border-radius="8" />
    <native:circle :width="50" :height="50" bg="#EF4444" />
</native:canvas>
```
@endverbatim

## Rect

A rectangle shape. Styled via the shared layout and style attributes.

@verbatim
```blade
<native:rect :width="120" :height="80" bg="#7C3AED" :border-radius="12" />
```
@endverbatim

### Props

All [shared layout and style attributes](layout) are supported, plus:

- `left` - X position offset in dp (optional, float)
- `top` - Y position offset in dp (optional, float)

## Circle

A circle shape. Automatically applies a large `border-radius` to create a circular form.

@verbatim
```blade
<native:circle :width="64" :height="64" bg="#22C55E" />
```
@endverbatim

### Props

All [shared layout and style attributes](layout) are supported, plus:

- `left` - X position offset in dp (optional, float)
- `top` - Y position offset in dp (optional, float)

<aside>

`<native:circle>` sets `border-radius` to `9999` by default, making any square element appear circular. Set equal
`width` and `height` for a perfect circle.

</aside>

## Line

A line drawn between two points. Defined by `from` and `to` coordinates.

@verbatim
```blade
<native:line from="0, 0" to="200, 100" :border-width="2" border-color="#94A3B8" />
```
@endverbatim

### Props

All [shared layout and style attributes](layout) are supported, plus:

- `from` - Start point as `"x, y"` string (optional)
- `to` - End point as `"x, y"` string (optional)

## Examples

### Status dot

@verbatim
```blade
<native:circle :width="12" :height="12" bg="#22C55E" />
```
@endverbatim

### Colored badge background

@verbatim
```blade
<native:rect :padding="[4, 12]" bg="#DBEAFE" :border-radius="12">
    <native:text :font-size="12" :font-weight="5" color="#2563EB">New</native:text>
</native:rect>
```
@endverbatim

### Decorative separator

@verbatim
```blade
<native:line from="0, 0" to="300, 0" :border-width="1" border-color="#E2E8F0" />
```
@endverbatim

<aside>

All shape elements (`<native:rect />`, `<native:circle />`, `<native:line />`) are self-closing by default and do
not accept children, except `<native:rect>` which can optionally wrap child elements.

</aside>
