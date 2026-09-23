---
title: Navigation & Flows
order: 400
---

## Overview

Screens navigate: a tap pushes a detail screen, a save replaces the current one, a back gesture pops. The testing
suite lets you assert on those intentions, and — when you want to keep going — follow them onto the next screen and
walk a whole flow, with each screen's state preserved exactly as it is on device.

## Asserting on navigation

After an interaction, assert on where the screen intends to go:

- `assertNavigatedTo($uri)` — a forward push to `$uri`.
- `assertReplacedWith($uri)` — the current screen was replaced with `$uri`.
- `assertWentBack()` — a back navigation.
- `assertExitedToWeb($uri)` — navigation left the native stack for a web URL.
- `assertTransition($transition)` — the pending navigation carries this transition, given as a `Transition` case or its string value (e.g. `'slide_from_bottom'`).
- `assertNoNavigation()` — the interaction stayed on this screen.

```php
it('navigates to a demo when a featured card is tapped', function () {
    Native::visit('/')
        ->tap('Scanner')
        ->assertNavigatedTo('/media/scanner');
});
```

## Following a flow

`follow()` (alias `followNavigation()`) resolves the pending navigation through the route registry and returns a
**new** harness mounted on the destination, carrying the intent's data, params, and layout.

It mirrors the router precisely. On a forward navigate the current screen stays alive underneath — `goBack()` returns
to it with its state intact and `onResume()` fired. On a replace it unmounts and drops out of the stack.

```php
use App\NativeComponents\GeolocationDemo;

it('follows navigation from home onto the geolocation demo', function () {
    Native::visit('/')
        ->tap('Location')
        ->assertNavigatedTo('/system/geolocation')
        ->follow()
        ->assertScreen(GeolocationDemo::class)
        ->assertSee('Geolocation');
});
```

`assertScreen($componentClass)` confirms which component the harness is currently driving — useful after a `follow()`
or `goBack()` to pin down exactly where you are.

## Going back with state preserved

`goBack()` pops the current screen and returns the harness for the one below it — the live component, resumed exactly
as the router resumes it. State the previous screen held before you navigated away is still there:

```php
use App\NativeComponents\GeolocationDemo;
use App\NativeComponents\Home;

it('returns to home with state preserved after visiting a demo', function () {
    $home = Native::visit('/')
        ->call('doubleTapped')      // mutate state before navigating away
        ->tap('Location');

    $home->follow()
        ->assertScreen(GeolocationDemo::class)
        ->assertNavTitle('Geolocation')
        ->goBack()
        ->assertScreen(Home::class)
        ->assertSet('gesture', 'Double-tapped!')   // survived the push/pop
        ->assertSee('Double-tapped!');
});
```

## Chrome assertions

When a screen returns `view()` with a layout, the suite renders that layout's chrome — the navigation bar and tab bar
— just as it appears on device. Assert on it directly.

- `assertNavTitle($title)` — the navigation bar shows this title.
- `assertHasTabBar()` — the screen renders native tab chrome.
- `assertTabBarVisible()` / `assertTabBarHidden()` — the tab bar's visibility on this screen.
- `assertHasTab($label)` — a tab with this label exists.
- `assertTabActive($label)` — the tab with this label is the active one.

`visit()` resolves the route's layout automatically, so chrome is populated exactly as navigation would produce it:

```php
it('renders the hub inside tab chrome with the right tab active', function () {
    Native::visit('/media')
        ->assertHasTabBar()
        ->assertNavTitle('Media')
        ->assertHasTab('Home')
        ->assertHasTab('Media')
        ->assertHasTab('System')
        ->assertTabActive('Media')
        ->assertTabBarVisible();
});
```

Detail screens using stack chrome carry a title but no tab bar:

```php
it('renders detail screens in stack chrome without a tab bar', function () {
    Native::visit('/system/haptics')
        ->assertNavTitle('Haptics')
        ->assertMissingElement('native_root_tabs');
});
```

<aside>

When you mount a component with `test()` and want its chrome, pass the `layout` argument. `visit()` supplies the
layout from the route registration for you, so it's the natural choice for chrome assertions.

</aside>

## Walking a full flow

Put it together — follow forward through the stack, assert the chrome tracks it, and pop back:

```php
use App\NativeComponents\MediaHub;
use App\NativeComponents\ScannerDemo;

it('walks hub → demo → back with chrome tracking the stack', function () {
    $hub = Native::visit('/media')->assertTabActive('Media');

    $scanner = $hub->tap('Scanner')
        ->assertNavigatedTo('/media/scanner')
        ->follow()
        ->assertScreen(ScannerDemo::class)
        ->assertNavTitle('Scanner');

    $scanner->goBack()
        ->assertScreen(MediaHub::class)
        ->assertTabActive('Media');
});
```
