---
title: Menus
order: 275
---

## Overview

A menu is a tap-to-open dropdown (SwiftUI `Menu` / Compose `DropdownMenu`) attached to a component. It isn't a
standalone element — you build a list of actions and attach it with an attribute:

- `:menu` on a [button](button) or [pressable](pressable) — tapping opens the menu instead of firing `@press`.
- `:trailing-menu` on a [list item](list) — opens from the row's trailing edge.
- `items()` on a nav-bar action — see [Menus in the nav bar](#menus-in-the-nav-bar) below.

All of them share one item model: an array of `NavAction`, the same builder used
[throughout layouts](../the-basics/layouts#builder-reference). Nav-bar menus can also be grouped into
[sections, rows and submenus](#sections-rows-and-submenus).

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

## Menus in the nav bar

The same `NavAction` builder powers pull-down menus on nav-bar (top bar) actions. Give an action sub-items
with `items()` and it renders as a menu that drops from the bar button instead of firing a plain press:

```php
use Native\Mobile\Edge\Layouts\Builders\NavAction;
use Native\Mobile\Edge\Layouts\Builders\NavBarOptions;

public function navigationOptions(): ?NavBarOptions
{
    return NavBarOptions::make()
        ->action(
            NavAction::make('share')
                ->icon('share')
                ->a11yLabel('Share')
                ->press('share')
        )
        ->action(
            NavAction::make('more')
                ->icon('ellipsis')
                ->a11yLabel('More options')
                ->items([
                    NavAction::make('mute')
                        ->label('Mute')
                        ->icon(ios: 'bell.slash', android: 'notifications_off')
                        ->press('mute'),
                    NavAction::make('pin')->label('Pin')->icon('pin')->press('pin'),
                    NavAction::divider(),
                    NavAction::make('delete')
                        ->label('Delete')
                        ->icon('trash')
                        ->press('delete')
                        ->destructive(),
                ])
        );
}
```

Here `share` stays a plain press, while `more` opens a native pull-down (SwiftUI `Menu` / Compose `DropdownMenu`)
with dividers and destructive tinting, exactly like the inline menus above. This works in both stack and tab
layouts — you can also attach it directly in a layout definition via `NavBar::make()->action(...)` in your
layout's `navBar()`.

> **Note** Menus are only supported on nav-bar actions. Bottom-nav / tab-bar items and a
> [`custom`](top-bar#custom-bars) `<native:top-bar>` render their actions as plain buttons, so `items()` is ignored
> there. An inline `<native:top-bar>` without `custom` takes a menu as nested `<native:top-bar-action>` elements; see
> [Menus in Blade](#menus-in-blade).

## Sections, rows and submenus

<x-docs.version-badge since="4.8" />

A nav-bar menu doesn't have to be a flat list. You can group items into titled sections, put a compact row of
buttons across the top, nest a submenu, and give any item a subtitle or a checkmark. This menu is laid out like the
one behind the ⋯ button in Apple's Notes app:

```php
use Native\Mobile\Edge\Layouts\Builders\NavAction;
use Native\Mobile\Edge\Layouts\Builders\NavBarOptions;

public function navigationOptions(): ?NavBarOptions
{
    return NavBarOptions::make()->action(
        NavAction::make('more')
            ->icon('ellipsis')
            ->a11yLabel('More')
            ->items([
                NavAction::row([
                    NavAction::make('scan')
                        ->label('Scan')
                        ->icon(ios: 'doc.viewfinder', android: 'document_scanner')
                        ->press('scan'),
                    NavAction::make('pin')
                        ->label($this->pinned ? 'Unpin' : 'Pin')
                        ->icon('pin')
                        ->press('togglePin'),
                    NavAction::make('lock')->label('Lock')->icon('lock')->press('lock'),
                ]),
                NavAction::section(items: [
                    NavAction::make('find')->label('Find in Note')->icon('search')->press('find'),
                    NavAction::make('move')
                        ->label('Move Note')
                        ->subtitle($this->folder)
                        ->icon('folder')
                        ->press('move'),
                ]),
                NavAction::section('View', [
                    NavAction::make('paper')
                        ->label('Lines & Grids')
                        ->icon(ios: 'squareshape.split.3x3', android: 'grid_on')
                        ->items([
                            NavAction::make('paper_none')
                                ->label('None')
                                ->checked($this->paper === 'none')
                                ->press("setPaper('none')"),
                            NavAction::make('paper_lines')
                                ->label('Lines')
                                ->checked($this->paper === 'lines')
                                ->press("setPaper('lines')"),
                            NavAction::make('paper_grid')
                                ->label('Grid')
                                ->checked($this->paper === 'grid')
                                ->press("setPaper('grid')"),
                        ]),
                    NavAction::make('attachments')
                        ->label('Show Attachments')
                        ->icon(ios: 'paperclip', android: 'attach_file')
                        ->checked($this->showAttachments)
                        ->press('toggleAttachments'),
                ]),
                NavAction::section(items: [
                    NavAction::make('delete')->label('Delete')->icon('trash')->press('delete')->destructive(),
                ]),
            ])
    );
}
```

Here's what each piece does:

- `NavAction::row([...])` puts its items side by side across the top of the menu, each with its icon above a short
  label. A row fits a few items, so keep the labels short.
- `NavAction::section('View', [...])` groups its items under a heading. Use `NavAction::section(items: [...])` for a
  group with no heading. It still gets separated from the items around it.
- An item with its own `items()` is a submenu. `Lines & Grids` above opens a submenu of paper styles.
- `subtitle()` adds a second, smaller line under an item's label. Here it shows the folder the note is in.
- `checked()` puts a checkmark on an item. Pass it a boolean from your screen's state, like the paper styles do, and
  the checkmark moves when the state changes.

Rows and sections are containers, not tappable items. Their ids are fixed placeholders (`row` and `section`, like
`divider` for a divider), and they ignore `icon()`, `press()` and the other item methods. Give each item inside them
its own unique id.

### How they look on each platform

On iOS, the menu is a native `UIMenu`. Sections are drawn inline, with the title as a small heading above the group
and a separator line between groups. The row uses UIKit's medium element size: each item is a small button with its
icon above its label, the same as the Scan, Pin and Lock buttons in Notes. A submenu shows a chevron and opens in
place over the menu it came from. Checked items get the system checkmark, and destructive items are red.

On Android, the menu is a Material dropdown. A divider separates each section from the items around it, and the
section's title shows as a small heading. The row is a line of buttons, each with an icon and a label. Tapping a
submenu replaces the dropdown's items with the submenu's, with a back row at the top to return to the previous
level. Checked items show a check mark at the end of the item, and a subtitle sits under the label in a smaller,
quieter style.

### Menus in Blade

An inline [`<native:top-bar>`](top-bar) builds the same menu from nested `<native:top-bar-action>` elements. Add
`section` (with an optional `label` for its heading) or `row` to an action to make it a container, and nest actions
inside an item to make a submenu. `subtitle` and `checked` are attributes like any other:

@verbatim
```blade
<native:top-bar title="Note">
    <native:top-bar-action id="more" icon="ellipsis" a11y-label="More">
        <native:top-bar-action row>
            <native:top-bar-action id="scan" label="Scan" icon="document_scanner" ios-icon="doc.viewfinder" @tap="scan" />
            <native:top-bar-action id="pin" :label="$pinned ? 'Unpin' : 'Pin'" icon="pin" @tap="togglePin" />
            <native:top-bar-action id="lock" label="Lock" icon="lock" @tap="lock" />
        </native:top-bar-action>

        <native:top-bar-action section label="View">
            <native:top-bar-action id="paper" label="Lines & Grids" icon="grid_on" ios-icon="squareshape.split.3x3">
                <native:top-bar-action id="paper_none" label="None" :checked="$paper === 'none'" @tap="setPaper('none')" />
                <native:top-bar-action id="paper_lines" label="Lines" :checked="$paper === 'lines'" @tap="setPaper('lines')" />
                <native:top-bar-action id="paper_grid" label="Grid" :checked="$paper === 'grid'" @tap="setPaper('grid')" />
            </native:top-bar-action>
        </native:top-bar-action>

        <native:top-bar-action section>
            <native:top-bar-action id="delete" label="Delete" icon="trash" destructive @tap="delete" />
        </native:top-bar-action>
    </native:top-bar-action>
</native:top-bar>
```
@endverbatim

<aside>

Sections, rows, submenus, subtitles and checkmarks are drawn in nav-bar menus. Menus attached with `:menu` or
`:trailing-menu` still draw a flat list, so keep those to plain items and `NavAction::divider()`.

</aside>

## Item reference

The menu-relevant `NavAction` methods:

- `make(string $id)` - create an item with a unique id
- `icon(?string $name = null, ios:, android:)` - a leading [icon](icon)
- `label(string)` - the item's text
- `subtitle(string)` - a second, smaller line under the label (nav-bar menus)
- `press(string $method)` - the component method to call when chosen
- `url(string)` - navigate to a URL instead of calling a method
- `items(array $items)` - sub-items that turn a nav-bar action into a pull-down menu, or an item inside one into a
  submenu (nav-bar actions only)
- `a11yLabel(string)` - screen-reader label for icon-only actions
- `destructive(bool = true)` - tint the item as destructive
- `checked(bool = true)` - show a checkmark on the item (nav-bar menus)
- `NavAction::divider()` - a separator row
- `NavAction::section(?string $title = null, array $items = [])` - an inline group of items under an optional heading
  (nav-bar menus)
- `NavAction::row(array $items)` - a compact row of icon-over-label buttons (nav-bar menus)

See the [`NavAction` builder](../the-basics/layouts#builder-reference) for the full list.
