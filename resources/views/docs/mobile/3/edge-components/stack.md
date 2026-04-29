---
title: Stack
order: 230
---

## Overview

An overlay container that layers its children on top of each other (similar to `Box` in Jetpack Compose). The first
child is rendered at the bottom, and each subsequent child is placed on top.

This is useful for badges, image overlays, floating labels, and layered UI effects.

@verbatim
```blade
<native:stack :width="200" :height="200">
    <native:image src="https://example.com/photo.jpg" fill />
    <native:column fill center>
        <native:text class="text-xl font-bold text-white">Overlay Text</native:text>
    </native:column>
</native:stack>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported. There are no stack-specific props.

## Children

Accepts any EDGE elements as children. Children are rendered in order, with later children appearing on top of
earlier ones.

By default, each child is **placed at its natural size and centered** in the stack's bounds. To force a child to
fill the stack, give it `class="w-full"`, `class="h-full"`, or `fill` — same semantics as the rest of the layout
engine.

<aside>

The iOS implementation uses a custom SwiftUI `Layout` (not `ZStack`) so that mixed-size children sit at their
natural size centered in the stack's bounds. An earlier "icon pinned to leading edge" bug — visible when
`<native:stack>` was placed inside a `flex-1 items-center` cell — is fixed in the current renderer.

`<native:stack>` does not currently honor `position-type: absolute` for its children. Use a
[positioned](../the-basics/positioning) `<native:row>` or `<native:column>` parent instead if you need absolute
placement.

</aside>

## Examples

### Avatar with centered badge

A small status circle centered over an avatar — the natural pairing for `<native:stack>`'s default centering:

@verbatim
```blade
<native:stack :width="56" :height="56">
    <native:image
        src="https://example.com/avatar.jpg"
        :width="56"
        :height="56"
        :border-radius="28"
    />
    <native:column
        :width="20"
        :height="20"
        bg="#22C55E"
        :border-radius="10"
        :border-width="2"
        border-color="#FFFFFF"
    />
</native:stack>
```
@endverbatim

### Badge on an icon

@verbatim
```blade
<native:stack :width="40" :height="40">
    <native:icon name="notifications" :size="32" />
    <native:column
        :width="18"
        :height="18"
        bg="#EF4444"
        :border-radius="9"
        center
    >
        <native:text :font-size="10" color="#FFFFFF" :font-weight="6">3</native:text>
    </native:column>
</native:stack>
```
@endverbatim

<aside>

For a status dot anchored to a corner (rather than centered), use a `<native:column class="relative">` parent with a
`<native:column class="absolute bottom-[0] right-[0]">` child instead. See [Positioning](../the-basics/positioning).

</aside>

### Image with gradient overlay

@verbatim
```blade
<native:stack class="w-full" :height="250" :border-radius="16">
    <native:image src="https://example.com/banner.jpg" fill :fit="2" />
    <native:column class="w-full h-full" :justify-content="2" :padding="16">
        <native:text class="text-2xl font-bold text-white">Featured Article</native:text>
        <native:text class="text-base text-white">Read more about this topic</native:text>
    </native:column>
</native:stack>
```
@endverbatim
