---
title: Shapes
order: 600
---

## Overview

Shape primitives for drawing simple geometric forms. Three primitives are available:

- `<native:rect>` — rectangle
- `<native:circle>` — circle
- `<native:line>` — horizontal rule

These are typically placed inside a [`<native:canvas>`](canvas) or used standalone for decorative accents.

## Rect

A rectangle filled with `bg`. All [shared layout and style attributes](layout) apply, so border, radius, opacity,
and elevation behave as on any other element.

@verbatim
```blade
<native:rect :width="120" :height="80" bg="#7C3AED" :border-radius="12" />
```
@endverbatim

### Props

- `left` - X position offset in dp (optional, float). Used inside an absolutely-positioned parent
- `top` - Y position offset in dp (optional, float)

`<native:rect>` may optionally wrap children if you need to layer content on top of the fill.

## Circle

A circle filled with `bg`. Defaults to `border-radius: 9999` so any square-ish frame appears as a circle. For a
perfect circle use equal `width` and `height`.

@verbatim
```blade
<native:circle :width="64" :height="64" bg="#22C55E" />
```
@endverbatim

### Props

- `left` - X position offset in dp (optional, float)
- `top` - Y position offset in dp (optional, float)

`<native:circle>` is a self-closing element. It does not accept children.

## Line

A 1pt horizontal rule across the available width.

@verbatim
```blade
<native:line :border-width="2" border-color="#94A3B8" />
```
@endverbatim

### Props

All [shared layout and style attributes](layout) are supported. The most useful:

- `border-color` - Line color as hex string (optional, default: platform separator color)
- `border-width` - Stroke thickness in dp (optional, float, default: `1`)

<aside>

`<native:line>` always paints a centered horizontal stroke across its frame — `from`/`to` coordinates are accepted
on the PHP element but the iOS and Android renderers don't read them. To position a line, control the parent
container's frame or use a styled `<native:divider>`.

</aside>

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
<native:line :border-width="1" border-color="#E2E8F0" />
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Rect;
use Native\Mobile\Edge\Elements\Circle;
use Native\Mobile\Edge\Elements\Line;

Rect::make();
Circle::make();
Line::make();
```

All three expose `make()` and inherit the standard layout / style fluent API.
