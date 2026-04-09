---
title: Button
order: 310
---

## Overview

A tappable button element. The label can be set via the `label` attribute or as slot content between the tags. Buttons
support custom colors, font sizing, and disabled state.

@verbatim
```blade
<native:button label="Get Started" @press="handleStart" color="#7C3AED" label-color="#FFFFFF" />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `label` - Button text (optional if using slot content)
- `color` - Background color as hex string (optional)
- `label-color` - Text color as hex string (optional)
- `font-size` - Label text size (optional, float)
- `disabled` - Disable the button (optional, boolean, default: `false`)

## Events

- `@press` - Livewire method to call when tapped
- `@longPress` - Livewire method to call on long press

## Examples

### Label as attribute

@verbatim
```blade
<native:button label="Save" @press="save" color="#22C55E" label-color="#FFFFFF" />
```
@endverbatim

### Label as slot content

@verbatim
```blade
<native:button @press="save" class="bg-violet-600 rounded-full text-white text-lg">
    Save Changes
</native:button>
```
@endverbatim

<aside>

When both a `label` attribute and slot content are provided, the `label` attribute takes precedence.

</aside>

### Button row

@verbatim
```blade
<native:row :gap="8" :justify-content="1">
    <native:button label="Cancel" @press="cancel" color="#94A3B8" label-color="#FFFFFF" />
    <native:button label="Confirm" @press="confirm" color="#7C3AED" label-color="#FFFFFF" />
</native:row>
```
@endverbatim

### Disabled button

@verbatim
```blade
<native:button label="Submit" disabled color="#CBD5E1" label-color="#94A3B8" />
```
@endverbatim

### Long press

@verbatim
```blade
<native:button label="Hold me" @press="tap" @longPress="longTap" color="#272d48" label-color="#FFFFFF" />
```
@endverbatim
