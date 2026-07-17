---
title: Top Bar
order: 450
---

> [!IMPORTANT]
> **Prefer the [Layout model](../the-basics/layouts).** Declare your app's top bar with the `NavBar`
> builder in a `NativeLayout` class rather than placing `<native:top-bar>` in a screen. This page documents the
> inline element, which still works but is no longer the recommended approach.

## Overview
<div class="images-two-up not-prose">

![](/img/docs/edge-top-bar-ios.png)

![](/img/docs/edge-top-bar-android.png)

</div>

A top bar with title, subtitle, and action buttons. This renders at the top of the screen.

@verbatim
```blade static
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

> [!IMPORTANT]
> In the [Layout model](../the-basics/layouts#builder-reference) a top-bar action is a `NavAction` — use that
> builder rather than placing `<native:top-bar-action>` inline.

- `id` - Unique identifier (required)
- `icon` - A named [icon](icon#icon-name-reference) (required)
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
- `icon(string $icon)` - A named [icon](icon#icon-name-reference)
- `label(string $label)` - Accessibility / overflow-menu label
- `url(string $url)` - A URL to navigate to when tapped
- `press(string $method)` - A component method on the screen to invoke when tapped
- `event(string $event)` - A native event name to dispatch (advanced)

## Per-screen overrides

Screens can override the title, colors, display behavior, and add actions on top of what their layout supplies by
overriding `navigationOptions()`:

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

### `NavBarOptions` methods

Non-null fields override the layout's `NavBar`; null fields fall through. `action()` appends to whatever the layout
already declared.

- `make()` - Create a new builder
- `hidden(bool $hidden = true)` - Hide the nav bar on this screen — the full-bleed / immersive pattern
- `title(?string $title)` - Override the title text
- `subtitle(?string $subtitle)` - Override the small line under the title
- `back(bool $show = true)` - Show or hide the back chevron
- `backgroundColor(string $color)` - Bar background color
- `textColor(string $color)` - Title and icon tint
- `elevation(int $px)` - Hairline thickness at the bottom of the bar
- `displayMode(string $mode)` - Title display mode — `large`, `inline`, or `automatic`
- `scrollBehavior(string $mode)` - How the bar reacts to content scrolling — `collapse`, `pinned`, or `enterAlways`
- `searchBar(string $placeholder = '', ?string $onQuery = null, int $debounceMs = 300)` - Attach an inline native search field; text changes call the `$onQuery` method on the screen
- `action(NavAction $action)` - Append a trailing action

### Hiding the nav bar on a screen

Individual screens can opt out of their layout's nav bar entirely — the full-bleed pattern for photo viewers,
onboarding flows, and video screens. This is the top-bar parallel to the tab bar's
[`hidden()`](bottom-nav#per-screen-tab-bar):

```php
use Native\Mobile\Edge\Layouts\Builders\NavBarOptions;
use Native\Mobile\Edge\NativeComponent;

class PhotoViewer extends NativeComponent
{
    public function navigationOptions(): ?NavBarOptions
    {
        return NavBarOptions::make()->hidden();
    }
}
```

For this common case, the shorter `protected bool $hidesNavBar = true;` property on the screen is equivalent to
`NavBarOptions::make()->hidden()`. Use either; if both are set, the explicit builder wins.

```php
class PhotoViewer extends NativeComponent
{
    protected bool $hidesNavBar = true;
}
```

Navigation keeps working while the bar is hidden — pushes, pops, and `@navigate` all behave normally. With the bar
hidden the screen is full-bleed: on Android the content extends up to the very top edge, under the transparent
status bar; use the safe-area utilities on elements that should stay clear of the clock and status icons. On iOS
the edge-swipe-back gesture is tied to the visible bar, so a pushed screen that hides it should render its own back
control (for example a floating button that uses `@navigate` to return to the parent screen).

See [Layouts](../the-basics/layouts) for the full picture.
