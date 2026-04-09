---
title: Scroll View
order: 220
---

## Overview

A scrollable container for content that exceeds the available screen space. By default it scrolls vertically, but can
be configured for horizontal scrolling. On the native side, this uses `LazyColumn`/`LazyRow` on Android and
`ScrollView` on iOS for efficient rendering.

@verbatim
```blade
<native:scroll-view fill>
    <native:column :padding="16" :gap="12">
        @foreach($items as $item)
            <native:text>{{ $item->name }}</native:text>
        @endforeach
    </native:column>
</native:scroll-view>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `horizontal` - Scroll horizontally instead of vertically (optional, boolean, default: `false`)
- `shows-indicators` - Show scroll indicators (optional, boolean, default: `true`)

## Children

Accepts any EDGE elements as children. Typically wraps a single `<native:column>` or `<native:row>` that contains the
scrollable content.

## Examples

### Vertical list

@verbatim
```blade
<native:scroll-view fill bg="#FFFFFF">
    <native:column class="w-full gap-0 safe-area">
        @foreach($posts as $post)
            <native:column class="w-full p-4 gap-2" :border-width="1" border-color="#F1F5F9">
                <native:text class="text-lg font-semibold">{{ $post->title }}</native:text>
                <native:text class="text-base text-slate-500">{{ $post->excerpt }}</native:text>
            </native:column>
        @endforeach
    </native:column>
</native:scroll-view>
```
@endverbatim

### Horizontal carousel

@verbatim
```blade
<native:scroll-view horizontal :shows-indicators="false">
    <native:row :gap="12" :padding="16">
        @foreach($categories as $category)
            <native:column
                :width="120"
                :height="80"
                center
                bg="#F1F5F9"
                :border-radius="12"
            >
                <native:text class="text-sm font-medium">{{ $category->name }}</native:text>
            </native:column>
        @endforeach
    </native:row>
</native:scroll-view>
```
@endverbatim

### Full-page scrollable layout

@verbatim
```blade
<native:scroll-view class="w-full h-full" bg="#FFFFFF">
    <native:column class="w-full gap-4 safe-area p-4">
        <native:text class="text-3xl font-bold">Welcome</native:text>
        <native:text class="text-base text-slate-500">
            Scroll down to see more content.
        </native:text>
        {{-- Long content here --}}
    </native:column>
</native:scroll-view>
```
@endverbatim

<aside>

When using vertical scrolling, make sure to set width to fill on both the scroll view and its child column so content
stretches across the full screen width.

</aside>
