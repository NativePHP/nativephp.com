---
title: Button Group
order: 150
---

## Overview

A segmented single-choice selector. Each option is a pressable pill in a horizontal bar; the active one fills with
`theme.primary`. The group owns the selected-index state.

Use this for short, mutually-exclusive choices that fit on one row. For more options or longer labels use a
[`<native:tab-row>`](tab-row) or [`<native:select>`](select).

@verbatim
```blade
@php $period = 0; @endphp

<native:button-group :options="['Daily', 'Weekly', 'Monthly']" native:model="period" />

<native:text class="text-sm text-theme-on-surface-variant">Showing {{ ['Daily', 'Weekly', 'Monthly'][$period] }} stats</native:text>
```
@endverbatim

`period` is a public int property on your component — the `@php` line stands in for `public int $period = 0;`. Tapping a segment syncs the new index back automatically, so anything echoing `$period` re-renders.

## Props

- `options` - Array of option labels (required, array of strings)
- `value` / `selected-index` - Currently selected index (optional, int, default: `0`)
- `disabled` - Disable the group (optional, boolean, default: `false`)
- `sync-mode` - How selection changes sync back to your component: `live | blur | debounce` (optional, usually set by `native:model` modifiers)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when the selection changes. Receives the new index as a parameter

## Two-way Binding

`native:model` binds the selected index to an integer property on your component — declare it as
`public int $planTier = 1;` and the group keeps it in sync:

@verbatim
```blade
@php $planTier = 1; @endphp

<native:button-group :options="$tiers" native:model="planTier" />

<native:text class="text-sm text-theme-on-surface-variant">Selected plan: {{ $tiers[$planTier] }}</native:text>
```
@endverbatim

## Examples

### Period picker

`reportRange` is a public int property on your component (`public int $reportRange = 2;`):

@verbatim
```blade
@php $reportRange = 2; @endphp

<native:button-group
    :options="['Day', 'Week', 'Month', 'Year']"
    native:model="reportRange"
/>

<native:text class="text-sm text-theme-on-surface-variant">Report range: {{ ['Day', 'Week', 'Month', 'Year'][$reportRange] }}</native:text>
```
@endverbatim

### With manual change handler

Instead of `native:model`, pass the current index with `:value` and handle changes yourself.
`$difficulty` is a public int property on your component, and `setDifficulty(int $index)` is the
method that receives the new index — assign it to `$difficulty` there:

@verbatim
```blade
@php $difficulty = 1; @endphp

<native:button-group
    :options="['Easy', 'Medium', 'Hard']"
    :value="$difficulty"
    @change="setDifficulty"
/>

<native:text class="text-sm text-theme-on-surface-variant">Difficulty: {{ ['Easy', 'Medium', 'Hard'][$difficulty] }}</native:text>
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
- `a11yHint(string $value)` - Accessibility hint
- `syncMode(string $mode)` - `live | blur | debounce` (set by `native:model` modifiers)
- `onChange(string $method)` - Component method invoked on change
