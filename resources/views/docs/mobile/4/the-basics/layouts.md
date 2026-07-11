---
title: Layouts
order: 154
---

## Overview

Layouts wrap the screens routed beneath them with shared chrome — a top nav bar, a bottom tab bar, or both — so
individual screens stay focused on their content.

A `NativeLayout` class declares which chrome to render, and the framework automatically wraps every screen registered
under that layout with the result. Push a detail screen onto a tabs section and the chrome swaps from "tabs" to "stack"
automatically; pop back and it swaps back.

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

If you use the `nativephp/native-ui` plugin, its `native-ui-layouts` publish tag scaffolds a starter `<x-layouts.app>`
Blade component that wraps a screen's content with safe-area handling and optional scrolling — copy it to
`feed.blade.php`, `detail.blade.php`, and so on for multiple page archetypes.

```bash
php artisan vendor:publish --tag=native-ui-layouts
```

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

## Builder reference

The chrome is described entirely with these fluent builders — you never place a top bar or tab bar as an element
in a screen's Blade.

### `NavBar` — the top bar

- `make()` — create a builder
- `title(?string)` / `subtitle(?string)` — title and the small line under it
- `titleView(Element|View)` — render a custom element or Blade view in the bar's centered slot instead of the string title (a logo lockup, wordmark, …)
- `logo(string $src, float $height = 28)` — convenience over `titleView()` for a bundled logo image
- `back(bool $show = true)` — show the back chevron
- `backgroundColor(string)` / `textColor(string)` — bar background and title/icon tint
- `elevation(int $px)` — hairline thickness at the bottom of the bar
- `displayMode(string)` — `large`, `inline`, or `automatic`
- `scrollBehavior(string)` — `collapse`, `pinned`, or `enterAlways`
- `searchBar(string $placeholder = '', ?string $onQuery = null, int $debounceMs = 300)` — attach a native search bar (see [Search](../digging-deeper/search))
- `action(NavAction $action)` — append a trailing action

### `NavAction` — a top-bar button or menu

- `make(string $id)` — create an action with a unique id
- `icon(?string $name = null, ios:, android:)` — a named [icon](../edge-components/icon), optionally per-platform
- `label(string)` — visible/overflow label; `a11yLabel(string)` — screen-reader label for icon-only actions
- `press(string $method)` — screen method to call when tapped
- `url(string)` — navigate to a URL when tapped; `event(string)` — dispatch a native event (advanced)
- `destructive(bool = true)` — render in the destructive tint
- `items(array $actions)` — nest `NavAction`s to render a pull-down menu; `NavAction::divider()` adds a separator

#### Custom title view / logo

Use `logo()` for the common case — a bundled brand image in place of the string title — or `titleView()` for any
element tree or Blade view:

```php
public function navBar(NativeComponent $screen): ?NavBar
{
    return NavBar::make()
        ->logo('images/logo.png');                       // bundled asset, ~28pt tall
        // ->titleView(Image::make('images/logo.png')->height(24))
        // ->titleView(view('native.brand-lockup'))
}
```

<aside>

A custom title view takes over the bar's centered slot and forces **inline** display mode, replacing the string
title entirely. Bundle the image as an app asset rather than a remote URL so the bar doesn't flash while it loads.
On iOS it renders as a `.principal` toolbar item; on Android it fills the `TopAppBar` title slot.

</aside>

### `TabBar` — the bottom tabs

- `make()` — create a builder; `add(Tab $tab)` — append a tab (up to 5)
- `activeColor(string)` / `backgroundColor(string)` / `textColor(string)` — tab colors
- `labelVisibility(string)` — `labeled`, `selected`, or `unlabeled`
- `dark(bool = true)` — force dark styling
- `minimizeOnScroll(bool = true)` — shrink the bar as content scrolls (iOS 26)
- `highlight(string $currentUrl)` — mark the active tab by longest-prefix URL match

### `Tab` — a tab item

- `Tab::link(string $label, string $url, icon:, ios:, android:)` — a navigating tab
- `Tab::action(string $label, icon:)` — a tab that calls a method instead of navigating
- `Tab::search(string $label, icon:, placeholder:)` — a tab that presents a search bar
- `id(string)`, `press(string $method)`, `badge(string $text, ?string $color = null)`, `news(bool = true)` (red dot), `active(bool = true)`

## Drawer navigation

For a slide-out side drawer, mix the native-ui `HasLayoutDrawer` trait into your layout and return a `Drawer` from
`drawer()`. The content is any Blade view, so you build the drawer's UI with normal EDGE components:

```php
use Nativephp\NativeUi\Builders\Drawer;
use Nativephp\NativeUi\Concerns\HasLayoutDrawer;

class AppLayout extends NativeLayout
{
    use HasLayoutDrawer;

    public function drawer(NativeComponent $screen): ?Drawer
    {
        return Drawer::make(view('native.sidebar'))
            ->width(320)
            ->reveal();   // ->modal() (dim + slide over) is the default
    }
}
```

### `Drawer` builder

- `make(View|Element $content)` — the drawer's content: a Blade view, or a pre-built element tree
- `width(int $points)` — drawer width in points/dp. Omit it for the platform default: ≈85% of the screen width in portrait, 40% in landscape
- `modal()` — slide the drawer over the content with a dim scrim (default)
- `reveal()` — push the main content aside to expose the drawer behind it

### Built-in interaction

The drawer is fully interactive without any wiring on your part:

- A ☰ hamburger affordance is drawn automatically at the top-leading corner while the drawer is closed, regardless of which chrome (nav bar, tab bar, or none) the screen uses.
- An edge-swipe from the left opens it; a drag back toward the edge or a tap on the scrim closes it.
- An open drawer closes automatically when you navigate to a screen whose layout has no drawer, so it never lingers over a screen that doesn't expect it.

