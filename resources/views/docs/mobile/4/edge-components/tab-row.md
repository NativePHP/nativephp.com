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

Per Material 3, the active tab uses `theme.primary` and the underline is `theme.primary`. Inactive tabs use
`theme.onSurfaceVariant`.

@verbatim
```blade
@php $activeTab = 0; @endphp

<native:tab-row native:model="activeTab">
    <native:tab label="Recent" icon="history" />
    <native:tab label="Starred" icon="star" />
    <native:tab label="Archived" icon="archive-box" />
</native:tab-row>
```
@endverbatim

`activeTab` is a public int property on your component — the `@php` line stands in for
`public int $activeTab = 0;`.

## Props (Row)

- `value` / `selected-index` - Currently selected tab index (optional, int, default: `0`)
- `sync-mode` - How `native:model` writes the selected index back to your component (optional)
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when the selection changes. Receives the new index as a parameter

## Two-way Binding

`native:model` binds the selected index to an integer property on your component. Tapping a tab syncs the new
index back automatically:

@verbatim
```blade
@php $currentTab = 0; @endphp

<native:tab-row native:model="currentTab">
    <native:tab label="One" />
    <native:tab label="Two" />
    <native:tab label="Three" />
</native:tab-row>

<native:text class="text-sm text-theme-on-surface-variant">Selected: {{ ['One', 'Two', 'Three'][$currentTab] }}</native:text>
```
@endverbatim

Here `currentTab` stands in for `public int $currentTab = 0;` on your component.

## Children

`<native:tab>` declares a single tab. Each accepts:

- `label` - Tab label (required, string). Can also be passed as the first argument to `make()`
- `icon` - Optional [icon](icon#icon-name-reference) name rendered above the label
- `a11y-label` - Accessibility label override (optional)

## Examples

### Section switcher

@verbatim
```blade
@php $section = 0; @endphp

<native:column class="w-full">
    <native:tab-row native:model="section">
        <native:tab label="Overview" />
        <native:tab label="Activity" />
        <native:tab label="Members" />
    </native:tab-row>

    @if($section === 0)
        <native:column class="w-full p-4">
            <native:text class="text-theme-on-surface">Overview content</native:text>
        </native:column>
    @elseif($section === 1)
        <native:column class="w-full p-4">
            <native:text class="text-theme-on-surface">Activity content</native:text>
        </native:column>
    @else
        <native:column class="w-full p-4">
            <native:text class="text-theme-on-surface">Members content</native:text>
        </native:column>
    @endif
</native:column>
```
@endverbatim

`section` is a public int property on your component (`public int $section = 0;`). On a real screen you would
typically add `fill` to the outer column and the content panes so they occupy the remaining screen height.

### Tabs with icons

@verbatim
```blade
@php $filter = 0; @endphp

<native:tab-row native:model="filter">
    <native:tab label="All"      icon="list" />
    <native:tab label="Starred"  icon="star" />
    <native:tab label="Archived" icon="archive-box" />
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
    Tab::make('Archived')->icon('archive-box'),
)
    ->selectedIndex($activeTab)
    ->onChange('setActiveTab');
```

### `TabRow` methods

- `make(Element ...$children)` - Create a row with tab children
- `selectedIndex(int $index)` - Currently selected index
- `a11yLabel(string $value)` - Accessibility label
- `syncMode(string $mode)` - Set by `native:model` modifiers
- `onChange(string $method)` - Component method invoked on change

### `Tab` methods

- `make(string $label = '')` - Create a tab with a label
- `icon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` - Tab icon. Pass a shared [icon](icon#icon-name-reference) name, or override per platform with the `ios:` / `android:` named arguments
