---
title: Top Bar
order: 50
---

## Overview
<div class="images-two-up not-prose">

![](/img/docs/edge-top-bar-ios.png)

![](/img/docs/edge-top-bar-android.png)

</div>

A top bar with title, subtitle, and action buttons. This renders at the top of the screen.

@verbatim
```blade
<native:top-bar title="Dashboard" subtitle="Welcome back">
    <native:top-bar-action
        id="search"
        label="Search"
        icon="search"
        :url="route('search')"
    />
    <native:top-bar-action
        id="settings"
        icon="settings"
        label="Settings"
        url="https://yourapp.com/my-account"
    />
</native:top-bar>
```
@endverbatim

## Props

- `title` - The title text (required)
- `subtitle` - A small line under the title (optional)
- `show-navigation-icon` - Show the back chevron (optional, default: `true`)
- `background-color` - Background color. Hex string (optional)
- `text-color` - Title and icon color. Also flows to `<native:top-bar-action>` icons (optional)
- `elevation` - Hairline thickness at the bottom of the bar in dp (optional)

<aside>

`elevation` renders as a thin hairline at the bottom of the bar — not a SwiftUI shadow. SwiftUI shadows are obscured
by sibling content in a flex column, so a hairline is used instead. Set to `0` to disable.

</aside>

## Children

A `<native:top-bar>` can contain up to 10 `<native:top-bar-action>` elements. These are displayed on the trailing
edge of the bar.

On Android, the first 3 actions are shown as icon buttons; additional actions collapse into an overflow menu (⋮).
On iOS, if more than 5 actions are provided, they collapse into an overflow menu.

### `<native:top-bar-action>` Props

- `id` - Unique identifier (required)
- `icon` - A named [icon](icons) (required)
- `label` - Text label for the action. Used for accessibility and displayed in overflow menus (optional but recommended)
- `url` - A URL to navigate to when tapped (optional)

<aside>

Any `url` that doesn't match a registered native route will exit to the web view and load that URL there.

</aside>

## Builder API

When a `<native:top-bar>` is supplied by a [layout](../the-basics/layouts), you build it fluently with the `NavBar`
and `NavAction` builders rather than writing it in Blade.

```php
use Native\Mobile\Edge\Layouts\Builders\NavAction;
use Native\Mobile\Edge\Layouts\Builders\NavBar;

NavBar::make()
    ->title('SyncUp')
    ->subtitle('All caught up')
    ->back()
    ->backgroundColor('#0891b2')
    ->textColor('#FFFFFF')
    ->elevation(8)
    ->action(NavAction::make('search')->icon('search')->press('openSearch'));
```

### `NavBar` methods

- `make()` - Create a new builder
- `title(?string $title)` - Title text
- `subtitle(?string $subtitle)` - Small line under the title
- `back(bool $show = true)` - Show the back chevron
- `backgroundColor(string $color)` - Bar background color
- `textColor(string $color)` - Title and icon tint
- `elevation(int $px)` - Hairline thickness at the bottom of the bar
- `action(NavAction $action)` - Append a trailing action

### `NavAction` methods

- `make(string $id)` - Create an action with a unique id
- `icon(string $icon)` - A named [icon](icons)
- `label(string $label)` - Accessibility / overflow-menu label
- `url(string $url)` - A URL to navigate to when tapped
- `press(string $method)` - A Livewire-style method on the screen to invoke when tapped
- `event(string $event)` - A native event name to dispatch (advanced)

## Per-screen overrides

Screens can contribute additional NavBar actions on top of what their layout supplies by overriding
`navigationOptions()`:

```php
use Native\Mobile\Edge\Layouts\Builders\NavAction;
use Native\Mobile\Edge\Layouts\Builders\NavBarOptions;
use Native\Mobile\Edge\NativeComponent;

class ItemDetail extends NativeComponent
{
    public function navigationOptions(): ?NavBarOptions
    {
        return NavBarOptions::make()
            ->title("Item #{$this->param('id')}")
            ->action(NavAction::make('save')->icon('save')->press('save'));
    }

    public function save(): void
    {
        // ...
    }
}
```

See [Layouts](../the-basics/layouts) for the full picture.
