---
title: Layouts
order: 160
---

## Overview

Layouts wrap the screens routed beneath them with shared chrome — a top nav bar, a bottom tab bar, or both — so
individual screens stay focused on their content.

The pattern is borrowed from Expo Router: a `NativeLayout` class declares what chrome to render, and the framework
automatically wraps every screen registered under that layout with the result. Push a detail screen onto a tabs
section and the chrome swaps from "tabs" to "stack" automatically; pop back and it swaps back.

## Attaching a layout to a route

Use `Route::native(...)->layout(...)` for a single screen, or `Route::nativeGroup(...)` for a set of screens that
share the same chrome.

```php
use App\NativeComponents\Browse;
use App\NativeComponents\Home;
use App\NativeComponents\ItemDetail;
use App\NativeComponents\Layouts\StackLayout;
use App\NativeComponents\Layouts\TabsLayout;
use App\NativeComponents\Profile;

// Three screens that share a tab-bar layout
Route::nativeGroup(TabsLayout::class, function () {
    Route::native('/tabs',         Home::class);
    Route::native('/tabs/browse',  Browse::class);
    Route::native('/tabs/profile', Profile::class);
});

// One screen with a stack-style top bar (back chevron + title)
Route::native('/item/{id}', ItemDetail::class)
    ->layout(StackLayout::class);
```

A screen with no layout renders without chrome — useful for splash, onboarding, or full-bleed views.

## Built-in layouts

NativePHP doesn't ship layouts in the framework — you write your own (they're tiny, see below). The sample app
includes two reference layouts you can copy as a starting point:

- `App\NativeComponents\Layouts\StackLayout` - Back chevron + screen title. No bottom tabs.
- `App\NativeComponents\Layouts\TabsLayout` - Title bar plus a 3-tab bottom nav.

## Writing a custom layout

Extend `Native\Mobile\Edge\Layouts\NativeLayout` and override `navBar()` and/or `tabBar()`. Returning `null` from a
method means "don't render that chrome."

```php
namespace App\NativeComponents\Layouts;

use Native\Mobile\Edge\Layouts\Builders\NavAction;
use Native\Mobile\Edge\Layouts\Builders\NavBar;
use Native\Mobile\Edge\Layouts\Builders\Tab;
use Native\Mobile\Edge\Layouts\Builders\TabBar;
use Native\Mobile\Edge\Layouts\NativeLayout;
use Native\Mobile\Edge\NativeComponent;

class SyncUpTabsLayout extends NativeLayout
{
    public function navBar(NativeComponent $screen): ?NavBar
    {
        return NavBar::make()
            ->title($screen->navTitle())
            ->subtitle('All caught up')
            ->back()
            ->backgroundColor('#0891b2')
            ->textColor('#FFFFFF')
            ->elevation(8)
            ->action(NavAction::make('search')->icon('search')->press('openSearch'));
    }

    public function tabBar(NativeComponent $screen): ?TabBar
    {
        return TabBar::make()
            ->dark()
            ->activeColor('#0891b2')
            ->labelVisibility('labeled')
            ->add(Tab::link('Chats',   '/syncup',          icon: 'chat_bubble')->badge('2'))
            ->add(Tab::link('Friends', '/syncup/friends',  icon: 'person.3.fill')->news())
            ->add(Tab::link('Profile', '/syncup/profile',  icon: 'person'));
    }
}
```

The `$screen` parameter is the live `NativeComponent` instance for the current screen, so the layout can read
properties or methods on it (such as `$screen->navTitle()`) to customize the chrome per screen.

See the [Top Bar](../edge-components/top-bar) and [Bottom Navigation](../edge-components/bottom-nav) pages for the
full builder API.

## How chrome wraps the screen

When a screen renders, the framework's `wrapWithChrome` flow:

1. Looks up the layout class declared on the route.
2. Calls `$layout->navBar($screen)` and `$layout->tabBar($screen)`.
3. Merges in any `navigationOptions()` declared on the screen.
4. Merges in any imperative state set via `$this->setNavBar([...])` / `$this->setTabBar([...])`.
5. Wraps the screen content in a `Column` filling the screen, with the bars stacked above and below the content.
6. Picks the right safe-area variant for the wrapper:
    - **TabBar present** → wrapper uses `safeAreaTop()`; the bar handles its own bottom inset.
    - **NavBar without TabBar** → wrapper uses `safeAreaBottom()`; the NavBar handles its own top inset.
    - **No bars** → wrapper uses `safeArea()` for both edges.

The resulting element tree looks like:

```
Column.fill().safeAreaTop()  ← (or whichever variant applies)
├─ TopBar          ← navBar
├─ <screen content> (flex-grow: 1)
└─ BottomNav       ← tabBar
```

Don't apply `safe-area` to the root of a screen wrapped by a layout — the layout already handles it.

## Per-screen NavBar contributions

Screens can add actions or override the title without writing their own layout, by implementing
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

Non-null fields on the returned `NavBarOptions` override the layout's defaults; null fields fall through. Actions
are appended to whatever the layout already declared.

## Per-screen titles

Override `navTitle()` to give a screen its own title that the layout's `NavBar` can read:

```php
class Profile extends NativeComponent
{
    public function navTitle(): string
    {
        return 'My Profile';
    }
}
```

A `StackLayout` that calls `->title($screen->navTitle())` will then show "My Profile" automatically when this screen
is on top.

## Imperative state changes

If you need to mutate the chrome at runtime — for example, to flip the title between "Edit" and "Done" — call
`$this->setNavBar([...])` or `$this->setTabBar([...])`:

```php
class Notes extends NativeComponent
{
    public bool $editing = false;

    public function toggleEdit(): void
    {
        $this->editing = ! $this->editing;

        $this->setNavBar([
            'title' => $this->editing ? 'Editing' : 'Notes',
        ]);
    }
}
```

Imperative state is merged onto the layout's NavBar at the next render. Supported keys mirror the `NavBar` builder
methods: `title`, `subtitle`, `back`, `backgroundColor`, `textColor`, `elevation`.

## Inline overrides

A screen can put its own `<native:top-bar>` or `<native:bottom-nav>` at the root of its blade, and the framework
will skip the layout-supplied chrome **for that slot only**. This is useful for one-off screens (e.g. a chat
detail with a custom titled top bar) without dropping the layout entirely.

@verbatim
```blade
<native:column class="w-full h-full">
    {{-- Override only the top bar — the layout's tab bar still renders --}}
    <native:top-bar :title="$thread->name" />

    <native:scroll-view class="w-full flex-1">
        {{-- ... --}}
    </native:scroll-view>
</native:column>
```
@endverbatim
