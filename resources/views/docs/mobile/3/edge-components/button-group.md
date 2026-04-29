---
title: Button Group
order: 315
---

## Overview

A segmented single-choice selector. Each option is a pressable pill in a horizontal bar; the active one fills with
`theme.primary`. The group owns the selected-index state.

Use this for short, mutually-exclusive choices that fit on one row. For more options or longer labels use a
[`<native:tab-row>`](tab-row) or [`<native:select>`](select).

@verbatim
```blade
<native:button-group :options="['Daily', 'Weekly', 'Monthly']" native:model="period" />
```
@endverbatim

## Props

- `options` - Array of option labels (required, array of strings)
- `value` / `selected-index` - Currently selected index (optional, int, default: `0`)
- `disabled` - Disable the group (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)

## Events

- `@change` - Livewire method called when the selection changes. Receives the new index as a parameter

## Two-way Binding

`native:model` binds the selected index to a Livewire integer property:

@verbatim
```blade
<native:button-group :options="$tiers" native:model="planTier" />
```
@endverbatim

## Examples

### Period picker

@verbatim
```blade
<native:button-group
    :options="['Day', 'Week', 'Month', 'Year']"
    native:model="period"
/>
```
@endverbatim

### With manual change handler

@verbatim
```blade
<native:button-group
    :options="['Easy', 'Medium', 'Hard']"
    :value="$difficulty"
    @change="setDifficulty"
/>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\ButtonGroup;

ButtonGroup::make()
    ->options(['Daily', 'Weekly', 'Monthly'])
    ->selectedIndex(1)
    ->onChange('setPeriod');
```

- `make()` - Create a button group
- `options(array $options)` - Option labels
- `selectedIndex(int $index)` - Currently selected index
- `disabled(bool $value = true)` - Disable the group
- `a11yLabel(string $value)` - Accessibility label
- `syncMode(string $mode)` - `live | blur | debounce` (set by `native:model` modifiers)
- `onChange(string $method)` - Livewire method invoked on change
