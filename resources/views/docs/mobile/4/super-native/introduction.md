---
title: Introduction
order: 1
---

## What is SuperNative?

SuperNative is the headline feature of NativePHP for Mobile v4: it drops the web view entirely in favor of fully
native UI — SwiftUI on iOS and Jetpack Compose on Android.

Your screens are no longer HTML rendered inside a browser shell. They are real, platform-native views, built and
updated by your PHP code. Same Laravel app, same Blade templates — genuinely native UI.

SuperNative is **the default** in v4. New apps render native screens from the very first route — no configuration
required. Prefer to keep building with the web view? [Opting out](#is-the-web-view-still-an-option) is one route
and one component.

## Try it now

The fastest way to see SuperNative is to run the demo app,
[`nativephp/super-native`](https://github.com/nativephp/super-native), on a simulator or device.

You'll need a working NativePHP for Mobile [development environment](../getting-started/environment-setup) first
(Xcode for iOS, Android Studio for Android). Then clone the demo, install it, and run it:

```shell
git clone https://github.com/nativephp/super-native
cd super-native
composer install
php artisan native:install
php artisan native:run
```

`native:run` builds the app and launches it on your connected device or simulator. Explore the source to see how
the screens are built, then start swapping in your own.

## How it works

SuperNative builds on three ideas working together:

- **Shared memory with PHP** — the native layer and your PHP application share memory directly, so there's no
  network round-trip, no serialization overhead, and no waiting on a web view bridge. State changes flow between
  PHP and the native UI almost instantly.
- **Livewire-like components** — each screen is driven by a PHP component class that holds its state and behavior,
  just like a Livewire component. User interactions call your methods, your properties update, and the UI
  re-renders to match.
- **Blade components for DX** — you define your UI with the same [EDGE component](../edge-components/introduction)
  syntax you already know. Familiar, expressive Blade templates compile down to native SwiftUI and Compose views.

If you've built anything with Livewire, you already know how to build with SuperNative.

For a look under the hood — how Blade becomes SwiftUI and Compose, how state flows across the shared-memory
boundary, and the threading model behind it — see the [Architecture](../architecture/overview) section.

## Why SuperNative?

Two reasons above all:

- **Performance** — native views render and animate at full platform speed. No web view startup cost, no DOM, no
  JavaScript bridge. Scrolling, transitions and gestures feel exactly the way users expect because they're powered
  by the same UI frameworks every other native app uses.
- **Accessibility** — SwiftUI and Jetpack Compose come with the platform's accessibility support built in.
  Screen readers, dynamic type, contrast settings and assistive controls work with your app out of the box,
  rather than being approximated through a browser.

## Is the web view still an option?

Yes. SuperNative applies **per route**: a URL you register with `Route::native()` renders as a native screen,
while every other route renders in the web view as ordinary Laravel HTML — exactly as it did in v3.

```php
// Native screen — SuperNative renders it natively
Route::native('/dashboard', Dashboard::class);

// A normal route — served to the web view as HTML, unchanged
Route::get('/settings', fn () => view('settings'));
```

So an existing app keeps working web-view-first with no changes, and you adopt SuperNative one screen at a time
whenever you're ready — or not at all.

## What's coming

SuperNative is in beta and moving quickly. On the roadmap:

- [Jump](../the-basics/jump) support
- A revised kitchen sink app
- Full plugin support for all first-party plugins

## For Plugin Developers

There are **no breaking changes** in our plugin architecture for v4.

Start updating your plugins now by widening the `nativephp/mobile` constraint in your plugin's `composer.json` to
allow v4:

```json
"require": {
    "nativephp/mobile": "^3.0" // [tl! remove]
    "nativephp/mobile": "^3.0|^4.0" // [tl! add]
}
```
