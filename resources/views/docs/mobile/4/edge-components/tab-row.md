---
title: Tab Row
order: 410
---

## Overview

A horizontal tab strip with an underline indicator on the selected tab. Scrollable when tabs overflow. The row
owns the selected-index state.

Distinct from [`<native:bottom-nav>`](bottom-nav) — bottom nav is your app's primary navigation chrome with full
URL routing, while `<native:tab-row>` is an in-screen sectioning control whose tabs swap content within the same
screen.

Per Model 3, the active tab uses `theme.primary` and the underline is `theme.primary`. Inactive tabs use
`theme.onSurfaceVariant`.

@verbatim
```blade
<native:tab-row native:model="activeTab">
    <native:tab label="Recent" icon="history" />
    <native:tab label="Starred" icon="star" />
    <native:tab label="Archived" icon="archive" />
</native:tab-row>
```
@endverbatim

## Props (Row)

- `value` / `selected-index` - Currently selected tab index (optional, int, default: `0`)
- `a11y-label` - Accessibility label (optional)

## Events

- `@change` - Livewire method called when the selection changes. Receives the new index as a parameter

## Two-way Binding

`native:model` binds the selected index to a Livewire integer property:

@verbatim
```blade
<native:tab-row native:model="activeTab">
    <native:tab label="One" />
    <native:tab label="Two" />
    <native:tab label="Three" />
</native:tab-row>
```
@endverbatim

## Children

`<native:tab>` declares a single tab. Each accepts:

- `label` - Tab label (required, string). Can also be passed as the first argument to `make()`
- `icon` - Optional [icon](icons) name rendered above the label
- `a11y-label` - Accessibility label override (optional)

## Examples

### Section switcher

@verbatim
```blade
<native:column fill>
    <native:tab-row native:model="section">
        <native:tab label="Overview" />
        <native:tab label="Activity" />
        <native:tab label="Members" />
    </native:tab-row>

    @if($section === 0)
        <native:column fill :padding="16">
            <native:text>Overview content</native:text>
        </native:column>
    @elseif($section === 1)
        <native:column fill :padding="16">
            <native:text>Activity content</native:text>
        </native:column>
    @else
        <native:column fill :padding="16">
            <native:text>Members content</native:text>
        </native:column>
    @endif
</native:column>
```
@endverbatim

### Tabs with icons

@verbatim
```blade
<native:tab-row native:model="filter">
    <native:tab label="All"      icon="list" />
    <native:tab label="Starred"  icon="star" />
    <native:tab label="Archived" icon="archive" />
</native:tab-row>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\TabRow;
use Nativephp\NativeUi\Elements\Tab;

TabRow::make(
    Tab::make('Recent')->icon('history'),
    Tab::make('Starred')->icon('star'),
    Tab::make('Archived')->icon('archive'),
)
    ->selectedIndex($activeTab)
    ->onChange('setActiveTab');
```

### `TabRow` methods

- `make(Element ...$children)` - Create a row with tab children
- `selectedIndex(int $index)` - Currently selected index
- `a11yLabel(string $value)` - Accessibility label
- `syncMode(string $mode)` - Set by `native:model` modifiers
- `onChange(string $method)` - Livewire method invoked on change

### `Tab` methods

- `make(string $label = '')` - Create a tab with a label
- `icon(string $icon)` - Tab icon
