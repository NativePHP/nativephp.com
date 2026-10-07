---
title: Stack
order: 400
---

## Overview

An overlay container that layers its children on top of each other — like `ZStack` in SwiftUI or `Box` in Jetpack
Compose. The first child renders at the bottom; each subsequent child is placed on top.

Useful for badges, image overlays, floating labels, and layered UI effects.

@verbatim
```blade
<native:stack class="w-[200] h-[200] rounded-2xl">
    <native:column class="w-full h-full bg-theme-primary rounded-2xl" />
    <native:column class="w-full h-full items-center justify-center">
        <native:text class="text-xl font-bold text-theme-on-primary">Overlay Text</native:text>
    </native:column>
</native:stack>
```
@endverbatim

## Children

Accepts any EDGE elements as children. Children are rendered in order, with later children appearing on top of earlier
ones.

Every child is positioned **absolutely** and placed at its natural size — you don't need `relative` on the stack or
`absolute` on the children. By default each child is **centered** in the stack's bounds. Give a child `w-full` or
`h-full` to force it to fill the stack.

The stack itself **sizes to its largest child** when you don't give it explicit dimensions, so a small overlaid
element (a badge, a dot) won't shrink the stack.

### Anchoring children

Move a child away from the centre with two points — one on the stack, one on the child:

- **`anchor`** — the point on the **stack** the child hooks onto.
- **`origin`** — the point **on the child** that lands there.

Both default to `center` and accept `center`, the four edges (`top`, `right`, `bottom`, `left`) and the four corners
(`top-left` … `bottom-right`) — as an attribute or an `anchor-*` / `origin-*` class. The child's `origin` point is
placed exactly on the stack's `anchor` point, so a child can sit on — or hang off — an edge or corner. See
[Positioning](../the-basics/positioning#anchor-amp-origin) for the full model.

@verbatim
```blade
<native:stack class="w-[56] h-[56]">
    <native:image src="https://i.pravatar.cc/128?img=12" class="w-[56] h-[56] rounded-full" />
    {{-- The dot's centre (origin default) sits on the stack's top-right corner. --}}
    <native:column anchor="top-right"
        class="w-[14] h-[14] rounded-full bg-green-500 border-2 border-white" />
</native:stack>
```
@endverbatim

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

### Avatar with a corner status dot

@verbatim
```blade
<native:stack class="w-[56] h-[56]">
    <native:image src="https://i.pravatar.cc/128?img=12" class="w-[56] h-[56] rounded-full" />
    {{-- The dot's centre sits on the avatar's bottom-right corner. --}}
    <native:column anchor="bottom-right"
        class="w-[16] h-[16] bg-green-500 rounded-full border-2 border-white" />
</native:stack>
```
@endverbatim

Set `origin` too if you want a different part of the badge on the corner — e.g. `anchor="top-right"
origin="top-right"` tucks the badge fully *inside* the corner instead of straddling it.

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
    <native:image src="https://picsum.photos/seed/banner/800/500" class="w-full h-full" :fit="2" />
    <native:column class="w-full h-full justify-end p-4 bg-black/40">
        <native:text class="text-2xl font-bold text-white">Featured Article</native:text>
        <native:text class="text-base text-white">Read more about this topic</native:text>
    </native:column>
</native:stack>
```
@endverbatim

<aside>

Because a child's `origin` can extend past the stack's `anchor`, a child **can draw outside the stack** on both iOS
and Android — handy for badges that poke over a corner. Nothing clips by default; clipping only applies when the
stack (or an ancestor) has rounded corners or is a scroll view. See [Positioning](../the-basics/positioning#anchor-amp-origin).

</aside>

## Element

```php
use Native\Mobile\Edge\Elements\Stack;
use Native\Mobile\Edge\Elements\Image;
use Native\Mobile\Edge\Elements\Text;

Stack::make(
    Image::make('https://picsum.photos/seed/nativephp/400/300'),
    Text::make('Overlay'),
)->width(200)->height(200);
```

- `make(Element ...$children)` - Create a stack with children. Layout / style fluent methods are inherited from the
  base `Element` class — see [Layout & Styling](layout)