### Per-screen drawer overrides

A single screen can replace or suppress the layout's drawer with the `InteractsWithDrawer` trait — the same shape as `navigationOptions()` for the nav bar:

```php
use Nativephp\NativeUi\Builders\Drawer;
use Nativephp\NativeUi\Concerns\InteractsWithDrawer;

class AdminScreen extends NativeComponent
{
    use InteractsWithDrawer;

    // Suppress the layout's drawer entirely on this screen…
    protected bool $hidesDrawer = true;

    // …or replace it just for this screen instead:
    public function drawerOverride(): ?Drawer
    {
        return Drawer::make(view('native.admin-sidebar'))->reveal();
    }
}
```

`hidesDrawer` wins outright; otherwise a non-null `drawerOverride()` beats the layout's `drawer()`, and returning `null` falls back to the layout.

## Keyboard-aware bottom content

Beyond the tab bar, a layout or an individual screen can pin its own content to the bottom of the screen — a chat
input, a search field, a contextual action bar. This content **stays above the software keyboard automatically**:
on iOS via `.safeAreaInset(.bottom)`, on Android via the `Scaffold` bottom bar plus `imePadding()`. The main
content region sits above it and keeps its own scroll.

The most ergonomic way is an inline `<native:bottom-bar>` at the root of a screen's Blade. It's lifted out of the
content flow and pinned, and it overrides the layout's `bottomBar()` for that screen:

@verbatim
```blade
<native:column class="w-full h-full">
    <native:scroll-view class="w-full flex-1">
        @foreach($messages as $message)
            <native:text class="p-3">{{ $message->body }}</native:text>
        @endforeach
    </native:scroll-view>

    {{-- Pinned to the bottom, riding above the keyboard while typing --}}
    <native:bottom-bar class="p-2 glass">
        <native:row class="w-full gap-2 items-center">
            <native:outlined-text-input native:model="draft" placeholder="Message…" class="flex-1" />
            <native:button label="Send" @press="send" />
        </native:row>
    </native:bottom-bar>
</native:column>
```
@endverbatim

To pin the same content across every screen under a layout, override `bottomBar()` instead — it returns any
element tree:

```php
public function bottomBar(NativeComponent $screen): ?Element
{
    // Return an element tree, or null for no bottom bar.
    // An inline <native:bottom-bar> on a screen overrides this for that screen.
}
```

Style the bar with the `glass` / `glass-thick` classes for a Liquid Glass capsule. Bottom-pinned content is only
rendered by layouts using native chrome (`usesNativeChrome()` is `true`).

<aside>

Let the bottom bar handle keyboard avoidance — don't add manual padding or shift the screen yourself. The platform
lifts the bar (and the content above it) when the keyboard appears; doubling that up with your own padding pushes
the content too far.

</aside>

## Floating overlay

For a pill or banner that **floats over** every screen — a "servers nearby" chip, a now-playing capsule, a
sync-status badge — mix the native-ui `HasFloatingOverlay` trait into your layout and return a `FloatingOverlay`
from `floatingOverlay()`. Unlike `bottomBar()`, it does **not** inset the content: it hovers on a top layer above
the content and the tab bar, so nothing is pushed up. Return `null` and nothing floats.

The content is any element tree or Blade view, so you build the overlay's UI with normal EDGE components:

```php
use Nativephp\NativeUi\Builders\FloatingOverlay;
use Nativephp\NativeUi\Concerns\HasFloatingOverlay;

class AppLayout extends NativeLayout
{
    use HasFloatingOverlay;

    public function floatingOverlay(NativeComponent $screen): ?FloatingOverlay
    {
        if (NearbyServers::none()) {
            return null;                                  // nothing floats
        }

        return FloatingOverlay::make(view('native.servers-pill'))
            ->offset(88);          // clearance above the tab bar; ->top() pins below the nav bar instead
    }
}
```

<aside>

Because it floats over **every** screen under the layout, the overlay's content should read from app-wide state (a
store / singleton), not the active screen's own properties — `floatingOverlay()` is re-evaluated on each publish
from the current `$screen`. A single screen can replace or suppress it with the `InteractsWithFloatingOverlay`
trait. Only rendered by layouts using native chrome (`usesNativeChrome()` is `true`).

</aside>

### `FloatingOverlay` builder

- `make(View|Element $content)` — the floating content: a Blade view, or a pre-built element tree
- `bottom()` — float against the bottom edge, above the tab bar (default)
- `top()` — float against the top edge, below the nav bar
- `offset(int $points)` — extra distance between the overlay and its aligned edge, added on top of the safe-area inset. Omit it for the platform default that clears a standard bottom tab bar; a tab-less stack layout has no tab bar to clear, so pass a small value there instead

### Per-screen overlay overrides

A single screen replaces or suppresses the layout's overlay with the `InteractsWithFloatingOverlay` trait:

```php
use Nativephp\NativeUi\Builders\FloatingOverlay;
use Nativephp\NativeUi\Concerns\InteractsWithFloatingOverlay;

class CheckoutScreen extends NativeComponent
{
    use InteractsWithFloatingOverlay;

    // Hide the app-wide pill on this screen…
    protected bool $hidesFloatingOverlay = true;

    // …or replace it just for this one instead:
    public function floatingOverlayOverride(): ?FloatingOverlay
    {
        return FloatingOverlay::make(view('native.checkout-hint'));
    }
}
```

`hidesFloatingOverlay` wins outright; otherwise a non-null `floatingOverlayOverride()` beats the layout's `floatingOverlay()`, and returning `null` falls back to the layout.

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
