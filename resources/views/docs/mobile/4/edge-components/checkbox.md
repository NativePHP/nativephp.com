---
title: Checkbox
order: 190
---

## Overview

A binary tick/untick control with an optional inline label. On iOS, renders as a tappable SF Symbol pair
(`checkmark.square.fill` / `square`) — SwiftUI has no native checkbox primitive. On Android, renders as a Material3
`Checkbox`.

Per Material 3, check/border/label colors come from the theme — no per-instance overrides.

@verbatim
```blade
@php $agreed = false; @endphp

<native:checkbox label="I agree to the terms" native:model="agreed" />

<native:text class="text-sm text-theme-on-surface-variant">{{ $agreed ? 'Thanks for agreeing!' : 'Tap the box to agree' }}</native:text>
```
@endverbatim

Here `agreed` is a public boolean property on your component — the `@php` line stands in for
`public bool $agreed = false;`. Toggling the box syncs the new state back automatically.

## Props

- `value` - Current checked state (optional, boolean, default: `false`)
- `label` - Inline label rendered to the right of the box (optional, string)
- `disabled` - Disable the checkbox (optional, boolean, default: `false`)
- `sync-mode` - `live | blur | debounce` (optional, string; set by `native:model` modifiers)
- `debounce-ms` - Debounce interval when `sync-mode` is `debounce` (optional, int)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when toggled. Receives the new boolean value as a parameter

<aside>

Margin classes position the checkbox; the check, border, and label colors come from the theme.

</aside>

## Two-way Binding

Use `native:model` for automatic two-way binding with a public boolean property on your component. The `live`,
`blur`, and `debounce` modifiers set `sync-mode` (and `debounce-ms`) for you, though for a discrete tap every
toggle is a single event.

@verbatim
```blade
@php $subscribed = true; @endphp

<native:checkbox label="Subscribe to the newsletter" native:model="subscribed" />

<native:text class="text-sm text-theme-on-surface-variant">{{ $subscribed ? 'You are subscribed' : 'Not subscribed' }}</native:text>
```
@endverbatim

`subscribed` is a public boolean property on your component (the `@php` line stands in for
`public bool $subscribed = true;`). Every toggle syncs the new value back, so the `@{{ $subscribed }}` echo
updates as soon as you tap.

## Examples

### Multiple options

@verbatim
```blade
@php
    $emailNotifications = true;
    $smsNotifications = false;
    $pushNotifications = true;
@endphp

<native:column class="w-full gap-2 p-4">
    <native:checkbox label="Email notifications" native:model="emailNotifications" />
    <native:checkbox label="SMS notifications" native:model="smsNotifications" />
    <native:checkbox label="Push notifications" native:model="pushNotifications" />

    <native:text class="text-sm text-theme-on-surface-variant">Enabled: {{ ($emailNotifications ? 1 : 0) + ($smsNotifications ? 1 : 0) + ($pushNotifications ? 1 : 0) }} of 3</native:text>
</native:column>
```
@endverbatim

Each checkbox binds its own public boolean property; the summary line re-renders on every toggle.

### Disabled

@verbatim
```blade
<native:checkbox label="Two-factor authentication (coming soon)" disabled />
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Checkbox;

Checkbox::make()
    ->value($agreed)
    ->label('I agree to the terms')
    ->onChange('setAgreed');
```

- `make()` - Create a checkbox
- `value(bool $checked)` - Current state
- `label(string $label)` - Inline label
- `disabled(bool $value = true)` - Disable the checkbox
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
- `syncMode(string $mode)`, `debounceMs(int $ms)` - Set by `native:model` modifiers
- `onChange(string $method)` - Component method invoked on toggle
