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
- `snap-x` / `snap-y` <x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" /> - The points a bound pan settles on when the finger lifts
  (optional), as an array of numbers or a comma-separated string. Give at least two. See
  [Swipe to reveal](#swipe-to-reveal).
- `swipe-fingers` - How many fingers a `@swipe` needs (optional, integer, default `1`).

<aside>

Per-frame drag values stay on the native side and drive the bound props on the UI thread — nothing round-trips
through PHP during the gesture. See [Gestures & Animation](../digging-deeper/gestures) for shared values and
interpolation formulas.

</aside>

## Events

- `@dragEnd` <x-docs.version-badge since="4.5" /> <x-docs.version-badge package="mobile-ui" since="0.5" /> - Component method to call when the finger lifts after a drag.
  It gets the final translation on each axis as two floats, `x` then `y`. A bound axis reports its shared value,
  which with snap points is the point it settled on. An unbound axis reports how far the finger moved.
- `@pinchEnd` - Component method to call when the fingers lift after a pinch, with the final scale as a float.
- `@swipe` - Component method to call on a quick swipe, with its direction as a string: `left`, `right`, `up` or
  `down`.

## Scrolling and swipe back

<x-docs.version-badge package="mobile-ui" changed="0.9" />

A gesture area shares its touches with the scroll view around it and with the system's back gesture:

- A pan bound on one axis only takes drags along that axis, so a list of rows that each pan sideways still scrolls
  up and down. On iOS, before mobile-ui 0.9, a one-axis pan took every touch from the moment the finger went down.
- On iOS 26, which lets you swipe back from anywhere on a screen, a sideways drag that starts on a `pan-x` area or a
  `@swipe` area moves the area and leaves the screen where it is. A swipe in from the left edge of the screen still
  goes back.
- A swipe across a pressable inside the area, or one around it, doesn't press it. On Android, before mobile-ui 0.9,
  it pressed it as well.

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

## Swipe to reveal

<x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" />

With `snap-x` (or `snap-y`), a bound pan settles on set points, the way a row in a mail app slides aside to show its
actions:

- Between the lowest and highest point, the content follows the finger. Past them it moves a third as far as the
  finger does.
- When the finger lifts, the content springs to the nearest point. A flick counts by where it was heading, so a quick
  flick opens a row the finger only moved part of the way.
- `@dragEnd` gets the point it settled on.

Here a card slides left to show a Reply button beside it. The button is there the whole time, shrunk to nothing, and
grows in as the card moves:

@verbatim
```blade static
@php $shown = $offset->interpolate([-48, 0], [1, 0]); @endphp

<native:gesture-area :pan-x="$offset" :snap-x="[-48, 0]" @dragEnd="settled" class="w-full">
    <native:row :translate-x="$offset" class="w-full gap-2 items-center">
        <native:column class="flex-shrink px-4 py-3 rounded-2xl bg-theme-surface">
            <native:text>Swipe me left</native:text>
        </native:column>

        @if ($open)
            <native:pressable @press="reply" :scale="$shown" :opacity="$shown" a11y-label="Reply">
                <native:icon ios="arrowshape.turn.up.left" android="reply" :size="20" class="text-theme-primary" />
            </native:pressable>
        @else
            <native:stack :scale="$shown" :opacity="$shown">
                <native:icon ios="arrowshape.turn.up.left" android="reply" :size="20" class="text-theme-primary" />
            </native:stack>
        @endif
    </native:row>
</native:gesture-area>
```
@endverbatim

The shared value lives on the component, so the card stays where it settled when the screen renders again. A fresh
one each render would put it back at rest every time.

PHP doesn't hear about the drag frame by frame, so `settled()` records where the card came to rest with `setValue()`.
Setting a snapping pan's value from PHP springs the content there, so closing the card is one more `setValue()`:

```php
use Native\Mobile\Edge\SharedValue;

public ?SharedValue $offset = null;

public bool $open = false;

public function mount(): void
{
    $this->offset = SharedValue::make();
}

public function settled(float $x, float $y): void
{
    $this->offset->setValue($x);
    $this->open = $x < 0;
}

public function reply(): void
{
    $this->offset->setValue(0);
    $this->open = false;

    // ...
}
```

<aside>

A button shrunk or faded to nothing still takes taps where it sits. That's why the example only makes Reply pressable
once the card has settled open. Otherwise a tap beside a closed card would reply.

</aside>
