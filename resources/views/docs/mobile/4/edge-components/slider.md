---
title: Slider
order: 545
---

## Overview

A continuous (or stepped) value selector. The active track and thumb use `theme.primary` — there are no
per-instance color overrides.

`native:model` is supported with `live` / `blur` / `debounce` modifiers — useful for keeping PHP round-trips
under control while the user drags.

@verbatim
```blade
<native:slider :min="0" :max="100" :step="1" native:model="volume" />
```
@endverbatim

## Props

- `value` - Current value (optional, float)
- `min` - Minimum value (optional, float, default: `0`)
- `max` - Maximum value (optional, float, default: `1`)
- `step` - Snap increment (optional, float, default: `0` for continuous)
- `disabled` - Disable the slider (optional, boolean, default: `false`)
- `size` - `sm | md (default) | lg` (optional, string)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Livewire method called when the value changes. Receives the new float value

## Two-way Binding

@verbatim
```blade
{{-- Every drag tick fires --}}
<native:slider native:model.live="volume" :min="0" :max="100" />

{{-- Only fires on drag release --}}
<native:slider native:model.blur="volume" :min="0" :max="100" />

{{-- Coalesce ticks into one event after 300ms idle --}}
<native:slider native:model.debounce.300ms="volume" :min="0" :max="100" />
```
@endverbatim

`live` is the default and stress-tests the runtime's round-trip; `blur` is the most efficient for unsteady hands;
`debounce` is the middle ground.

## Examples

### Volume slider

@verbatim
```blade
<native:column class="w-full gap-2 p-4">
    <native:row class="w-full" :justify-content="3">
        <native:text class="text-sm">Volume</native:text>
        <native:text class="text-sm">{{ $volume }}%</native:text>
    </native:row>
    <native:slider :min="0" :max="100" :step="1" native:model.debounce.150ms="volume" />
</native:column>
```
@endverbatim

### Stepped picker

@verbatim
```blade
<native:slider :min="1" :max="5" :step="1" native:model="rating" />
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
- `size(string $value)` - `sm | md | lg`
- `a11yLabel(string $value)`, `a11yHint(string $value)` - Accessibility
- `syncMode(string $mode)`, `debounceMs(int $ms)` - Set by `native:model` modifiers
- `onChange(string $method)` - Livewire method invoked on change
