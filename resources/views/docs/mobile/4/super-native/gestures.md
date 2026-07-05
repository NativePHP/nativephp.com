---
title: Gestures & Animation
order: 70
---

## Overview

Smooth, finger-tracking animation can't wait on a PHP round-trip — at 120fps there's no time to ask the server
where a view should be. SuperNative solves this with **shared values**: a numeric value that lives on the native
side and is mutated on the UI thread by gestures and animations. PHP holds only an opaque handle; the per-frame
numbers never cross the boundary.

If you've used Reanimated in React Native, this is the same idea.

## Shared values

Create one in PHP with `SharedValue::make($initial)`, bind it to a gesture, and read it from any animatable prop:

@verbatim
```blade
@php $drag = \Native\Mobile\Edge\SharedValue::make(); @endphp

<native:gesture-area :pan-y="$drag" @drag-end="onRelease">
    <native:column
        :translate-y="$drag"
        :opacity="$drag->interpolate([0, 200], [1, 0])"
        :scale="$drag->interpolate([0, 200], [1, 0.7])"
        class="p-6 bg-theme-surface rounded-2xl">
        <native:text>Pull me down</native:text>
    </native:column>
</native:gesture-area>
```
@endverbatim

As the user drags, `$drag` updates on the UI thread and the bound props follow instantly — the card moves, fades,
and shrinks together, with no PHP involved during the gesture.

### Formulas

Rather than bind the raw value everywhere, derive from it with a chainable formula. Each method returns a new
derived value, so one gesture can drive many props differently:

- `->interpolate([$inMin, $inMax], [$outMin, $outMax])` — linearly remap a range (clamps at the ends).
- `->clamp($min, $max)` — constrain to a range.
- `->multiply($factor)` — scale.
- `->add($offset)` — shift.

```php
$drag->interpolate([0, 200], [1, 0]);  // 0→1, 200→0, fully faded past 200
```

Animatable props that accept a shared value: `translate-x`, `translate-y`, `scale`, `rotate`, and `opacity`.

## Reacting when the gesture ends

Per-frame values stay on the native side, but you often need PHP to make a decision when the user lets go — commit
or snap back. The [`<native:gesture-area>`](../edge-components/gesture-area) fires `@drag-end` with the final
value:

```php
public function onRelease(float $value): void
{
    if ($value > 150) {
        $this->dismiss();      // dragged far enough — commit
    }
    // otherwise the view springs back to its resting position
}
```

Call `->value()` on a shared value inside such a handler to read its PHP-side snapshot (with any formula applied)
if you need the derived number rather than the raw translation.

## Property animations

For state-driven motion that isn't gesture-tracked — a card that slides in when a value changes — use the
`animate-*` props on any element (`animate-duration`, `animate-easing`) together with the transform props. Those
animate whenever the target value changes between renders, no shared value required.

<aside>

Reach for a shared value when motion must track a finger or run at display refresh rate. For a one-shot
transition between two states, `animate-*` props are simpler.

</aside>
