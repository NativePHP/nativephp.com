---
title: Modal
order: 710
---

## Overview

A full-screen modal overlay. Visibility is driven by the `visible` prop. Use a [bottom sheet](bottom-sheet) for
contextual actions; reach for `<native:modal>` when you want the entire screen covered (e.g. an onboarding flow,
image preview, or detail view).

Per Model 3, backdrop and surface colors come from `theme.background`. The close icon uses `theme.onSurfaceVariant`.

@verbatim
```blade
<native:modal :visible="$showDetails" @dismiss="closeDetails">
    <native:column class="w-full h-full p-4 gap-4 safe-area">
        <native:text class="text-2xl font-bold">Details</native:text>
        <native:text class="text-base">{{ $item->description }}</native:text>
    </native:column>
</native:modal>
```
@endverbatim

## Props

- `visible` - Whether the modal is shown (required, boolean)
- `dismissible` - Render a close icon and allow swipe-to-dismiss (optional, boolean, default: `true`)
- `a11y-label` - Accessibility label (optional)

## Events

- `@dismiss` - Livewire method called when the user dismisses the modal (close button tap or swipe). Always handle
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
<native:modal :visible="$preview !== null" @dismiss="closePreview">
    <native:column class="w-full h-full" center>
        <native:image src="{{ $preview }}" class="w-full" :fit="1" />
    </native:column>
</native:modal>
```
@endverbatim

### Non-dismissible loading modal

@verbatim
```blade
<native:modal :visible="$processing" :dismissible="false">
    <native:column fill center :gap="16">
        <native:activity-indicator size="lg" />
        <native:text class="text-base">Processing...</native:text>
    </native:column>
</native:modal>
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
- `onDismiss(string $method)` - Livewire method invoked on dismissal
