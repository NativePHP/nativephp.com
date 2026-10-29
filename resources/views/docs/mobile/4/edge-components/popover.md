---
title: Popover
order: 285
---

## Overview

A small bubble that floats beside a view while `visible` is true, like a reactions bar above a message or a tip
pointing at a button. Its children are the bubble's content.

The element itself draws nothing and takes no touches. It marks the spot the bubble points at, so lay it over the view
it belongs to, in a [stack](stack) with `absolute top-0 bottom-0 left-0 right-0`. Put it beside a
[pressable](pressable), not inside one.

- **iOS** draws the system popover with its arrow, kept a popover on iPhone rather than turning into a sheet.
- **Android** draws a raised Material surface in a popup, centred on the spot.

Both take their colour from `theme.surface`.

@verbatim
```blade
@php $reacting = false; @endphp

<native:stack :width="44" :height="44">
    <native:pressable @press="$reacting = true" a11y-label="React">
        <native:stack :width="44" :height="44" center class="rounded-full bg-theme-surface-variant">
            <native:icon name="face.smiling" :size="22" />
        </native:stack>
    </native:pressable>

    <native:popover
        class="absolute top-0 bottom-0 left-0 right-0"
        :visible="$reacting"
        @dismiss="$reacting = false"
    >
        <native:row class="gap-2 p-2">
            <native:pressable @press="react('👍')"><native:text class="text-2xl">👍</native:text></native:pressable>
            <native:pressable @press="react('🎉')"><native:text class="text-2xl">🎉</native:text></native:pressable>
            <native:pressable @press="react('❤️')"><native:text class="text-2xl">❤️</native:text></native:pressable>
        </native:row>
    </native:popover>
</native:stack>
```
@endverbatim

```php
public bool $reacting = false;

public function react(string $emoji): void
{
    $this->reacting = false;
    // ...
}
```

## Placement

The bubble opens above the spot by default and takes the other side when it wouldn't fit between the spot and the
system bars, so one near the top of the screen opens below. Set `placement="bottom"` to prefer below instead.

## Props

- `visible` - Whether the bubble is shown (required, boolean)
- `placement` - The side the bubble prefers: `top` (default) or `bottom`
- `a11y-label` - Accessibility label for the bubble (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@dismiss` - Component method called when the person closes the bubble with a tap outside it, or back on Android.
  Setting `visible` to false from PHP never calls it

## Children

Accepts any EDGE elements as children. They're laid out in a column inside the bubble, which sizes itself to them, so
give the content its own padding.

<aside>

Close the bubble from PHP once something in it is chosen, and handle `@dismiss` to set `visible` back to false when the
person taps outside it. If you don't, setting `visible` to true again won't reopen it.

</aside>

## Element

```php
use Native\Mobile\UI\Elements\Popover;

Popover::make()
    ->visible($reacting)
    ->placement('bottom')
    ->onDismiss('closeReactions');
```

- `make()` - Create a popover
- `visible(bool $value = true)` - Toggle visibility
- `placement(string $placement)` - `top` or `bottom`; anything else throws
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
- `onDismiss(string $method)` - Component method invoked when the person closes it
