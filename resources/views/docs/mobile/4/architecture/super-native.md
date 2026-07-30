---
title: SuperNative Introduction
order: 5
---

## What is SuperNative?

SuperNative is our name for a combination of technologies that enable PHP to produce platform-native UI at
blistering speeds.

While SuperNative uses HTML-like syntax, **there is no need for a web view**. Your apps built with Blade 
can fully leverage the native components and performance of each platform's UI tools, directly from PHP — SwiftUI
on iOS and Jetpack Compose on Android.

Your app's screens are not web views rendering HTML. They are real, platform-native views, built and
updated by your PHP code. Same Laravel app, same Blade templates, two genuine native UIs.

You don't need to think about the right syntax for Android vs iOS, just use EDGE.

SuperNative is **the default**. New apps render native screens from the very first route — no configuration
required.

<aside>

Prefer to keep building with the web view? [Opting out](#is-the-web-view-still-an-option) of native UIs couldn't
be simpler.

</aside>

## What SuperNative is not

SuperNative is not a fully custom renderer, like Skia or Impeller. It does not attempt to create pixel-perfect
cross-platform user interfaces. Instead it *embraces* the differences and simply smooths over them with a single
consistent syntax.

It's not another virtual machine on top of or adjacent to PHP trying to convert *all* instructions to/from native
equivalents. It's explicitly focused on turning PHP objects that conform to a known interface into a fixed-length
byte array that can be processed by an explicit native-side interpreter.

Neither is it a transpiler or HTML-to-native converter. We've built our own Blade engine that converts real Blade
components into a simple binary representation instead of HTML ([The Renderer](renderer)). This gets passed immediately
to the native shell — no other serialization, no bridge function calls — and Swift and Kotlin can pick it up and parse
it immediately into a native UI tree.

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
boundary, and the threading model behind it — see the rest of the [Architecture](about-the-new-architecture) section.

<aside>

You **don't** need to read any of this to build apps with NativePHP. If you're here to ship an app, start with the
[Quick Start](../getting-started/quick-start) and [The Basics](../the-basics/overview) instead. This section is for
the curious — and for plugin authors and contributors who want to understand the machinery they're building on.

</aside>

## Why SuperNative?

Two reasons above all:

- **Performance** — native views render and animate at full platform speed. No web view startup cost, no DOM, no
  JavaScript bridge. Scrolling, transitions and gestures feel exactly the way users expect because they're powered
  by the same UI frameworks every other native app uses.
- **Accessibility** — SwiftUI and Jetpack Compose come with the platform's accessibility support built in.
  Screen readers, dynamic type, contrast settings and assistive controls work with your app out of the box,
  rather than being approximated through a browser.

There's more detail [in this blog article](/blog/supernative).

## Is the web view still an option?

Yes, but instead of being the default, it's now a component that you add to a native view. To make your app behave
the same way that NativePHP for Mobile versions before v4 did, you can do something like this:

```php
// routes/mobile.php
Route::native('/home', WebViewScreen::class);

// webviewscreen.blade.php
<webview php url="/" fullscreen />

// routes/web.php
Route::view('/', 'welcome');
```

Then just set `NATIVEPHP_START_URL=/home` in your `.env`.

This way your existing web view-based app can keep on working and you can start adopt SuperNative one screen at a time
whenever you're ready — or not at all.

## For Plugin Developers

SuperNative contains **no breaking changes** to our plugin architecture.

It actually expands what your plugins are capable of by giving you a standardized target for UI elements. That means your
plugins can ship fully native EDGE components that you know will work consistently for developers using your plugin.

No need to make your plugins UI-less abstractions or support multiple flavors of front-end tooling; simply create and ship
EDGE components and every consumer of your plugin will see the UI you intended.
