---
title: Sheet Pane
order: 365
---

## Overview

An always-visible pane anchored to the bottom of the screen that the user drags between resting heights, like the
panes in Apple Maps. The drag and the spring-snap to each detent run natively, with no PHP round trip while the finger
moves.

Unlike a [bottom sheet](bottom-sheet), the pane isn't presented over the screen: it renders inside it, so floating
chrome such as the tab bar or a [FAB](fab) stays above it. Dragging the pane's non-scrolling area moves it, while
dragging inside a child [scroll view](scroll-view) scrolls the content.

@verbatim
```blade
<native:sheet-pane detents="200,560,780" :detent="$detent" @change="paneMoved">
    <native:column class="w-full p-4 gap-3">
        <native:text class="text-xl font-bold">Nearby</native:text>
        <native:scroll-view class="flex-1">
            ...
        </native:scroll-view>
    </native:column>
</native:sheet-pane>
```
@endverbatim

## Props

- `detents` - Resting heights (optional, default: `"200,560,780"`). Comma-separated combination of:
    - Heights in points (e.g. `"200,560"`)
    - `fit` (the content's measured height, capped at the available screen height minus `inset-bottom`)
- `detent` - Current resting height: a number of points, or `fit` (optional, default: the lowest detent)
- `corner-radius` - Corner radius in points (optional, default: `44`). Match the device's display curvature: the screen
  radius minus the inset
- `inset-x` - Horizontal inset from the screen edges in points (optional, default: `8`)
- `inset-bottom` - Inset from the bottom of the screen in points (optional, default: `8`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when a drag settles on a different detent. Receives the detent: `"fit"` for the
  fit detent, the height in points otherwise

Store the value and bind it back to `detent`. The pane only moves when `detent` changes, so republishing the value it
reported keeps it in place across re-renders:

```php
public float|string $detent = 200;

public function paneMoved(string $detent): void
{
    $this->detent = $detent === 'fit' ? 'fit' : (float) $detent;
}
```

## Children

Accepts any EDGE elements as children. The content is laid out once at the tallest detent and stays pinned to the top
of the pane: a lower detent reveals less of it and cuts it off at the bottom, rather than re-centering it.

## Fit to content

Your PHP code doesn't know the screen's height, so point heights can't size a pane to its content. List `fit` instead:
the content is measured on the device and the pane rests at its height. It combines with point heights, so
`detents="88,fit"` drags between a collapsed header and the full content.

@verbatim
```blade
<native:sheet-pane detents="88,fit" :detent="$detent" @change="paneMoved">
    <native:column class="w-full p-4 gap-3">
        <native:text class="text-xl font-bold">Flight AF 1234</native:text>
        <native:text class="text-base text-theme-on-surface-variant">Paris CDG → New York JFK</native:text>
        <native:text class="text-base text-theme-on-surface-variant">Boarding at 10:45, gate K32</native:text>
    </native:column>
</native:sheet-pane>
```
@endverbatim

Set `detent="fit"` to open the pane at the content's height. While it rests on `fit`, the pane follows the content
when its height changes, though not in the middle of a drag. The pane adds no padding of its own, so pad the content.

<aside>

Once `fit` is among the detents, the content takes its natural height instead of filling the tallest detent, so a
`flex-1` child no longer stretches.

</aside>

## Element

```php
use Native\Mobile\UI\Elements\SheetPane;

SheetPane::make()
    ->detents('88,fit')
    ->detent($detent)
    ->onChange('paneMoved');
```

- `make()` - Create a sheet pane
- `detents(string $detents)` - Resting heights
- `detent(float|int|string $height)` - Current resting height, in points or `'fit'`
- `cornerRadius(float $radius)` - Corner radius
- `insetX(float $points)` - Horizontal inset
- `insetBottom(float $points)` - Bottom inset
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
- `onChange(string $method)` - Component method invoked when the pane settles on a new detent
