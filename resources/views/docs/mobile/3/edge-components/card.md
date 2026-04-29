---
title: Card
order: 260
---

## Overview

A content surface with three semantic variants:

- `filled` (default) — `theme.surfaceVariant` background, no stroke. Medium emphasis
- `outlined` — `theme.surface` + a `theme.outline` 1pt stroke. Low emphasis
- `elevated` — `theme.surface` + a soft drop shadow. High emphasis

Per Model 3, all colors and the corner radius (`theme.radiusLg`) come from the theme. For custom visuals drop to a
styled `<native:column>` or [`<native:pressable>`](pressable).

@verbatim
```blade
<native:card variant="elevated">
    <native:column class="p-4 gap-2">
        <native:text class="text-lg font-bold">Card Title</native:text>
        <native:text class="text-base">Card content goes here.</native:text>
    </native:column>
</native:card>
```
@endverbatim

## Props

- `variant` - `filled` (default), `outlined`, or `elevated`
- `filled`, `outlined`, `elevated` - Boolean shortcuts for the corresponding variant
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

Cards honor the standard `@press` and `@longPress` directives — useful for tappable cards.

## Children

Accepts any EDGE elements as children. Typically wraps a `<native:column>` so internal content has padding and gap
control.

## Examples

### Outlined card

@verbatim
```blade
<native:card outlined>
    <native:column class="p-4 gap-2">
        <native:text class="text-lg font-semibold">Settings</native:text>
        <native:text class="text-base text-slate-500">Manage your preferences</native:text>
    </native:column>
</native:card>
```
@endverbatim

### Tappable card

@verbatim
```blade
<native:card variant="elevated" @press="openItem({{ $item->id }})">
    <native:column class="p-4 gap-2">
        <native:text class="text-lg font-bold">{{ $item->title }}</native:text>
        <native:text class="text-base">{{ $item->excerpt }}</native:text>
    </native:column>
</native:card>
```
@endverbatim

### Card with image header

@verbatim
```blade
<native:card variant="elevated">
    <native:image src="{{ $post->cover }}" class="w-full" :height="160" :fit="2" />
    <native:column class="p-4 gap-2">
        <native:text class="text-lg font-bold">{{ $post->title }}</native:text>
        <native:text class="text-base">{{ $post->excerpt }}</native:text>
    </native:column>
</native:card>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Card;

Card::make()->variant('elevated');

// Shortcuts:
Card::make()->filled();
Card::make()->outlined();
Card::make()->elevated();
```

- `make()` - Create a card
- `variant(string $variant)` - `filled | outlined | elevated`
- `filled()`, `outlined()`, `elevated()` - Variant shortcuts
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
