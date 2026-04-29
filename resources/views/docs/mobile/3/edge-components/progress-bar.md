---
title: Progress Bar
order: 355
---

## Overview

A linear progress indicator. When `value` is supplied, renders as determinate progress in `[0.0, 1.0]`. Without a
value (or with `indeterminate`), renders an animated wave.

For a circular spinner use [`<native:activity-indicator>`](activity-indicator) instead.

Per Model 3, the progress fill uses `theme.primary` and the track uses `theme.surfaceVariant`. The optional `color`
prop is an escape hatch for non-theme containers.

@verbatim
```blade
<native:progress-bar :value="0.65" />
```
@endverbatim

## Props

- `value` - Current progress in `[0.0, 1.0]` (optional, float). Setting `value` implies `indeterminate=false`
- `indeterminate` - Force indeterminate mode (optional, boolean, default: `false` when `value` is set, otherwise `true`)
- `color` - Override the fill color as hex string (optional)
- `track-color` - Override the track color as hex string (optional)
- `a11y-label` - Accessibility label (optional)

<aside>

`<native:progress-bar />` is a self-closing element. It does not accept children.

</aside>

## Examples

### Determinate

@verbatim
```blade
<native:column class="w-full gap-2 p-4">
    <native:row class="w-full" :justify-content="3">
        <native:text class="text-sm">Uploading</native:text>
        <native:text class="text-sm">{{ round($progress * 100) }}%</native:text>
    </native:row>
    <native:progress-bar :value="$progress" />
</native:column>
```
@endverbatim

### Indeterminate

@verbatim
```blade
<native:progress-bar indeterminate a11y-label="Loading" />
```
@endverbatim

### With color override

@verbatim
```blade
<native:progress-bar :value="0.4" color="#22C55E" track-color="#DCFCE7" />
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\ProgressBar;

ProgressBar::make()->value(0.65);

ProgressBar::make()->indeterminate();
```

- `make()` - Create a progress bar
- `value(float $val)` - Determinate progress in `[0.0, 1.0]`
- `indeterminate(bool $value = true)` - Force indeterminate mode
- `color(string $hex)` - Override the fill tint
- `trackColor(string $hex)` - Override the track color
- `a11yLabel(string $value)` - Accessibility label
