---
title: Select
order: 540
---

## Overview

A single-choice dropdown picker over a flat list of strings. On iOS, renders as a SwiftUI `Menu` (popover); on
Android, as an M3 `ExposedDropdownMenuBox` with an outlined trigger.

Per Model 3, colors and borders come from theme tokens.

@verbatim
```blade
<native:select
    label="Country"
    :options="['United States', 'Canada', 'Mexico']"
    placeholder="Select your country"
    native:model="country"
/>
```
@endverbatim

## Props

- `options` - Array of option strings (required, array)
- `value` - Currently selected option string (optional). Use `native:model` for two-way binding
- `label` - Label rendered above the trigger (optional, string)
- `placeholder` - Text shown when nothing is selected (optional, string)
- `disabled` - Disable the picker (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Livewire method called when the selection changes. Receives the new option string

## Two-way Binding

`native:model` binds the selected option to a Livewire string property:

@verbatim
```blade
<native:select :options="$countries" native:model="country" />
```
@endverbatim

## Examples

### Country picker

@verbatim
```blade
<native:select
    label="Country"
    placeholder="Select country"
    :options="['Australia', 'Brazil', 'Canada', 'Germany', 'Japan', 'United Kingdom', 'United States']"
    native:model="country"
/>
```
@endverbatim

### Manual handler

@verbatim
```blade
<native:select
    :options="['Daily', 'Weekly', 'Monthly']"
    :value="$cadence"
    @change="setCadence"
/>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Select;

Select::make()
    ->label('Country')
    ->placeholder('Select country')
    ->options(['Australia', 'Canada', 'United States'])
    ->value($country)
    ->onChange('setCountry');
```

- `make()` - Create a select
- `options(array $options)` - Option strings
- `value(string $val)` - Current selection
- `label(string $text)` - Label text
- `placeholder(string $text)` - Empty-state text
- `disabled(bool $value = true)` - Disable the picker
- `a11yLabel(string $value)`, `a11yHint(string $value)` - Accessibility
- `syncMode(string $mode)` - Set by `native:model` modifiers
- `onChange(string $method)` - Livewire method invoked on change
