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
- `zoom` <x-docs.version-badge since="4.9" /> - A `SharedValue` that receives the zoom scale, where `1` is the content's whole size. A pinch
  drives it, and so does a [double tap](#double-tap-to-zoom). Read it from `scale` on the children. Before 4.9 the
  zoom attributes were named `pinch`, `pinch-min`, `pinch-max` and `@pinchEnd`, and those names still work.
- `zoom-min` / `zoom-max` <x-docs.version-badge since="4.9" /> - The smallest and largest the scale can go (optional, float). The limit is applied
  as the fingers move, so the scale never runs past it and a pinch that changes direction responds straight away.
- `bounded` <x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" /> - Keep zoomed content on screen, and leave drags it can't use to
  whatever is around the area (optional, boolean). See [Zoomable images](#zoomable-images).
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
- `@zoomEnd` <x-docs.version-badge since="4.9" /> - Component method to call when a zoom ends, with the final scale as a float. That's when the
  fingers lift after a pinch, or when a [double tap's](#double-tap-to-zoom) zoom lands.
- `@doubleTap.zoom` <x-docs.version-badge since="4.9" /> <x-docs.version-badge package="mobile-ui" since="0.11" /> - The scale a double tap zooms to (float). It isn't a component
  method. See [Double tap to zoom](#double-tap-to-zoom).
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
- A `bounded` area leaves a one-finger drag to whatever is around it, such as a pager or a scroll view, unless its
  zoomed content has room to move that way. See [Who gets a drag](#who-gets-a-drag).

## Zoomable images

<x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" />

With `bounded`, the gesture area keeps zoomed content inside its own edges, the way a photo viewer does:

- Zoomed in, the content pans until its edges meet the area's edges, and no further.
- On an axis where the zoomed content still fits, such as the height of a wide photo, it stays centered. At its whole
  size it fits both ways, so it doesn't move at all.
- A drag past an edge moves with resistance, and the content springs back into place when the finger lifts. Inside
  a pager or a scroll view, the rest of the drag [pages or scrolls instead](#who-gets-a-drag).
- A pinch zooms around the point between the fingers, and two fingers pan as they pinch.
- Let go of a drag and the content carries on at the finger's speed, slowing to a stop, and bounces off an edge it
  reaches. A finger held still before it lifts doesn't glide. `@dragEnd` is called as the finger lifts, with where
  the content will come to rest. <x-docs.version-badge package="mobile-ui" since="0.11" />
- The area clips its content to its own frame, so zoomed content never draws over the elements around it, or over
  the next page in a pager. <x-docs.version-badge package="mobile-ui" since="0.11" />

Bind both pan axes and the zoom, bind the content's transforms to the same values, and pass the image's shape as
`content-ratio`:

@verbatim
```blade static
<native:gesture-area
    :zoom="$zoom" zoom-min="1" zoom-max="4"
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

The shared values live on the component. Making fresh ones puts the image back at its whole size, in the middle:

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

### Double tap to zoom

<x-docs.version-badge since="4.9" /> <x-docs.version-badge package="mobile-ui" since="0.11" />

Add `@doubleTap.zoom` to the area, with the scale a double tap zooms to:

@verbatim
```blade static
<native:gesture-area
    :zoom="$zoom" zoom-min="1" zoom-max="4" @doubleTap.zoom="2"
    :pan-x="$panX" :pan-y="$panY"
    bounded :content-ratio="$width / $height"
    class="w-full flex-1">
```
@endverbatim

A double tap at whole size zooms to that scale and brings the tapped point to the middle, as far as `bounded` lets it
go. A double tap on zoomed content takes it back to whole size, in the middle. Both ways animate over about a third
of a second, and `@zoomEnd` is called with the new scale once the zoom lands, the same as after a pinch.

It needs `:zoom` bound. Like a pinch, it aims by moving the bound pans, so bind `pan-x` and `pan-y` as well.

An `@event.modifier="value"` attribute isn't a PHP handler the way `@event="method"` is. Its value goes straight to
the phone, so `@doubleTap.zoom="2"` tells the area to zoom to a scale of 2 on a double tap, and the phone does that
without calling PHP. The value can be echoed, as in `@doubleTap.zoom="@{{ $level }}"`. A scale of `1` or less leaves
the double tap off.

### Who gets a drag

<x-docs.version-badge package="mobile-ui" changed="0.11" />

At whole size, a bounded area leaves one-finger drags to whatever is around it: a pager pages, a scroll view scrolls,
and the pan of a gesture area around it moves, such as a drag to close. Zoomed in, it takes a drag while the content
has room to move the way the finger is going, so the same drag that paged now pans the photo. Two fingers down are
always the area's, for a pinch.

Zoomed content at an edge leaves a drag that way to a pager or scroll view around it, so a zoomed photo at its right
edge pages on a swipe left. It keeps the drag from a gesture area's pan, though. A drag to close only works at whole
size, so panning a zoomed photo up to its top edge doesn't start closing the viewer.

Room to move means more than 2 points, and a zoom that ends a hair past 1x counts as whole size. Keep both pans
bound all the time. There's no need to unbind them, or to change the markup, when the zoom changes.

When zoomed content reaches its edge part way through a drag and the finger keeps going, the rest of the drag moves
the pager or scroll view around it. A pager slides on to the next page, the way the Photos app does. With nothing
around to take the drag, the content moves past the edge with resistance and springs back when the finger lifts.

Before mobile-ui 0.11, a bounded area with its pans bound took every one-finger drag, so a pager around it couldn't
page.

### A photo viewer

<x-docs.version-badge since="4.9" /> <x-docs.version-badge package="mobile-ui" since="0.11" />

A horizontal [pager](pager) of photos, each one zoomable on its own page:

@verbatim
```blade static
<native:pager horizontal report-on-settle :page="$index" on-page-change="paged" class="w-full h-full">
    @foreach ($photos as $i => $photo)
        <native:gesture-area
            :native:key="'photo-'.$i"
            :zoom="$zooms[$i]" zoom-min="1" zoom-max="4" @doubleTap.zoom="2"
            :pan-x="$pansX[$i]" :pan-y="$pansY[$i]"
            bounded :content-ratio="$photo['width'] / $photo['height']"
            class="w-full h-full">
            <native:image
                :src="$photo['url']"
                fit="contain"
                :scale="$zooms[$i]"
                :translate-x="$pansX[$i]"
                :translate-y="$pansY[$i]"
                class="w-full h-full"
            />
        </native:gesture-area>
    @endforeach
</native:pager>
```
@endverbatim

At whole size a swipe sideways pages. Zoomed in, it pans the photo, and once the photo's edge is reached the same
swipe carries on into the next page. A drag down to close goes on a gesture area around the pager, with its own
`pan-y`, and the photos leave that drag to it while they're at whole size.

Each photo has its own three shared values, kept on the component by its place in the list. `paged()` gives the
photo you left fresh ones, so it's back at whole size when you come back to it. With `report-on-settle`, `paged()`
is only called once the swipe has come to rest, so the photo doesn't jump back to whole size while it's still
sliding off screen:

```php
use Native\Mobile\Edge\SharedValue;

public array $photos = []; // each one ['url' => ..., 'width' => ..., 'height' => ...]

public int $index = 0;

public array $zooms = [];
public array $pansX = [];
public array $pansY = [];

public function mount(): void
{
    // Load $this->photos, then give each photo its shared values.

    foreach (array_keys($this->photos) as $i) {
        $this->rest($i);
    }
}

public function paged(int $index): void
{
    $this->rest($this->index);
    $this->index = $index;
}

private function rest(int $i): void
{
    $this->zooms[$i] = SharedValue::make(1.0);
    $this->pansX[$i] = SharedValue::make();
    $this->pansY[$i] = SharedValue::make();
}
```

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
