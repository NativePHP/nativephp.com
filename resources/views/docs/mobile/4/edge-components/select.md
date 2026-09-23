---
title: Select
order: 350
---

## Overview

A single-choice dropdown picker over a flat list of strings. On iOS, renders as a SwiftUI `Menu` (popover); on
Android, as an M3 `ExposedDropdownMenuBox` with an outlined trigger.

Per Material 3, colors and borders come from theme tokens.

@verbatim
```blade
@php $shippingCountry = null; @endphp

<native:select
    label="Country"
    :options="['United States', 'Canada', 'Mexico']"
    placeholder="Select your country"
    native:model="shippingCountry"
/>
```
@endverbatim

Here `shippingCountry` is a public string property on your component (the `@php` line stands in for
`public ?string $shippingCountry = null;`) — while it is `null`, the placeholder shows.

Options are a flat list of display strings — pass the strings you want shown, and the selected string is the
value bound back to your component. An associative `value => label` array is flattened to its labels, so the
displayed text is what you get.

## Props

- `options` - Array of option strings (required, array)
- `value` - Currently selected option string (optional). Use `native:model` for two-way binding
- `label` - Label rendered above the trigger (optional, string)
- `placeholder` - Text shown when nothing is selected (optional, string)
- `disabled` - Disable the picker (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when the selection changes. Receives the new option string

<aside>

Margin classes position the picker; its colors and borders come from the theme.

</aside>

## Two-way Binding

`native:model` binds the selected option to a string property on your component:

@verbatim
```blade
@php $country = 'Canada'; @endphp

<native:select
    label="Country"
    :options="$countries"
    native:model="country"
/>

<native:text class="text-sm text-theme-on-surface-variant">Shipping to: {{ $country }}</native:text>
```
@endverbatim

`country` is a public string property on your component (the `@php` line stands in for
`public string $country = 'Canada';`), and `$countries` is an array of option strings. Picking an option
syncs the selected string back automatically — no `@change` handler needed — so the echoed text updates
with the selection.

## Examples

### Country picker

@verbatim
```blade
@php $destination = 'Japan'; @endphp

<native:select
    label="Country"
    placeholder="Select country"
    :options="['Australia', 'Brazil', 'Canada', 'Germany', 'Japan', 'United Kingdom', 'United States']"
    native:model="destination"
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
- `onChange(string $method)` - Component method invoked on change
