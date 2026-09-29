---
title: Shapes
order: 360
---

## Overview

Shape primitives for drawing simple geometric forms. Three primitives are available:

- `<native:rect>` — rectangle
- `<native:circle>` — circle
- `<native:line>` — horizontal rule

These are typically placed inside a [`<native:canvas>`](canvas) or used standalone for decorative accents.

## Rect

A rectangle filled with `bg`. All [shared layout and style attributes](layout) apply — use Tailwind-style classes
(`rounded-*`, `border-*`, `opacity-*`, `shadow-*`) for radius, borders, opacity, and elevation, just as on any
other element.

@verbatim
```blade
<native:rect :width="120" :height="80" bg="#7C3AED" class="rounded-xl" />
```
@endverbatim

### Props

The `left` / `top` position props are accepted by the PHP element but are not currently read by the iOS or Android
renderers. To offset a rect inside a parent, use absolute-positioning classes instead — e.g.
`class="absolute top-[8] left-[8]"`.

`<native:rect>` is a self-closing element, so it doesn't accept tag children. To layer content on top of the fill,
overlay the rect and your content inside a [`<native:stack>`](stack). In PHP, the `Rect` element accepts children via
`addChild()` if you'd rather build the layering fluently.

## Circle

A circle filled with `bg`. Defaults to `border-radius: 9999` so any square-ish frame appears as a circle. For a
perfect circle use equal `width` and `height`.

@verbatim
```blade
<native:circle :width="64" :height="64" bg="#22C55E" />
```
@endverbatim

### Props

As with `<native:rect>`, the `left` / `top` position props exist on the PHP element but the renderers don't read
them — position circles with absolute-positioning classes (`class="absolute top-[8] left-[8]"`) or by centering
them in a [`<native:stack>`](stack).

`<native:circle>` is a self-closing element. It does not accept children.

## Line

A horizontal rule. Style it with `border-*` classes:

@verbatim
```blade static
<native:line class="border-2 border-theme-outline" />
```
@endverbatim

### Props

All [shared layout and style attributes](layout) are supported. Set the stroke through classes:

- `border-theme-outline` / `border-[#94A3B8]` - Line color (default: platform separator color)
- `border` / `border-2` / `border-4` - Stroke thickness in dp (default: `1`)

<aside>

On Android, `<native:line>` paints a centered horizontal stroke across the full width of its frame. On iOS the
current renderer draws a fixed 100pt stroke pinned to the top of the frame, so it does not reliably span the
available width — prefer `<native:divider class="border-theme-outline" />` for a full-width rule. `from`/`to`
coordinates are accepted on the PHP element but neither renderer reads them.

</aside>

## Examples

### Status dot

@verbatim
```blade
<native:circle :width="12" :height="12" bg="#22C55E" />
```
@endverbatim

### Colored badge background

Overlay the label on a filled rect with a `<native:stack>`. Give the rect an explicit frame and center the text on top:

@verbatim
```blade
<native:stack class="items-center justify-center">
    <native:rect :width="52" :height="24" bg="#DBEAFE" class="rounded-xl" />
    <native:text :font-size="12" :font-weight="5" color="#2563EB">New</native:text>
</native:stack>
```
@endverbatim

### Decorative separator

@verbatim
```blade static
<native:line class="border border-theme-outline" />
```
@endverbatim

For a full-width rule that renders consistently on both platforms today, use a divider instead:

@verbatim
```blade
<native:divider class="border-theme-outline" />
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
