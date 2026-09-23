---
title: Toggle
order: 440
---

## Overview

A native on/off switch. Renders as a SwiftUI `Toggle` on iOS and a Material3 `Switch` on Android.

Per Material 3, the active track / thumb colors come from `theme.primary` / `theme.onPrimary`. There are no per-instance
color overrides. For custom visuals drop to [`<native:pressable>`](pressable) wrapping your own drawing.

@verbatim
```blade
@php $darkMode = false; @endphp

<native:toggle label="Dark Mode" :value="$darkMode" @change="toggleDarkMode" />
```
@endverbatim

`darkMode` is a public bool property on your component — the `@php` line stands in for
`public bool $darkMode = false;` — and `toggleDarkMode()` is the method that flips it.

## Props

- `value` - Current toggle state (optional, boolean, default: `false`)
- `label` - Inline label text rendered to the left of the switch (optional)
- `disabled` - Disable the toggle (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when the toggle is flipped. Receives the new boolean value as a parameter

<aside>

`<native:toggle />` is a self-closing element. It does not accept children.

Layout attributes flow through; per-instance `padding`, `bg`, `border-*`, `elevation`, `opacity` are dropped
before rendering.

</aside>

## Two-way Binding

Use the `native:model` directive for automatic two-way binding with a component property. This expands to
`:value` plus an `@change` handler that calls `__syncProperty`.

@verbatim
```blade
@php $notifications = true; @endphp

<native:toggle label="Notifications" native:model="notifications" />

<native:text class="text-sm text-theme-on-surface-variant">{{ $notifications ? 'You are subscribed' : 'Notifications are muted' }}</native:text>
```
@endverbatim

Flipping the switch syncs the `notifications` property back automatically — the echo below it updates
on every flip, no `@change` handler needed.

`sync-mode` and `debounce-ms` are accepted for API consistency with the other stateful components, but for a
discrete tap the distinction between `live`, `blur`, and `debounce` makes no real difference — every flip is one
event.

## Examples

### Settings list

@verbatim
```blade
@php $darkMode = false; $notifications = true; $locationEnabled = true; @endphp

<native:column class="w-full gap-0">
    <native:row class="w-full px-4 py-3 justify-between items-center">
        <native:text class="text-base text-theme-on-surface-variant">Dark Mode</native:text>
        <native:toggle native:model="darkMode" />
    </native:row>
    <native:divider />
    <native:row class="w-full px-4 py-3 justify-between items-center">
        <native:text class="text-base text-theme-on-surface-variant">Notifications</native:text>
        <native:toggle native:model="notifications" />
    </native:row>
    <native:divider />
    <native:row class="w-full px-4 py-3 justify-between items-center">
        <native:text class="text-base text-theme-on-surface-variant">Location</native:text>
        <native:toggle :value="$locationEnabled" @change="toggleLocation" disabled />
    </native:row>
</native:column>
```
@endverbatim

### With inline label

@verbatim
```blade
@php $pushEnabled = true; @endphp

<native:toggle label="Push Notifications" native:model="pushEnabled" />
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Toggle;

Toggle::make()
    ->value($darkMode)
    ->label('Dark Mode')
    ->disabled(false)
    ->onChange('toggleDarkMode');
```

- `make()` - Create a toggle
- `value(bool $checked)` - Current state
- `label(string $text)` - Inline label
- `disabled(bool $value = true)` - Disable the toggle
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
- `syncMode(string $mode)` - `live | blur | debounce` (set by `native:model` modifiers)
- `debounceMs(int $ms)` - Debounce interval when `syncMode === 'debounce'`
- `onChange(string $method)` - Component method invoked on flip
