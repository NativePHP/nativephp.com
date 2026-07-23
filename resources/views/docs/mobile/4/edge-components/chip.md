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
@php $filterVerified = false; @endphp

<native:chip label="Verified" icon="check" native:model="filterVerified" />
```
@endverbatim

`filterVerified` is a public boolean property on your component — the `@php` line stands in for
`public bool $filterVerified = false;`.

## Props

- `label` - Chip text (optional, string). Can also be passed as the first argument to `make()`
- `selected` / `value` - Whether the chip is active (optional, boolean, default: `false`)
- `icon` - Leading [icon](icon#icon-name-reference) name (optional, string)
- `disabled` - Disable the chip (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when toggled. Receives the new boolean value

## Two-way Binding

`native:model` binds the selected state to a boolean property on your component:

@verbatim
```blade
@php $filterOnSale = false; @endphp

<native:chip label="On Sale" native:model="filterOnSale" />

<native:text class="text-sm text-theme-on-surface-variant">
    {{ $filterOnSale ? 'Showing sale items only' : 'Showing everything' }}
</native:text>
```
@endverbatim

Toggling the chip syncs the new boolean back to `filterOnSale` automatically, and anything that reads the
property — like the `@{{ $filterOnSale ? ... }}` echo above — re-renders with the new value.

## Examples

### Filter chip row

@verbatim
```blade
@php $filter = 'all'; @endphp

<native:row class="w-full gap-2 p-4">
    <native:chip label="All"     :selected="$filter === 'all'"     @change="setFilter('all')" />
    <native:chip label="Active"  :selected="$filter === 'active'"  @change="setFilter('active')" />
    <native:chip label="Archive" :selected="$filter === 'archive'" @change="setFilter('archive')" />
</native:row>
```
@endverbatim

Here `$filter` is a public string property and `setFilter()` is a public method on your component that assigns it —
driving `selected` from one property keeps the row single-select.

### With icon

@verbatim
```blade
@php $onlyVerified = false; @endphp

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
