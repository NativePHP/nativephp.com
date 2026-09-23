---
title: Gesture Area
order: 225
---

## Overview

Captures a vertical pan/drag gesture over its content and writes the translation to a bound
[shared value](../digging-deeper/gestures), so the drag can drive animation on the UI thread with no PHP round-trip.
Children render normally — gesture detection wraps the whole content frame.

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

- `pan-y` - A [`SharedValue`](../digging-deeper/gestures) that receives the vertical drag translation (required for
  the gesture to do anything). Bind it, then read it from animatable props (`translate-y`, `opacity`, `scale`, …)
  on the children.

<aside>

Per-frame drag values stay on the native side and drive the bound props on the UI thread — nothing round-trips
through PHP during the gesture. See [Gestures & Animation](../digging-deeper/gestures) for shared values and
interpolation formulas.

</aside>
