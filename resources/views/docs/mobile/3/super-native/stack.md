---
title: Stack
order: 230
---

## Overview

An overlay container that layers its children on top of each other — like `ZStack` in SwiftUI or `Box` in Jetpack
Compose. The first child renders at the bottom; each subsequent child is placed on top.

Useful for badges, image overlays, floating labels, and layered UI effects.

@verbatim
```blade
<native:stack class="w-[200] h-[200]">
    <native:image src="https://example.com/photo.jpg" class="w-full h-full" />
    <native:column class="w-full h-full items-center justify-center">
        <native:text class="text-xl font-bold text-white">Overlay Text</native:text>
    </native:column>
</native:stack>
```
@endverbatim

## Children

Accepts any EDGE elements as children. Children are rendered in order, with later children appearing on top of earlier
ones.

Each child is **placed at its natural size and centered** in the stack's bounds. Give a child `w-full` or `h-full` to
force it to fill the stack.

## Supported Tailwind classes

Stack inherits the full class set documented at [Layout & Styling](layout#supported-tailwind-classes). The classes
that shape how a stack behaves specifically:

| Class | Effect on a stack |
|---|---|
| `w-*`, `h-*`, fractional, arbitrary `w-[N]` / `h-[N]` | Set the stack's own bounds — children center within it |
| `flex-1` | Fills remaining space in the parent flex container |
| `self-*` | This stack's alignment within its parent (`self-start`, `self-center`, `self-end`, `self-stretch`) |
| `absolute`, `relative`, `top-N`, `right-N`, `bottom-N`, `left-N` | Position the stack itself when its parent uses absolute layout |

Everything else from the shared list applies as on any element (`p-*`, `m-*`, `bg-*`, `rounded-*`, `border-*`,
`shadow-*`, `opacity-*`, `dark:*`, `ios:*` / `android:*`, `glass:*`, alpha suffix `/N`, arbitrary `prefix-[value]`).

## Examples

### Avatar with centered badge

@verbatim
```blade
<native:stack class="w-[56] h-[56]">
    <native:image src="https://example.com/avatar.jpg" class="w-[56] h-[56] rounded-full" />
    <native:column class="w-[20] h-[20] bg-green-500 rounded-full border-2 border-white" />
</native:stack>
```
@endverbatim

### Badge on an icon

@verbatim
```blade
<native:stack class="w-[40] h-[40]">
    <native:icon name="notifications" :size="32" />
    <native:column class="w-[18] h-[18] bg-red-500 rounded-full items-center justify-center">
        <native:text class="text-[10] font-bold text-white">3</native:text>
    </native:column>
</native:stack>
```
@endverbatim

### Image with bottom-aligned overlay

@verbatim
```blade
<native:stack class="w-full h-[250] rounded-2xl">
    <native:image src="https://example.com/banner.jpg" class="w-full h-full" :fit="2" />
    <native:column class="w-full h-full justify-end p-4">
        <native:text class="text-2xl font-bold text-white">Featured Article</native:text>
        <native:text class="text-base text-white">Read more about this topic</native:text>
    </native:column>
</native:stack>
```
@endverbatim

<aside>

For corner-anchored placement (e.g. a status dot pinned to the bottom-right of an avatar), wrap your content in a
`<native:column class="relative">` parent and use `<native:column class="absolute bottom-[0] right-[0]">` for the
anchored child. See [Positioning](../the-basics/positioning).

</aside>

## Element

```php
use Native\Mobile\Edge\Elements\Stack;
use Native\Mobile\Edge\Elements\Image;
use Native\Mobile\Edge\Elements\Text;

Stack::make(
    Image::make('https://example.com/photo.jpg'),
    Text::make('Overlay'),
)->width(200)->height(200);
```

- `make(Element ...$children)` - Create a stack with children. Layout / style fluent methods are inherited from the
  base `Element` class — see [Layout & Styling](layout)
