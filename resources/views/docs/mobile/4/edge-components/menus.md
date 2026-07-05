---
title: Menus
order: 275
---

## Overview

A menu is a tap-to-open dropdown (SwiftUI `Menu` / Compose `DropdownMenu`) attached to a component. It isn't a
standalone element — you build a list of actions and attach it with an attribute:

- `:menu` on a [button](button) or [pressable](pressable) — tapping opens the menu instead of firing `@press`.
- `:trailing-menu` on a [list item](list) — opens from the row's trailing edge.

All three share one item model: an array of `NavAction`, the same builder used for
[nav-bar menus](../super-native/layouts#builder-reference).

## Building the items

Each item is a `NavAction`. Give it an id, an [icon](icon), a label, and a `press()` handler; insert
`NavAction::divider()` for a separator and `->destructive()` to tint an item as dangerous:

```php
use Native\Mobile\Edge\Layouts\Builders\NavAction;

$menu = [
    NavAction::make('export_pdf')->icon('doc')->label('Export as PDF')->press('exportPdf'),
    NavAction::make('export_csv')->icon('tablecells')->label('Export as CSV')->press('exportCsv'),
    NavAction::divider(),
    NavAction::make('delete')->icon('trash')->label('Delete')->press('delete')->destructive(),
];
```

Build the array in your component (in `render()`, `mount()`, or a helper) and pass it to the attribute.

## Attaching to a button or pressable

@verbatim
```blade
{{-- Button: the menu replaces @press --}}
<native:button label="Export" :menu="$menu" />

{{-- Pressable: any content becomes the tap target --}}
<native:pressable :menu="$menu">
    <native:icon name="ellipsis" />
</native:pressable>
```
@endverbatim

When `:menu` is set, tapping opens the menu — it shadows the element's own `@press`.

## Attaching to a list item

Use `:trailing-menu` to open a menu from a row's trailing edge — a common pattern for per-row actions:

@verbatim
```blade
@foreach ($conversations as $chat)
    <native:list-item
        headline="{{ $chat->name }}"
        supporting="{{ $chat->preview }}"
        :trailing-menu="$rowMenu"
    />
@endforeach
```
@endverbatim

## Item reference

The menu-relevant `NavAction` methods:

- `make(string $id)` — create an item with a unique id
- `icon(?string $name = null, ios:, android:)` — a leading [icon](icon)
- `label(string)` — the row text
- `press(string $method)` — the component method to call when chosen
- `url(string)` — navigate to a URL instead of calling a method
- `destructive(bool = true)` — tint the item as destructive
- `NavAction::divider()` — a separator row

See the [`NavAction` builder](../super-native/layouts#builder-reference) for the full list.
