---
title: Chip
order: 200
---

## Overview

A compact selectable tag with a boolean active state and an optional leading icon. Renders as a capsule.

When selected, the chip fills with `theme.primary` and uses `theme.onPrimary` for content. When unselected, it uses
`theme.surfaceVariant` with a `theme.outline` 1pt stroke. Colors come from the theme; the capsule radius defaults to
fully rounded and can be adjusted with `rounded-*` classes.

@verbatim
```blade
<native:chip label="Verified" icon="check" native:model="filterVerified" />
```
@endverbatim

## Props

- `label` - Chip text (optional, string). Can also be passed as the first argument to `make()`
- `selected` / `value` - Whether the chip is active (optional, boolean, default: `false`)
- `icon` - Leading [icon](icons) name (optional, string)
- `disabled` - Disable the chip (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when toggled. Receives the new boolean value

## Two-way Binding

`native:model` binds the selected state to a boolean property on your component:

@verbatim
```blade
<native:chip label="On Sale" native:model="filterOnSale" />
```
@endverbatim

## Examples

### Filter chip row

@verbatim
```blade
<native:row :gap="8" class="w-full p-4">
    <native:chip label="All"     :selected="$filter === 'all'"     @change="setFilter('all')" />
    <native:chip label="Active"  :selected="$filter === 'active'"  @change="setFilter('active')" />
    <native:chip label="Archive" :selected="$filter === 'archive'" @change="setFilter('archive')" />
</native:row>
```
@endverbatim

### With icon

@verbatim
```blade
<native:chip label="Verified" icon="check" native:model="onlyVerified" />
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Chip;

Chip::make('Verified')
    ->icon('check')
    ->selected($onlyVerified)
    ->onChange('toggleVerified');
```

- `make(string $label = '')` - Create a chip with an optional label
- `label(string $label)` - Set the chip text
- `selected(bool $selected = true)` - Active state
- `icon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` -
  Leading icon; pass `ios:` / `android:` for per-platform symbols
- `disabled(bool $value = true)` - Disable the chip
- `a11yLabel(string $value)`, `a11yHint(string $value)` - Accessibility
- `syncMode(string $mode)` - Set by `native:model` modifiers
- `onChange(string $method)` - Component method invoked on toggle
