---
title: Positioning
order: 190
---

## Overview

Most layouts use the flex engine — children flow inside their parent column or row. For overlays like floating
action buttons, badges that hang off the edge of an avatar, or a "skip" button pinned to a corner, the framework
also supports CSS-style absolute positioning.

## Tailwind classes

@verbatim
```blade
<native:column class="absolute bottom-[20] right-[20]">
    {{-- Pinned to the bottom-right corner of the parent --}}
</native:column>
```
@endverbatim

- `absolute` - Take this element out of flex flow and position it relative to the nearest positioned ancestor
- `relative` - Default; element flows normally
- `top-[N]` - Inset from the parent's top edge (dp)
- `right-[N]` - Inset from the parent's right edge (dp)
- `bottom-[N]` - Inset from the parent's bottom edge (dp)
- `left-[N]` - Inset from the parent's left edge (dp)

## Anchor convention

When `right-[N]` is set and `left-` is unset, the child anchors to the parent's right edge offset by N. Same for
`bottom-`. This mirrors CSS `position: absolute` shorthand.

If both `left-` and `right-` are set, the child stretches between them. Likewise for `top-` and `bottom-`.

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

`<native:stack>` does **not** currently honor `position-type: absolute` for its children — it uses its own custom
Layout to center children at their natural size. Use a `<native:row>` or `<native:column>` as the parent when you
need absolute positioning.

</aside>
