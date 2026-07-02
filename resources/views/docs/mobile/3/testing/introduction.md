---
title: Introduction
order: 100
---

## Overview

NativePHP ships a testing suite for your [Super Native](../super-native/introduction) screens. It mounts a
`NativeComponent`, renders its Blade, dispatches events, and re-renders — the whole component lifecycle — entirely
in-process.

There's no device, no simulator, and no running app. Tests are plain [Pest](https://pestphp.com) (or PHPUnit) and run
anywhere PHP does, including CI. A `FakeBridge` stands in for the native runtime and captures every element tree the
component publishes and every native call it makes, so your assertions target the published wire tree and the
component's state.

Rendering fidelity — how SwiftUI or Compose actually paints those elements on screen — is out of scope by design. The
suite asserts on *what your component published*, exactly the layer you control from PHP.

## The `Native` entry point

Every test starts from the `Native` facade. It has three entry points:

```php
use Native\Mobile\Testing\Native;
```

- `Native::test(Counter::class)` mounts a component class directly.
- `Native::visit('/profile/5')` mounts the component registered for a native route, resolving the route's params and
  layout exactly as navigation would on device.
- `Native::fakeBridge()` returns the `FakeBridge` so you can script native responses before the component mounts.

Both `test()` and `visit()` return a `TestableComponent` — the fluent harness you chain assertions and interactions
onto.

## Your first test

Say you have a `Counter` screen with a public `$count` property and an `increment` button:

```php
use App\NativeComponents\Counter;
use Native\Mobile\Testing\Native;

it('increments the count', function () {
    Native::test(Counter::class)
        ->assertSee('Count: 0')
        ->tap('Increment')
        ->assertSet('count', 1)
        ->assertSee('Count: 1');
});
```

`assertSee()` looks for text anywhere in the rendered tree. `tap()` presses the nearest pressable element whose
subtree shows the given text (or one carrying a matching `ref` — more on that in [Interactions](interactions)).
`assertSet()` reads a public or `#[Computed]` property and compares it. Every interaction re-renders the component, so
the assertions that follow see the fresh frame.

## Passing params and data

`test()` accepts route params and navigation data, mirroring the values a screen receives on device:

```php
it('shows the requested profile', function () {
    Native::test(Profile::class, params: ['id' => 5], data: ['from' => 'search'])
        ->assertSee('Profile #5');
});
```

When you use `visit()`, params come from the route URI automatically:

```php
Native::visit('/profile/5')->assertSee('Profile #5');
```

## Generating a test

Scaffold a test for any screen with the `native:make-test` command:

```bash
php artisan native:make-test Counter
```

This writes `tests/Feature/CounterTest.php` with a couple of starter `it()` blocks ready to fill in. A bare name
resolves to `App\NativeComponents\Counter`; you can also pass a nested name like `Settings/Profile` or a fully
qualified class name. Pass `--force` to overwrite an existing test.

## What's next

- [Interactions](interactions) — tapping, typing, toggling, and every other way to drive a screen.
- [Native Events & the Bridge](native-events) — delivering device events and asserting on native calls.
- [Navigation & Flows](navigation) — walking between screens and asserting on chrome.
- [Advanced](advanced) — platform variants, render-count guards, wire snapshots, and Pest sugar.
