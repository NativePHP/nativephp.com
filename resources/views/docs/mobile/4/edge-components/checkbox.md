---
title: Checkbox
order: 190
---

## Overview

A binary tick/untick control with an optional inline label. On iOS, renders as a tappable SF Symbol pair
(`checkmark.square.fill` / `square`) — SwiftUI has no native checkbox primitive. On Android, renders as a Material3
`Checkbox`.

Per Model 3, check/border/label colors come from the theme — no per-instance overrides.

@verbatim
```blade
<native:checkbox label="I agree to the terms" native:model="agreed" />
```
@endverbatim

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

Use `native:model` for automatic two-way binding with a boolean property on your component. The `live`, `blur`,
and `debounce` modifiers set `sync-mode` (and `debounce-ms`) for you, though for a discrete tap every toggle is a
single event.

@verbatim
```blade
<native:checkbox label="Subscribe" native:model="subscribed" />
```
@endverbatim

## Examples

### Multiple options

@verbatim
```blade
<native:column class="w-full gap-2 p-4">
    <native:checkbox label="Email notifications" native:model="emailNotifications" />
    <native:checkbox label="SMS notifications" native:model="smsNotifications" />
    <native:checkbox label="Push notifications" native:model="pushNotifications" />
</native:column>
```
@endverbatim

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
