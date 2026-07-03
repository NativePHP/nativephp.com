---
title: Activity Indicator
order: 350
---

## Overview

A circular spinner indicating background activity. Always indeterminate — for determinate progress use
[`<native:progress-bar>`](progress-bar). Renders as a SwiftUI `ProgressView` on iOS and Material3
`CircularProgressIndicator` on Android.

@verbatim
```blade
<native:activity-indicator />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `size` - `"sm"`, `"md"` (default), or `"lg"` (optional, string). Legacy ints `1`=large, `2`=small are also accepted
- `color` - Spinner color as hex string (optional). Leave unset to use `theme.primary`
- `a11y-label` - Accessibility label (optional)

<aside>

`<native:activity-indicator />` is a self-closing element. It does not accept children.

The default tint comes from `theme.primary`. The `color` prop is an escape hatch — useful when the spinner sits on
a non-theme-styled container (e.g. a light spinner over a dark image overlay).

</aside>

## Examples

### Centered loading screen

@verbatim
```blade
<native:column fill center>
    <native:activity-indicator size="lg" />
    <native:text class="text-base text-slate-400 mt-4">Loading...</native:text>
</native:column>
```
@endverbatim

### Inline loading

@verbatim
```blade
<native:row :gap="8" :align-items="1">
    <native:activity-indicator size="sm" />
    <native:text class="text-sm text-slate-500">Refreshing</native:text>
</native:row>
```
@endverbatim

### Override the tint

@verbatim
```blade
<native:activity-indicator color="#7C3AED" />
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\ActivityIndicator;

ActivityIndicator::make()
    ->size('lg')
    ->color('#7C3AED')
    ->a11yLabel('Loading messages');
```

- `make()` - Create a new indicator
- `size(string|int $size)` - `"sm" | "md" | "lg"`. Legacy: `1`=large, `2`=small
- `color(string $hex)` - Override the theme tint
- `a11yLabel(string $value)` - Accessibility label
