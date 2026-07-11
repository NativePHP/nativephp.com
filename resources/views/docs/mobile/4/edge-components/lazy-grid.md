---
title: Lazy Grid
order: 255
---

## Overview

A self-scrolling grid that only materializes the cells currently in — or about to enter — the viewport. On iOS it
maps to SwiftUI `ScrollView { LazyVGrid }`; on Android to Compose `LazyVerticalGrid`. Both platforms build and lay
out just the visible rows, so a grid of thousands of cells paints instantly and stays smooth.

Reach for it in place of a [`<native:scroll-view>`](scroll-view) wrapping a manually-chunked
[`<native:row>`](row) grid whenever the cell count is large enough to be felt at parse or layout time — a good rule
of thumb is around 50 or more cells.

@verbatim
```blade
<native:lazy-grid :columns="4" :gap="12">
    @foreach($icons as $icon)
        <native:column class="items-center gap-1 p-3 rounded-lg">
            <native:icon :ios="$icon" :size="28" />
        </native:column>
    @endforeach
</native:lazy-grid>
```
@endverbatim

## Props

- `columns` - Number of equal-width tracks the cells flow across. When `horizontal` is set, this becomes the number
  of fixed rows instead (optional, int, default: `2`, minimum `1`)
- `gap` - Spacing in dp applied to **both** axes — between rows and between columns. Match the surrounding column's
  `gap-N` if you want flush alignment with neighbouring content (optional, float, default: `0`)
- `horizontal` - Flip the scroll orientation. Rows become the cross axis, `columns` is the number of fixed-height
  rows, and the grid scrolls horizontally (SwiftUI `LazyHGrid` / Compose `LazyHorizontalGrid`) (optional, boolean,
  default: `false`)

## Children

Each child becomes one grid cell. Cells fill their column width by default and size to their intrinsic height — wrap
a child in a [`<native:column>`](column) with explicit sizing if you need uniform cell heights.

<aside>

On Android, when a lazy grid sits inside another scrolling container (a scroll view or scrollable column), its main
axis is unbounded and Compose's lazy grids can't measure. In that case the renderer falls back to a non-lazy chunked
grid that wraps its content — the same visual result, without virtualization. Give the grid a bounded height (or let
it be the screen's scroll container) to keep lazy composition.

</aside>

## Examples

### Photo grid

@verbatim
```blade
<native:lazy-grid :columns="3" :gap="2">
    @foreach($photos as $photo)
        <native:column :height="120">
            <native:image src="{{ $photo->url }}" class="w-full h-full" :fit="2" />
        </native:column>
    @endforeach
</native:lazy-grid>
```
@endverbatim

### Horizontal category shelves

@verbatim
```blade
<native:lazy-grid :columns="2" :gap="12" horizontal>
    @foreach($products as $product)
        <native:column :width="160" class="p-3 gap-2 rounded-lg bg-theme-surface">
            <native:image src="{{ $product->image }}" :width="136" :height="100" :border-radius="8" :fit="2" />
            <native:text class="text-sm font-medium">{{ $product->name }}</native:text>
        </native:column>
    @endforeach
</native:lazy-grid>
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\LazyGrid;
use Native\Mobile\Edge\Elements\Column;
use Native\Mobile\Edge\Elements\Icon;

LazyGrid::make(
    Column::make(Icon::make(ios: 'star')),
    Column::make(Icon::make(ios: 'heart')),
)
    ->columns(4)
    ->gap(12);
```

- `make(Element ...$children)` - Create a lazy grid with children
- `columns(int $count)` - Number of tracks (clamped to a minimum of `1`)
- `gap(float $gap)` - Spacing applied to both axes
- `horizontal(bool $value = true)` - Scroll horizontally with fixed rows
