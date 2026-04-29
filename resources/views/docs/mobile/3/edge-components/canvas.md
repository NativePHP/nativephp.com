---
title: Canvas
order: 605
---

## Overview

A drawing surface for [shape primitives](shapes). Behaves like a [`<native:column>`](column) for layout purposes —
children stack vertically by default. Use it as a semantic wrapper when grouping shapes.

@verbatim
```blade
<native:canvas :width="200" :height="200">
    <native:rect :width="100" :height="100" bg="#3B82F6" :border-radius="8" />
    <native:circle :width="50" :height="50" bg="#EF4444" />
</native:canvas>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported. There are no canvas-specific props.

## Children

Accepts any EDGE elements as children. Typically used with [shape primitives](shapes) (`<native:rect>`,
`<native:circle>`, `<native:line>`).

For overlay-style layering of shapes use a [`<native:stack>`](stack) instead — `<native:canvas>` arranges children
along the column main axis, which is rarely what you want for free-form drawing.

## Element

```php
use Native\Mobile\Edge\Elements\Canvas;
use Native\Mobile\Edge\Elements\Rect;

Canvas::make(
    Rect::make()->width(100)->height(100)->bg('#3B82F6'),
);
```

- `make(Element ...$children)` - Create a canvas with children
