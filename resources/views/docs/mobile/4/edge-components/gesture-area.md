---
title: Gesture Area
order: 225
---

## Overview

Captures drag and pinch gestures over its content and writes them to bound
[shared values](../digging-deeper/gestures), so the gesture can drive animation on the UI thread with no PHP
round-trip. Children render normally — gesture detection wraps the whole content frame.

@verbatim
```blade static
@php $drag = \Native\Mobile\Edge\SharedValue::make(); @endphp

<native:gesture-area :pan-y="$drag">
    <native:column :translate-y="$drag" class="p-6 bg-theme-surface rounded-2xl">
        <native:text>Drag me</native:text>
    </native:column>
</native:gesture-area>
```
@endverbatim

This example needs a real app to try out: the drag runs entirely on the UI thread against a live
`SharedValue` bound from your component, so there's no inline preview here — drop the snippet into a
screen in your app and drag the card.

## Props

- `pan-y` - A [`SharedValue`](../digging-deeper/gestures) that receives the vertical drag translation. Bind it, then
  read it from animatable props (`translate-y`, `opacity`, `scale`, …) on the children.
- `pan-x` - The same for the horizontal drag translation. Bind one axis or both.
- `pinch` - A `SharedValue` that receives the pinch scale factor, where `1` is the content's own size. Read it from
  `scale` on the children.
- `pinch-min` / `pinch-max` - The smallest and largest the pinch scale can go (optional, float). The limit is applied
  as the fingers move, so the scale never runs past it and a pinch that changes direction responds straight away.
- `bounded` <x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" /> - Keep zoomed content on screen (optional, boolean). See
  [Zoomable images](#zoomable-images).
- `content-ratio` <x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" /> - The content's width over height, for `bounded` (optional, float).
  The content is taken to be that shape fitted inside the area, which is where an image with `fit="contain"` draws.
  Leave it out when the content fills the area.

<aside>

Per-frame drag values stay on the native side and drive the bound props on the UI thread — nothing round-trips
through PHP during the gesture. See [Gestures & Animation](../digging-deeper/gestures) for shared values and
interpolation formulas.

</aside>

## Zoomable images

<x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" />

With `bounded`, the gesture area keeps zoomed content inside its own edges, the way a photo viewer does:

- Zoomed in, the content pans until its edges meet the area's edges, and no further.
- On an axis where the zoomed content still fits, such as the height of a wide photo, it stays centered. At its own
  size it fits both ways, so it doesn't move at all.
- A drag past an edge moves with resistance, and the content springs back into place when the finger lifts.
- A pinch zooms around the point between the fingers, and two fingers pan as they pinch.

Bind both pan axes and the pinch, bind the content's transforms to the same values, and pass the image's shape as
`content-ratio`:

@verbatim
```blade static
<native:gesture-area
    :pinch="$zoom" pinch-min="1" pinch-max="4"
    :pan-x="$panX" :pan-y="$panY"
    bounded :content-ratio="$width / $height"
    class="w-full flex-1">
    <native:image
        :src="$url"
        fit="contain"
        :scale="$zoom"
        :translate-x="$panX"
        :translate-y="$panY"
        class="w-full h-full"
    />
</native:gesture-area>
```
@endverbatim

The shared values live on the component. Making fresh ones puts the image back at its own size, in the middle:

```php
use Native\Mobile\Edge\SharedValue;

public ?SharedValue $zoom = null;
public ?SharedValue $panX = null;
public ?SharedValue $panY = null;

public function showImage(): void
{
    $this->zoom = SharedValue::make(1.0);
    $this->panX = SharedValue::make();
    $this->panY = SharedValue::make();
}
```

<aside>

Without `content-ratio` the bounds are the area itself. That's right for content that fills the area, but an image
drawn smaller than the area (with bars above and below, or at the sides) could then be pulled partly off screen.
Pass its width over height so the bounds match the picture.

</aside>
