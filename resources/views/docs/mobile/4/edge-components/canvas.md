---
title: Canvas
order: 160
---

## Overview

A drawing surface for [shape primitives](shapes). Behaves like a [`<native:column>`](column) for layout purposes —
children stack vertically by default. Use it as a semantic wrapper when grouping shapes.

@verbatim
```blade
<native:canvas :width="200" :height="200" class="p-4 bg-theme-surface-variant rounded-2xl">
    <native:rect :width="100" :height="100" class="bg-theme-primary rounded-lg" />
    <native:circle :width="50" :height="50" bg="#EF4444" />
</native:canvas>
```
@endverbatim

Shapes take their fill from a `bg` attribute or any `bg-*` class (including `bg-theme-*` tokens), and corner
rounding from `rounded-*` classes.

## Props

All [shared layout and style attributes](layout) are supported. There are no canvas-specific props.

## Children

Accepts any EDGE elements as children. Typically used with [shape primitives](shapes) (`<native:rect>`,
`<native:circle>`, `<native:line>`).

For overlay-style layering of shapes use a [`<native:stack>`](stack) instead — `<native:canvas>` arranges children
along the column main axis, which is rarely what you want for free-form drawing.

## Examples

### Mini bar chart

Shapes plus flex layout are enough for lightweight data graphics. A bottom-aligned row of rects with varying
heights makes a bar chart — vary the `opacity-*` class to get a tonal ramp from a single theme color:

@verbatim
```blade
<native:canvas class="p-4 bg-theme-surface-variant rounded-2xl">
    <native:row class="items-end justify-between h-40">
        <native:rect :width="28" :height="48" class="bg-theme-primary opacity-40 rounded-md" />
        <native:rect :width="28" :height="88" class="bg-theme-primary opacity-60 rounded-md" />
        <native:rect :width="28" :height="64" class="bg-theme-primary opacity-80 rounded-md" />
        <native:rect :width="28" :height="120" class="bg-theme-primary rounded-md" />
    </native:row>
</native:canvas>
```
@endverbatim

In a real app you'd generate the rects with `@foreach` over a public array property on your component and
compute each `:height` from the data point.

### Pulsing beacon

Transform attributes (`:scale`, `:rotate`, `:translate-x`, `:translate-y`) combine with `animate-loop` and
`:animate-duration` to produce continuous, auto-reversing animations. Layer an animated circle behind a static
one in a stack to get a pulsing status beacon:

@verbatim
```blade
<native:canvas class="h-40 w-full">
    <native:stack class="flex-1 items-center justify-center">
        <native:circle :width="96" :height="96" class="bg-theme-primary opacity-30" :scale="1.4" animate-loop :animate-duration="1200" />
        <native:circle :width="40" :height="40" class="bg-theme-primary" />
    </native:stack>
</native:canvas>
```
@endverbatim

The loop oscillates the outer circle between its resting state and the declared `:scale` (and `opacity-*`),
reversing each cycle. `:animate-duration` is in milliseconds; add `animate-easing` to change the curve.

### Concentric rings

Layering same-center circles of decreasing size in a stack gives a bullseye — again using opacity steps of one
theme color so it works in both light and dark mode:

@verbatim
```blade
<native:canvas class="h-48 w-full">
    <native:stack class="flex-1 items-center justify-center">
        <native:circle :width="160" :height="160" class="bg-theme-primary opacity-20" />
        <native:circle :width="112" :height="112" class="bg-theme-primary opacity-40" />
        <native:circle :width="64" :height="64" class="bg-theme-primary" />
    </native:stack>
</native:canvas>
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Canvas;
use Native\Mobile\Edge\Elements\Rect;

Canvas::make(
    Rect::make()->width(100)->height(100)->bg('#3B82F6'),
);
```

- `make(Element ...$children)` - Create a canvas with children
