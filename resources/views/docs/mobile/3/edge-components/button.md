---
title: Button
order: 310
---

## Overview

A native button. Renders as a SwiftUI `Button` with `buttonStyle(...)` on iOS and a Material3 `Button` on Android.

Visual styling follows Model 3 — colors, radius, shadow, and typography come from the theme. There are intentionally
**no per-instance** color, background, border, radius, shadow, font-size, or font-weight overrides. For full visual
control drop to a [`<native:pressable>`](pressable) wrapping your own content.

@verbatim
```blade
<native:button label="Get Started" @press="handleStart" />
```
@endverbatim

## Props

The label can be passed as the `label` attribute or as slot content between the tags. If both are set, `label` wins.

- `label` - Button text (optional if using slot content)
- `variant` - Semantic style: `primary` (default), `secondary`, `destructive`, `ghost`
- `size` - `sm`, `md` (default), `lg`
- `icon` - A leading [icon](icons) name (optional)
- `icon-trailing` - A trailing [icon](icons) name (optional)
- `disabled` - Disable the button (optional, boolean, default: `false`)
- `loading` - Show a spinner in place of the leading icon and prevent presses (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label override (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@press` - Livewire method to call when tapped

<aside>

Layout attributes (`width`, `height`, `flex-grow`, `margin`, `align-self`) flow through to position the button
inside its parent. Per-instance `padding`, `bg`, `border-*`, `border-radius`, `elevation`, `opacity`, `font-*`
attributes are intentionally dropped before reaching the renderer.

</aside>

## Examples

### Variants

@verbatim
```blade
<native:column class="w-full gap-3 p-4">
    <native:button label="Save"   variant="primary"     @press="save" />
    <native:button label="Cancel" variant="secondary"   @press="cancel" />
    <native:button label="Delete" variant="destructive" @press="delete" />
    <native:button label="Skip"   variant="ghost"       @press="skip" />
</native:column>
```
@endverbatim

### Sizes

@verbatim
```blade
<native:row :gap="8" :align-items="1">
    <native:button label="Small"  size="sm" @press="action" />
    <native:button label="Medium" size="md" @press="action" />
    <native:button label="Large"  size="lg" @press="action" />
</native:row>
```
@endverbatim

### With icons

@verbatim
```blade
<native:button
    label="Continue"
    icon="check"
    icon-trailing="forward"
    @press="next"
/>
```
@endverbatim

### Loading state

@verbatim
```blade
<native:button label="Saving..." loading @press="save" />
```
@endverbatim

### Label as slot content

@verbatim
```blade
<native:button @press="save" variant="primary">
    Save Changes
</native:button>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Button;

Button::make('Save')
    ->variant('primary')
    ->size('md')
    ->icon('check')
    ->iconTrailing('forward')
    ->disabled(false)
    ->loading(false)
    ->onPress('save');
```

- `make(string $label = '')` - Create a button with an optional label
- `variant(string $value)` - `primary | secondary | destructive | ghost`
- `size(string $value)` - `sm | md | lg`
- `icon(string $name)` - Leading icon
- `iconTrailing(string $name)` - Trailing icon
- `disabled(bool $value = true)` - Disable the button
- `loading(bool $value = true)` - Show a spinner and prevent presses
- `a11yLabel(string $value)` - Accessibility label override
- `a11yHint(string $value)` - Accessibility hint
- `onPress(string $method)` - Livewire method to invoke on tap
