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

<native:gesture-area :pan-y="$drag">
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

Formulas are chainable — each call returns a new derived value, so you can stack them:

```php
$drag->clamp(0, 200)->multiply(0.5)->add(20);
```

Animatable props that accept a shared value: `translate-x`, `translate-y`, `scale`, `rotate`, and `opacity`.

Everything above runs natively on the UI thread — as the finger moves, the bound props update at the display's
refresh rate with no PHP involved. If you need the PHP-side number (for example, the current snapshot with its
formula applied), call `->value()` on the shared value.

## Property animations

Any element animates when a value you bind to it changes between renders — no shared value, no gesture. Wrap the
change in `animate-duration` (milliseconds) and the element eases from its old value to the new one on the next
render. The animatable props are `translate-x`, `translate-y`, `scale`, `rotate`, and `opacity`:

@verbatim
```blade
{{-- A panel slides up from below when $shown flips to true --}}
<native:column
    :translate-y="$shown ? 0 : 120"
    :animate-duration="450"
    animate-easing="ease-out"
    class="bg-theme-surface rounded-2xl p-4">
    <native:text>Surprise!</native:text>
</native:column>
```
@endverbatim

Toggle `$shown` in a method and the panel eases into place — there's no animation code to write beyond the props.

### Duration & easing

- `animate-duration` — length in milliseconds (e.g. `250`). Without it, a change snaps instantly.
- `animate-easing` — the curve: `linear`, `ease-in`, `ease-out`, or `ease-in-out`.

Combine transforms freely under one duration — a card can slide, fade, and scale at once by animating
`translate-y`, `opacity`, and `scale` together.

### Looping

Set `animate-loop` to repeat an animation continuously — a pulsing dot, a breathing highlight:

@verbatim
```blade
<native:column :scale="$pulsing ? 1.15 : 1.0" :animate-duration="600" :animate-loop="true">
    <native:icon name="heart.fill" />
</native:column>
```
@endverbatim

## Press feedback

Press feedback is a special case: it reacts the instant a finger touches down — **before any PHP round-trip** — so
a tap feels immediate. Set any of these on a pressable element and the effect holds while pressed, then springs
back on release:

- `press-scale` — scale while pressed (e.g. `0.9` to shrink slightly)
- `press-opacity` — dim while pressed (e.g. `0.55`)
- `press-translate-y` — nudge down while pressed (e.g. `3`)

@verbatim
```blade
<native:pressable :press-scale="0.92" :press-opacity="0.85" :press-translate-y="3" @press="open">
    <native:text>Press me</native:text>
</native:pressable>
```
@endverbatim

Unlike `animate-*` (which runs when a re-render changes a value), press feedback needs no state and no handler —
the native side plays it locally on touch.

<aside>

Three tools, three jobs: **`animate-*` props** for state-driven motion (a value changed — ease to it), **press
feedback** for instant touch response, and **shared values** for finger-tracking or refresh-rate animation. For
motion *between screens*, see [Navigation](../the-basics/routing#custom-transitions).

</aside>
