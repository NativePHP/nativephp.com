---
title: Modal
order: 280
---

## Overview

A full-screen modal overlay. Visibility is driven by the `visible` prop. Use a [bottom sheet](bottom-sheet) for
contextual actions; reach for `<native:modal>` when you want the entire screen covered (e.g. an onboarding flow,
image preview, or detail view).

Per Model 3, backdrop and surface colors come from `theme.background`. The close icon uses `theme.onSurfaceVariant`.

@verbatim
```blade
@php
    $showDetails = false;
    $description = 'Everything about the selected item goes here.';
@endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="View details" @press="$showDetails = true" />

    <native:modal :visible="$showDetails" @dismiss="$showDetails = false">
        <native:column class="w-full h-full p-4 gap-4 safe-area">
            <native:text class="text-2xl font-bold">Details</native:text>
            <native:text class="text-base">{{ $description }}</native:text>
        </native:column>
    </native:modal>
</native:column>
```
@endverbatim

## Props

- `visible` - Whether the modal is shown (required, boolean)
- `dismissible` - Render a close icon and allow swipe-to-dismiss (optional, boolean, default: `true`)
- `a11y-label` - Accessibility label (optional)

## Events

- `@dismiss` - Component method called when the user dismisses the modal (close button tap or swipe). Always handle
  this to keep your `visible` state in sync

## Children

Accepts any EDGE elements as children. The children are rendered inside the modal's content area below the
auto-supplied close button (when `dismissible`).

<aside>

The `@dismiss` callback only fires from explicit user actions (close button tap or system swipe-to-dismiss).
Programmatically setting `visible` to `false` from PHP does not fire the callback.

</aside>

## Examples

### Image preview

@verbatim
```blade
@php
    $showImage = false;
    $previewUrl = 'https://picsum.photos/800/1200';
@endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Preview image" @press="$showImage = true" />

    <native:modal :visible="$showImage" @dismiss="$showImage = false">
        <native:column class="w-full h-full" center>
            <native:image src="{{ $previewUrl }}" class="w-full" :fit="1" />
        </native:column>
    </native:modal>
</native:column>
```
@endverbatim

### Non-dismissible loading modal

@verbatim
```blade
@php $processing = false; @endphp

<native:column class="w-full gap-3 items-start">
    <native:button label="Start processing" @press="$processing = true" />

    <native:modal :visible="$processing" :dismissible="false">
        <native:column fill center :gap="16">
            <native:activity-indicator size="lg" />
            <native:text class="text-base">Processing...</native:text>
            {{-- A real app closes this from PHP when the work finishes;
                 the button stands in for that here since the modal can't
                 be dismissed by the user. --}}
            <native:button label="Done" variant="secondary" @press="$processing = false" />
        </native:column>
    </native:modal>
</native:column>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Modal;

Modal::make()
    ->visible($showDetails)
    ->dismissible(true)
    ->onDismiss('closeDetails');
```

- `make()` - Create a modal
- `visible(bool $value = true)` - Toggle visibility
- `dismissible(bool $value = true)` - Allow user dismissal
- `a11yLabel(string $value)` - Accessibility label
- `onDismiss(string $method)` - Component method invoked on dismissal
