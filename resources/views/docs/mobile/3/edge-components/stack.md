---
title: Stack
order: 230
---

## Overview

An overlay container that layers its children on top of each other (similar to `ZStack` in SwiftUI or `Box` in
Jetpack Compose). The first child is rendered at the bottom, and each subsequent child is placed on top.

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

## Examples

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
        :align-self="2"
    >
        <native:text :font-size="10" color="#FFFFFF" :font-weight="6">3</native:text>
    </native:column>
</native:stack>
```
@endverbatim

### Image with gradient overlay

@verbatim
```blade
<native:stack class="w-full" :height="250" :border-radius="16">
    <native:image src="https://example.com/banner.jpg" fill :fit="2" />
    <native:column fill :justify-content="2" :padding="16">
        <native:text class="text-2xl font-bold text-white">Featured Article</native:text>
        <native:text class="text-base text-white">Read more about this topic</native:text>
    </native:column>
</native:stack>
```
@endverbatim
