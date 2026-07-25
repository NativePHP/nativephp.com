---
title: Positioning
order: 190
---

## Overview

Most layouts use the flex engine — children flow inside their parent column or row. For overlays like floating
action buttons, badges that hang off the edge of an avatar, or a "skip" button pinned to a corner, the framework
also supports CSS-style absolute positioning, plus a two-point **anchor / origin** model for precise placement.

## Absolute positioning

Take an element out of flex flow and position it relative to its parent. You can use either Tailwind classes or
plain attributes — they are equivalent.

@verbatim
```blade
{{-- Tailwind classes --}}
<native:column class="absolute bottom-[20] right-[20]">
    {{-- Pinned to the bottom-right corner of the parent --}}
</native:column>

{{-- The same thing as attributes --}}
<native:column absolute :bottom="20" :right="20">
    ...
</native:column>
```
@endverbatim

| Class | Attribute | Effect |
|---|---|---|
| `absolute` | `absolute` / `position="absolute"` | Take this element out of flex flow and position it against its parent |
| `relative` | `relative` / `position="relative"` | Default; element flows normally |
| `top-[N]` | `:top="N"` | Inset from the parent's top edge (dp) |
| `right-[N]` | `:right="N"` | Inset from the parent's right edge (dp) |
| `bottom-[N]` | `:bottom="N"` | Inset from the parent's bottom edge (dp) |
| `left-[N]` | `:left="N"` | Inset from the parent's left edge (dp) |

When an explicit class form and its attribute are both present (e.g. `class="top-4"` and `:top="99"`), the class
form wins.

### Inset convention

When `right` is set and `left` is unset, the child anchors to the parent's right edge offset by that amount. Same
for `bottom`. This mirrors CSS `position: absolute` shorthand. If both `left` and `right` are set, the child
stretches between them; likewise for `top` and `bottom`.

## Anchor & origin

For precise overlay placement, an absolutely-positioned child can align **two points** — one on the parent and one
on itself — instead of pinning to an edge:

- **`anchor`** — the point on the **parent** the child hooks onto.
- **`origin`** — the point **on the child** that lands on that parent point.

Both default to `center`, and both accept the same nine values (or the `anchor-*` / `origin-*` class form):

```
top-left      top      top-right
left          center   right
bottom-left   bottom   bottom-right
```

(`top` is an alias for `top-center`, `left` for `center-left`, and so on.)

The child is placed so its `origin` point sits exactly on the parent's `anchor` point:

@verbatim
```blade
{{-- The dot's CENTRE (origin defaults to center) sits on the parent's top-right
     corner — so it straddles the corner, half inside and half outside. --}}
<native:column class="relative w-[56] h-[56]">
    <native:image src="https://i.pravatar.cc/128?img=12" class="w-[56] h-[56] rounded-full" />
    <native:column anchor="top-right"
        class="absolute w-[14] h-[14] rounded-full bg-green-500 border-2 border-white" />
</native:column>

{{-- Give the child its own origin to change which part of it lands on the anchor.
     Here the badge's bottom-left corner hooks the parent's top-right corner. --}}
<native:column absolute anchor="top-right" origin="bottom-left" class="...">
    ...
</native:column>
```
@endverbatim

`anchor` and `origin` apply to any absolutely-positioned child. Insets (`top`/`right`/`bottom`/`left`) still apply
on top as a nudge from the anchored position. A plain `absolute top-0 right-0` child with no `anchor`/`origin`
keeps the simple edge-inset behaviour described above.

<aside>

Because the child's `origin` can extend past the parent's `anchor`, a child **can draw outside its container** — on
both iOS and Android. Nothing clips by default; clipping only kicks in when the container (or an ancestor) has
rounded corners or is a scroll view. For a badge that pokes out of a `rounded-full` avatar, keep the badge a
**sibling** of the avatar (inside a non-rounded parent), not a child of the rounded element. Touch targets that fall
outside the parent's bounds may not receive taps, so keep interactive overflow modest.

</aside>

## Common pattern: floating action button

A FAB pinned to the bottom-right of a screen:

@verbatim
```blade
<native:column class="w-full h-full bg-[#f7f9fb]">
    <native:scroll-view class="w-full flex-1">
        {{-- main content --}}
    </native:scroll-view>

    <native:column @press="newMessage"
        class="absolute bottom-[20] right-[20] w-[56] h-[56] rounded-2xl bg-[#00677d] items-center justify-center">
        <native:icon name="plus.message.fill" :size="24" color="#FFFFFF" />
    </native:column>
</native:column>
```
@endverbatim

Absolute children only occupy their placed bounds — siblings receive scroll and touch events normally.

<aside>

A [`<native:stack>`](../edge-components/stack) positions **all** of its children absolutely with `anchor` / `origin`
(both defaulting to `center`), so it's the most convenient parent for overlays — you don't need `relative` on the
parent or `absolute` on the children. Use a `<native:row>` / `<native:column>` parent when you want a single
overlay on top of normal flowing content instead.

</aside>
