---
title: Slider
order: 380
---

## Overview

A continuous (or stepped) value selector. The active track and thumb use `theme.primary` — there are no
per-instance color overrides.

`native:model` is supported with `live` / `blur` / `debounce` modifiers — useful for keeping PHP round-trips
under control while the user drags.

@verbatim
```blade
@php $brightness = 60; @endphp

<native:slider :min="0" :max="100" :step="1" native:model="brightness" />
```
@endverbatim

`brightness` is a public property on your component — the `@php` line stands in for
`public int $brightness = 60;`.

## Props

- `value` - Current value (optional, float)
- `min` - Minimum value (optional, float, default: `0`)
- `max` - Maximum value (optional, float, default: `1`)
- `step` - Snap increment (optional, float, default: `0` for continuous)
- `disabled` - Disable the slider (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when the value changes. Receives the new float value

<aside>

Margin classes position the slider; the active track and thumb colors come from the theme.

</aside>

## Two-way Binding

@verbatim
```blade
@php $intensity = 50; @endphp

{{-- Every drag tick fires --}}
<native:slider native:model.live="intensity" :min="0" :max="100" />

{{-- Only fires on drag release --}}
<native:slider native:model.blur="intensity" :min="0" :max="100" />

{{-- Coalesce ticks into one event after 300ms idle --}}
<native:slider native:model.debounce.300ms="intensity" :min="0" :max="100" />

<native:text class="text-sm text-theme-on-surface-variant">Intensity: {{ $intensity }}</native:text>
```
@endverbatim

`intensity` is a public property on your component (`public int $intensity = 50;`). All three sliders bind
the same property, so dragging any one of them syncs the others and the echo — notice *when* each modifier
pushes its update. `live` is the default and stress-tests the runtime's round-trip; `blur` is the most
efficient for unsteady hands; `debounce` is the middle ground. Omit the interval (`native:model.debounce`)
and it defaults to 300ms.

## Examples

### Volume slider

@verbatim
```blade
@php $volume = 40; @endphp

<native:column class="w-full gap-2 p-4">
    <native:row class="w-full justify-between">
        <native:text class="text-sm text-theme-on-surface">Volume</native:text>
        <native:text class="text-sm text-theme-on-surface-variant">{{ $volume }}%</native:text>
    </native:row>
    <native:slider :min="0" :max="100" :step="1" native:model.debounce.150ms="volume" />
</native:column>
```
@endverbatim

Declare `volume` as a public property on your component (`public int $volume = 40;`) and the label
tracks the thumb as it settles.

### Stepped picker

@verbatim
```blade
@php $rating = 3; @endphp

<native:slider :min="1" :max="5" :step="1" native:model="rating" />

<native:text class="text-sm text-theme-on-surface-variant">Rating: {{ $rating }} / 5</native:text>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Slider;

Slider::make()
    ->value($volume)
    ->min(0)
    ->max(100)
    ->step(1)
    ->onChange('setVolume');
```

- `make()` - Create a slider
- `value(float $val)`, `min(float $val)`, `max(float $val)`, `step(float $val)` - Range / current value
- `disabled(bool $value = true)` - Disable the slider
- `a11yLabel(string $value)`, `a11yHint(string $value)` - Accessibility
- `syncMode(string $mode)`, `debounceMs(int $ms)` - Set by `native:model` modifiers
- `onChange(string $method)` - Component method invoked on change
