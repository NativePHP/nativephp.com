---
title: Menus
order: 275
---

## Overview

A menu is a dropdown (SwiftUI `Menu` / Compose `DropdownMenu`) attached to a component. It isn't a
standalone element — you build a list of actions and attach it with an attribute:

- `:menu` on a [button](button) or [pressable](pressable) — tapping opens the menu instead of firing `@press`.
- `:context-menu` on a [pressable](pressable) — a long press opens the menu, and a tap still fires `@press`. See
  [Opening on a long press](#opening-on-a-long-press) below.
- `:trailing-menu` on a [list item](list) — opens from the row's trailing edge.
- `items()` on a nav-bar action — see [Menus in the nav bar](#menus-in-the-nav-bar) below.

All of them share one item model: an array of `NavAction`, the same builder used
[throughout layouts](../the-basics/layouts#builder-reference).

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

## Opening on a long press

<x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" />

`:menu` takes over the tap, which doesn't suit a row in a list, where a tap should open the row. Use `:context-menu`
on a pressable instead. It takes the same items, opens them when the row is pressed and held, and leaves the tap to
`@press`:

@verbatim
```blade
@foreach ($conversations as $chat)
    <native:pressable
        @press="open({{ $chat->id }})"
        :context-menu="$this->rowMenu($chat)"
        class="w-full px-4 py-3"
    >
        <native:text class="text-base font-medium text-theme-on-surface">{{ $chat->name }}</native:text>
        <native:text class="text-sm text-theme-on-surface-variant">{{ $chat->preview }}</native:text>
    </native:pressable>
@endforeach
```
@endverbatim

Each row needs its own items, so build them in a helper on your component that puts the row's id into each handler:

```php
use App\Models\Conversation;
use Native\Mobile\Edge\Layouts\Builders\NavAction;

protected function rowMenu(Conversation $chat): array
{
    return [
        NavAction::make('pin')->icon('pin')->label('Pin')->press("pin({$chat->id})"),
        NavAction::make('mute')
            ->icon(ios: 'bell.slash', android: 'notifications_off')
            ->label('Mute')
            ->press("mute({$chat->id})"),
        NavAction::divider(),
        NavAction::make('delete')->icon('trash')->label('Delete')->press("delete({$chat->id})")->destructive(),
    ];
}
```

Each platform draws its own context menu. On iOS it's SwiftUI's `.contextMenu`, so the row lifts, the screen behind
it dims and the haptic plays. On Android the same dropdown as `:menu` opens where the finger is, with the long-press
haptic. TalkBack offers it as the row's long-press action.

A few things to keep in mind:

- Don't put `@longPress` on the same pressable. They both want the same gesture.
- A pressable takes `:menu` or `:context-menu`, not both. Setting both throws an exception.
- There are no submenus. As with the other inline menus, `items()` only works on nav-bar actions.

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

> **Note** Menus are only supported on nav-bar actions. Bottom-nav / tab-bar items and the inline
> `<native:top-bar>` element render their actions as plain buttons — `items()` is ignored there.

## Item reference

The menu-relevant `NavAction` methods:

- `make(string $id)` — create an item with a unique id
- `icon(?string $name = null, ios:, android:)` — a leading [icon](icon)
- `label(string)` — the row text
- `press(string $method)` — the component method to call when chosen
- `url(string)` — navigate to a URL instead of calling a method
- `items(array $items)` — sub-items that turn a nav-bar action into a pull-down menu (nav-bar actions only)
- `a11yLabel(string)` — screen-reader label for icon-only actions
- `destructive(bool = true)` — tint the item as destructive
- `NavAction::divider()` — a separator row

See the [`NavAction` builder](../the-basics/layouts#builder-reference) for the full list.
