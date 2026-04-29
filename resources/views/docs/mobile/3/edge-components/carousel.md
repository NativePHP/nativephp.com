---
title: Carousel
order: 270
---

## Overview

A horizontal paging carousel. Each child is sized to `item-width` and laid out in a horizontally-scrolling lazy
stack with `item-spacing` between items.

@verbatim
```blade
<native:carousel :item-width="280" :item-spacing="12">
    @foreach($posts as $post)
        <native:column :height="200" bg="#F1F5F9" :border-radius="16" :padding="16">
            <native:text class="text-lg font-bold">{{ $post->title }}</native:text>
            <native:text class="text-sm text-slate-500">{{ $post->excerpt }}</native:text>
        </native:column>
    @endforeach
</native:carousel>
```
@endverbatim

## Props

- `item-width` - Width of each child in dp (optional, float, default: `200`)
- `item-spacing` - Spacing between items in dp (optional, float, default: `8`)
- `variant` - Reserved for future variants (optional, string)

## Children

Accepts any EDGE elements as children. Each child is clipped to a 16dp rounded rectangle by the renderer, so
border-radius styling on the child itself is optional.

<aside>

For more control over snapping, indicators, or vertical paging, use a [`<native:scroll-view>`](scroll-view)
wrapping a [`<native:row>`](row).

</aside>

## Examples

### Featured cards

@verbatim
```blade
<native:carousel :item-width="320" :item-spacing="16">
    @foreach($features as $feature)
        <native:column :height="180" bg="{{ $feature->color }}" :padding="20">
            <native:text class="text-2xl font-bold text-white">{{ $feature->title }}</native:text>
            <native:spacer />
            <native:text class="text-base text-white">{{ $feature->subtitle }}</native:text>
        </native:column>
    @endforeach
</native:carousel>
```
@endverbatim

### Avatar strip

@verbatim
```blade
<native:carousel :item-width="80" :item-spacing="8">
    @foreach($contacts as $contact)
        <native:column :height="100" center :gap="6">
            <native:image src="{{ $contact->avatar }}" :width="64" :height="64" :border-radius="32" :fit="2" />
            <native:text class="text-xs">{{ $contact->name }}</native:text>
        </native:column>
    @endforeach
</native:carousel>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Carousel;

Carousel::make($child1, $child2, $child3)
    ->itemWidth(280)
    ->itemSpacing(12);
```

- `make(Element ...$children)` - Create a carousel with children
- `itemWidth(float $width)` - Width per item
- `itemSpacing(float $spacing)` - Spacing between items
- `variant(string $variant)` - Variant identifier (reserved)
