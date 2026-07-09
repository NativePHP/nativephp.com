---
title: Gesture Area
order: 225
---

## Overview

Captures a vertical pan/drag gesture over its content and writes the translation to a bound
[shared value](../digging-deeper/gestures), so the drag can drive animation on the UI thread with no PHP round-trip.
Children render normally — gesture detection wraps the whole content frame.

@verbatim
```blade
@php $drag = \Native\Mobile\Edge\SharedValue::make(); @endphp

<native:gesture-area :pan-y="$drag" @drag-end="onRelease">
    <native:column :translate-y="$drag" class="p-6 bg-theme-surface rounded-2xl">
        <native:text>Drag me</native:text>
    </native:column>
</native:gesture-area>
```
@endverbatim

## Props

- `pan-y` - A [`SharedValue`](../digging-deeper/gestures) that receives the vertical drag translation (required for
  the gesture to do anything). Bind it, then read it from animatable props (`translate-y`, `opacity`, `scale`, …)
  on the children.

## Events

- `@drag-end` - Fired when the user lifts their finger, with the final value as `{value: float}`. Use it to decide
  commit-vs-revert in PHP:

```php
public function onRelease(float $value): void
{
    if ($value > 150) {
        $this->dismiss();
    }
}
```

<aside>

Per-frame drag values stay on the native side and never round-trip through PHP — only `@drag-end` calls back. See
[Gestures & Animation](../digging-deeper/gestures) for shared values and interpolation formulas.

</aside>
