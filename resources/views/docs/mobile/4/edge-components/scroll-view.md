---
title: Scroll View
order: 340
---

## Overview

A scrollable container for content that exceeds the available screen space. By default it scrolls vertically, but can
be configured for horizontal or two-axis (2D pan) scrolling. On the native side, this uses `LazyColumn`/`LazyRow` on
Android and `ScrollView` on iOS for efficient rendering.

@verbatim
```blade
<native:scroll-view class="w-full h-[240] rounded-xl border border-theme-outline">
    <native:column class="w-full p-4 gap-3">
        @foreach (range(1, 20) as $i)
            <native:text class="text-theme-on-surface-variant">Scrollable row {{ $i }}</native:text>
        @endforeach
    </native:column>
</native:scroll-view>
```
@endverbatim

The fixed `h-[240]` here just gives the preview a bounded viewport so the content overflows and scrolls. On a real
screen you'd typically let the scroll view fill the page instead:

@verbatim
```blade static
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

- `axis` - Scroll direction: `vertical`, `horizontal`, or `both` (optional, default: `vertical`). `both` enables 2D
  panning for content that's larger than the viewport in both dimensions — give the inner content explicit dimensions
  larger than the viewport for there to be anything to pan to
- `horizontal` - Scroll horizontally instead of vertically (optional, boolean, default: `false`). `axis="horizontal"` is
  the modern equivalent
- `shows-indicators` - Show scroll indicators (optional, boolean, default: `true`) [iOS]
- `scroll-anchor` - Set to `bottom` for chat-style behavior: the view opens at the latest item and auto-scrolls on new
  content while the user is near the bottom (optional)

<aside>

Android `LazyRow` / `LazyColumn` don't render scrollbars by default, so `shows-indicators` is effectively iOS-only.
The prop is silently accepted on Android.

</aside>

## Children

Accepts any EDGE elements as children. Typically wraps a single `<native:column>` or `<native:row>` that contains the
scrollable content.

## Examples

### Vertical list

A full-screen list of posts. `safe-area` keeps the content clear of the notch and home indicator, so this one is
meant for a real page root — run it in your app to see it edge-to-edge:

@verbatim
```blade static
<native:scroll-view fill class="bg-theme-background">
    <native:column class="w-full gap-3 p-4 safe-area">
        @foreach($posts as $post)
            <native:column class="w-full p-4 gap-2 bg-theme-surface rounded-xl">
                <native:text class="text-lg font-semibold">{{ $post->title }}</native:text>
                <native:text class="text-base text-theme-on-surface-variant">{{ $post->excerpt }}</native:text>
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
                class="bg-theme-surface-variant rounded-xl"
            >
                <native:text class="text-sm font-medium text-theme-on-surface-variant">{{ $category->name }}</native:text>
            </native:column>
        @endforeach
    </native:row>
</native:scroll-view>
```
@endverbatim

### Full-page scrollable layout

Another page-root pattern — `h-full` and `safe-area` only make sense against a real screen, so try this one in your
app:

@verbatim
```blade static
<native:scroll-view class="w-full h-full bg-theme-background">
    <native:column class="w-full gap-4 safe-area p-4">
        <native:text class="text-3xl font-bold">Welcome</native:text>
        <native:text class="text-base text-theme-on-surface-variant">
            Scroll down to see more content.
        </native:text>
        {{-- Long content here --}}
    </native:column>
</native:scroll-view>
```
@endverbatim

### Chat-style stick-to-bottom

The view opens at the latest message and stays pinned to the bottom as new content arrives. The fixed `h-[200]`
bounds the preview so there's something to scroll — in a real chat screen you'd use `fill` instead:

@verbatim
```blade
<native:scroll-view scroll-anchor="bottom" class="w-full h-[100] rounded-xl border border-theme-outline">
    <native:column class="w-full gap-2 p-4">
        @foreach($messages as $message)
            <native:text class="text-base text-theme-on-surface-variant">{{ $message->body }}</native:text>
        @endforeach
    </native:column>
</native:scroll-view>
```
@endverbatim

<aside>

When using vertical scrolling, make sure to set width to fill on both the scroll view and its child column so content
stretches across the full screen width.

</aside>

## Element

```php
use Native\Mobile\Edge\Elements\ScrollView;

ScrollView::make()
    ->horizontal()
    ->both()
    ->showsIndicators(false)
    ->autoScrollTo(0);
```

- `make(Element ...$children)` - Create a scroll view with children
- `horizontal(bool $value = true)` - Scroll horizontally instead of vertically
- `both()` - Enable 2D panning on both axes
- `showsIndicators(bool $value = true)` - Toggle scroll indicators [iOS]
- `autoScrollTo(int $index)` - Programmatically scroll to the child at `$index`
