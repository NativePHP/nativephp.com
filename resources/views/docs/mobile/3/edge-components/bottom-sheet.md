---
title: Bottom Sheet
order: 700
---

## Overview

A modal bottom sheet that slides up from the bottom of the screen. Use it for contextual actions, forms, and detail
views that overlay the main content. Renders as a native bottom sheet on both iOS and Android.

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

All [shared layout and style attributes](layout) are supported, plus:

- `visible` - Whether the sheet is shown (required, boolean)

## Events

- `@dismiss` - Livewire method called when the sheet is dismissed (e.g. by swiping down or tapping the scrim)

## Children

Accepts any EDGE elements as children. The children are rendered inside the sheet's content area.

## Examples

### Action sheet

@verbatim
```blade
<native:bottom-sheet :visible="$showActions" @dismiss="hideActions">
    <native:column class="w-full gap-0 pb-8">
        <native:pressable @press="editItem" class="w-full px-4 py-3">
            <native:row :gap="12" :align-items="1">
                <native:icon name="edit" :size="24" color="#1E293B" />
                <native:text class="text-base">Edit</native:text>
            </native:row>
        </native:pressable>
        <native:divider />
        <native:pressable @press="shareItem" class="w-full px-4 py-3">
            <native:row :gap="12" :align-items="1">
                <native:icon name="share" :size="24" color="#1E293B" />
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
<native:bottom-sheet :visible="$showForm" @dismiss="closeForm">
    <native:column class="w-full p-4 gap-4">
        <native:text class="text-xl font-bold">Add Item</native:text>
        <native:text-input label="Name" @model="itemName" />
        <native:text-input label="Description" @model="itemDescription" multiline :min-lines="3" />
        <native:row :gap="8" :justify-content="2">
            <native:button label="Cancel" @press="closeForm" />
            <native:button label="Save" @press="saveItem" color="#7C3AED" label-color="#FFFFFF" />
        </native:row>
    </native:column>
</native:bottom-sheet>
```
@endverbatim

### Toggling a sheet

The sheet is controlled by a Livewire property. Set `visible` to `true` to show it, and handle the `@dismiss` event to
hide it when the user swipes it away.

@verbatim
```blade
{{-- Trigger --}}
<native:button label="Show Options" @press="showSheet" />

{{-- Sheet --}}
<native:bottom-sheet :visible="$showSheet" @dismiss="hideSheet">
    <native:column class="w-full p-4">
        <native:text>Sheet content</native:text>
    </native:column>
</native:bottom-sheet>
```
@endverbatim

<aside>

Always handle the `@dismiss` event to update your Livewire state. If you don't, the `visible` property will be out of
sync with the actual sheet state after the user dismisses it by gesture.

</aside>
