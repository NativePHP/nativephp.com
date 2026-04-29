---
title: Radio Group
order: 530
---

## Overview

A single-choice container holding `<native:radio>` children. The group owns the selection; each child declares its
own `value` and label.

Per Model 3, all colors come from theme tokens.

@verbatim
```blade
<native:radio-group native:model="plan" label="Choose a plan">
    <native:radio value="free"  label="Free" />
    <native:radio value="pro"   label="Pro" />
    <native:radio value="team"  label="Team" />
</native:radio-group>
```
@endverbatim

## Props (Group)

- `value` - Currently selected `value` string (optional). Use `native:model` for two-way binding
- `label` - Label text rendered above the group (optional, string)
- `disabled` - Disable the entire group (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Livewire method called when the selection changes. Receives the new value as a parameter

## Two-way Binding

`native:model` binds the group's selected value to a Livewire string property:

@verbatim
```blade
<native:radio-group native:model="plan">
    <native:radio value="free" label="Free" />
    <native:radio value="pro"  label="Pro" />
</native:radio-group>
```
@endverbatim

## Children

`<native:radio>` declares a single option:

- `value` - The option's value (required, string). Must be unique within the group
- `label` - Inline label (optional, string)
- `disabled` - Disable just this option (optional, boolean, default: `false`)

## Examples

### Plan picker

@verbatim
```blade
<native:radio-group native:model="plan" label="Choose a plan">
    <native:radio value="free"     label="Free — $0/mo" />
    <native:radio value="pro"      label="Pro — $9/mo" />
    <native:radio value="team"     label="Team — $29/mo" />
    <native:radio value="custom"   label="Enterprise (contact sales)" disabled />
</native:radio-group>
```
@endverbatim

### Manual handler

@verbatim
```blade
<native:radio-group :value="$shippingMethod" @change="setShipping" label="Shipping">
    <native:radio value="standard" label="Standard (5-7 days)" />
    <native:radio value="express"  label="Express (1-2 days)" />
    <native:radio value="pickup"   label="Store pickup" />
</native:radio-group>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\RadioGroup;
use Nativephp\NativeUi\Elements\Radio;

RadioGroup::make(
    Radio::make('free')->label('Free'),
    Radio::make('pro')->label('Pro'),
    Radio::make('team')->label('Team'),
)
    ->value($plan)
    ->label('Choose a plan')
    ->onChange('setPlan');
```

### `RadioGroup` methods

- `make(Element ...$children)` - Create a group with radio children
- `value(string $selectedValue)` - Currently selected value
- `label(string $text)` - Group label
- `disabled(bool $value = true)` - Disable the group
- `a11yLabel(string $value)`, `a11yHint(string $value)` - Accessibility
- `syncMode(string $mode)` - Set by `native:model` modifiers
- `onChange(string $method)` - Livewire method invoked on selection change

### `Radio` methods

- `make(string $value = '')` - Create a radio with a value
- `label(string $label)` - Inline label
- `disabled(bool $value = true)` - Disable the option
