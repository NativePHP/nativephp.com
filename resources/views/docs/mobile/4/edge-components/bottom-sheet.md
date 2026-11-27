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
    - A numeric fraction `0.0`–`1.0` for a custom height (e.g. `"0.4"` for 40%)
- `title` - The sheet's title, drawn the way each platform draws its own sheets (optional, string). See
  [Titled sheet](#titled-sheet)
- `close-label` - What VoiceOver reads for the close button of a titled iOS sheet (optional, string, default: the
  system's own label on iOS 26, "Close" before it). Also accepted as `closeLabel`
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@dismiss` - Component method called when the sheet is dismissed (swipe down, tap outside, or the close button of a
  titled iOS sheet)

## Children

Accepts any EDGE elements as children. The children are rendered inside the sheet's content area.

## Examples

### Titled sheet

Give the sheet a `title` instead of drawing your own heading row. Each platform then draws it the way its own sheets
look:

- **iOS** puts the title in the sheet's navigation bar and adds the system close button on the bar's leading edge,
  as Apple's Human Interface Guidelines ask for sheets. On iOS 26 it's the system's close button; on earlier versions,
  a filled xmark circle.
- **Android** shows the title as heading text under the drag handle. Material's modal bottom sheet has no close
  button, so none is added: people close it by swiping down, tapping the scrim, or pressing back.

@verbatim
```blade
@php
    $showFilters = false;
    $openNow = false;
    $indoorOnly = false;
@endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Filters" @press="$showFilters = true" />

    <native:bottom-sheet
        :visible="$showFilters"
        @dismiss="$showFilters = false"
        title="Filters"
        :close-label="__('Close')"
        detents="medium"
    >
        <native:column class="w-full p-4 gap-3">
            <native:toggle label="Open now" native:model="openNow" />
            <native:toggle label="Indoor only" native:model="indoorOnly" />
        </native:column>
    </native:bottom-sheet>
</native:column>
```
@endverbatim

<aside>

#### The iOS close button is added for you

Don't put your own close button in a titled sheet. On iOS it would appear next to the one the system already shows.
Tapping the system button closes the sheet and calls `@dismiss`, just as swiping down does, so the same handler
covers both.

A `permanent` sheet can't be dismissed, so it gets no close button. Its title still shows.

The package can't translate the close button's VoiceOver label. If your app's language can differ from the device's,
pass your own translation with `close-label`.

</aside>

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
    ->title('Filters')
    ->onDismiss('hideSheet');
```

- `make()` - Create a bottom sheet
- `visible(bool $value = true)` - Toggle visibility
- `detents(string $detents)` - Allowed heights
- `title(string $title)` - The sheet's title; an empty string leaves the sheet untitled
- `closeLabel(string $label)` - VoiceOver label for the close button of a titled iOS sheet
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
- `onDismiss(string $method)` - Component method invoked on dismissal
