---
title: Bottom Sheet
order: 700
---

## Overview

A modal panel that slides up from the bottom of the screen. Use it for contextual actions, forms, and detail views
that overlay the main content. Renders as SwiftUI's `.sheet` with `presentationDetents` on iOS and a Material3
`ModalBottomSheet` on Android.

Per Model 3, the container color resolves from `theme.surface`. For a custom surface wrap content in a
`<native:column class="bg-...">`.

@verbatim
```blade
<native:bottom-sheet :visible="$showSheet" @dismiss="hideSheet">
    <native:column class="w-full p-4 gap-3">
        <native:text class="text-xl font-bold">Sheet Title</native:text>
        <native:text class="text-base text-slate-500">Sheet content goes here.</native:text>
        <native:button label="Close" @press="hideSheet" />
    </native:column>
</native:bottom-sheet>
```
@endverbatim

## Props

- `visible` - Whether the sheet is shown (required, boolean)
- `detents` - Allowed sheet heights (optional, default: `"medium,large"`). Comma-separated combination of:
    - `small` (25% of screen)
    - `medium`
    - `large`
    - `full` (100% of screen)
    - A numeric fraction `0.0`–`1.0` for a custom height (e.g. `"0.4"` for 40%)
- `a11y-label` - Accessibility label (optional)

## Events

- `@dismiss` - Livewire method called when the sheet is dismissed (swipe down or tap outside)

## Children

Accepts any EDGE elements as children. The children are rendered inside the sheet's content area.

## Examples

### Action sheet

@verbatim
```blade
<native:bottom-sheet :visible="$showActions" @dismiss="hideActions" detents="small">
    <native:column class="w-full gap-0 pb-8">
        <native:pressable @press="editItem" class="w-full px-4 py-3">
            <native:row :gap="12" :align-items="1">
                <native:icon name="edit" :size="24" />
                <native:text class="text-base">Edit</native:text>
            </native:row>
        </native:pressable>
        <native:divider />
        <native:pressable @press="shareItem" class="w-full px-4 py-3">
            <native:row :gap="12" :align-items="1">
                <native:icon name="share" :size="24" />
                <native:text class="text-base">Share</native:text>
            </native:row>
        </native:pressable>
        <native:divider />
        <native:pressable @press="deleteItem" class="w-full px-4 py-3">
            <native:row :gap="12" :align-items="1">
                <native:icon name="delete" :size="24" color="#EF4444" />
                <native:text class="text-base" color="#EF4444">Delete</native:text>
            </native:row>
        </native:pressable>
    </native:column>
</native:bottom-sheet>
```
@endverbatim

### Form in a sheet

@verbatim
```blade
<native:bottom-sheet :visible="$showForm" @dismiss="closeForm" detents="medium,large">
    <native:column class="w-full p-4 gap-4">
        <native:text class="text-xl font-bold">Add Item</native:text>
        <native:outlined-text-input label="Name" native:model="itemName" />
        <native:outlined-text-input label="Description" native:model="itemDescription" multiline :min-lines="3" />
        <native:row :gap="8" :justify-content="2">
            <native:button label="Cancel" variant="secondary" @press="closeForm" />
            <native:button label="Save" @press="saveItem" />
        </native:row>
    </native:column>
</native:bottom-sheet>
```
@endverbatim

### Custom height

@verbatim
```blade
<native:bottom-sheet :visible="$showPreview" @dismiss="closePreview" detents="0.4">
    <native:image src="{{ $previewUrl }}" class="w-full h-full" :fit="2" />
</native:bottom-sheet>
```
@endverbatim

<aside>

Always handle the `@dismiss` event to update your Livewire state. If you don't, the `visible` property will be out
of sync with the actual sheet state after the user dismisses it by gesture.

</aside>

## Element

```php
use Nativephp\NativeUi\Elements\BottomSheet;

BottomSheet::make()
    ->visible($showSheet)
    ->detents('medium,large')
    ->onDismiss('hideSheet');
```

- `make()` - Create a bottom sheet
- `visible(bool $value = true)` - Toggle visibility
- `detents(string $detents)` - Allowed heights
- `a11yLabel(string $value)` - Accessibility label
- `onDismiss(string $method)` - Livewire method invoked on dismissal
