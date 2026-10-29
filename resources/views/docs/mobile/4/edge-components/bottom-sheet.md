---
title: Bottom Sheet
order: 130
---

## Overview

A modal panel that slides up from the bottom of the screen. Use it for contextual actions, forms, and detail views
that overlay the main content. Renders as SwiftUI's `.sheet` with `presentationDetents` on iOS and a Material3
`ModalBottomSheet` on Android.

Per Material 3, the container color resolves from `theme.surface`. For a custom surface wrap content in a
`<native:column class="bg-...">`.

@verbatim
```blade
@php $showSheet = false; @endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Open bottom sheet" @press="$showSheet = true" />

    <native:bottom-sheet :visible="$showSheet" @dismiss="$showSheet = false">
        <native:column class="w-full p-4 gap-3">
            <native:text class="text-xl font-bold text-theme-primary">Sheet Title</native:text>
            <native:text class="text-base text-theme-on-surface-variant">Sheet content goes here.</native:text>
            <native:button label="Close" @press="$showSheet = false" />
        </native:column>
    </native:bottom-sheet>
</native:column>
```
@endverbatim

## Props

- `visible` - Whether the sheet is shown (required, boolean)
- `detents` - Allowed sheet heights (optional, default: `"medium,large"`). Comma-separated combination of:
    - `small` (25% of screen)
    - `medium`
    - `large`
    - `full` (100% of screen)
    - `fit` (exactly as tall as the sheet's content, measured on the device and capped at `large`)
    - A numeric fraction `0.0`–`1.0` for a custom height (e.g. `"0.4"` for 40%)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@dismiss` - Component method called when the sheet is dismissed (swipe down or tap outside)

## Children

Accepts any EDGE elements as children. The children are rendered inside the sheet's content area.

## Examples

### Action sheet

@verbatim
```blade
@php $showActions = false; @endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Show actions" @press="$showActions = true" />

    <native:bottom-sheet :visible="$showActions" @dismiss="$showActions = false" detents="small">
        <native:column class="w-full gap-0 pb-8">
            <native:pressable @press="$showActions = false" class="w-full px-4 py-3">
                <native:row class="gap-3 items-center">
                    <native:icon class="text-theme-on-surface-variant" name="edit" :size="24" />
                    <native:text class="text-base text-theme-on-surface-variant">Edit</native:text>
                </native:row>
            </native:pressable>
            <native:divider />
            <native:pressable @press="$showActions = false" class="w-full px-4 py-3">
                <native:row class="gap-3 items-center">
                    <native:icon class="text-theme-on-surface-variant" name="share" :size="24" />
                    <native:text class="text-base text-theme-on-surface-variant">Share</native:text>
                </native:row>
            </native:pressable>
            <native:divider />
            <native:pressable @press="$showActions = false" class="w-full px-4 py-3">
                <native:row class="gap-3 items-center">
                    <native:icon name="delete" :size="24" color="#EF4444" />
                    <native:text class="text-base" color="#EF4444">Delete</native:text>
                </native:row>
            </native:pressable>
        </native:column>
    </native:bottom-sheet>
</native:column>
```
@endverbatim

### Form in a sheet

@verbatim
```blade
@php
    $showForm = false;
    $itemName = '';
    $itemDescription = '';
@endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Add item" @press="$showForm = true" />

    <native:bottom-sheet :visible="$showForm" @dismiss="$showForm = false" detents="medium,large">
        <native:column class="w-full p-4 gap-4">
            <native:text class="text-xl font-bold text-theme-on-surface-variant">Add Item</native:text>
            <native:outlined-text-input label="Name" native:model="itemName" />
            <native:outlined-text-input label="Description" native:model="itemDescription" multiline :min-lines="3" />
            <native:row class="gap-2 justify-end">
                <native:button label="Cancel" variant="secondary" @press="$showForm = false" />
                <native:button label="Save" @press="$showForm = false" />
            </native:row>
        </native:column>
    </native:bottom-sheet>
</native:column>
```
@endverbatim

### Custom height

@verbatim
```blade
@php
    $showPreview = false;
    $previewUrl = 'https://picsum.photos/600/400';
@endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Preview" @press="$showPreview = true" />

    <native:bottom-sheet :visible="$showPreview" @dismiss="$showPreview = false" detents="0.4">
        <native:image src="{{ $previewUrl }}" class="w-full h-full" fit="cover" />
    </native:bottom-sheet>
</native:column>
```
@endverbatim

### Fit to content

Your PHP code doesn't know the screen's height, so a fraction can't size a sheet to its content. Use `fit` instead:
the sheet is measured on the device and opens exactly as tall as its content, capped at `large`.

@verbatim
```blade
@php $showConfirm = false; @endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Delete" @press="$showConfirm = true" />

    <native:bottom-sheet :visible="$showConfirm" @dismiss="$showConfirm = false" detents="fit">
        <native:column class="w-full p-4 gap-3 pb-8">
            <native:text class="text-xl font-bold text-theme-on-surface-variant">Delete this item?</native:text>
            <native:text class="text-base text-theme-on-surface-variant">This can't be undone.</native:text>
            <native:button label="Delete" @press="$showConfirm = false" />
        </native:column>
    </native:bottom-sheet>
</native:column>
```
@endverbatim

On iOS, `fit` combines with the other detents: `detents="fit,large"` opens at the content's height and drags up to
`large`. The sheet also follows its content's height when it changes.

<aside>

Android's sheet only has a fixed half-screen partial stop, so a `fit` sheet opens at its content's height there and
ignores the other detents listed beside it.

</aside>

<aside>

Always handle the `@dismiss` event to update your component state. If you don't, the `visible` property will be out
of sync with the actual sheet state after the user dismisses it by gesture.

</aside>

## Element

```php
use Native\Mobile\UI\Elements\BottomSheet;

BottomSheet::make()
    ->visible($showSheet)
    ->detents('medium,large')
    ->onDismiss('hideSheet');
```

- `make()` - Create a bottom sheet
- `visible(bool $value = true)` - Toggle visibility
- `detents(string $detents)` - Allowed heights
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
- `onDismiss(string $method)` - Component method invoked on dismissal
