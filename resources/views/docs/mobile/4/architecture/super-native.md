---
title: SuperNative Introduction
order: 5
---

## What is SuperNative?

SuperNative is our name for the set of technologies that let PHP produce platform-native UI. Your Blade
templates become SwiftUI views on iOS and Jetpack Compose views on Android, built and updated directly by
your PHP code.

The syntax looks like HTML, but **there is no web view**. Each screen is a real, platform-native view.
Same Laravel app, same Blade templates, two native UIs.

You write EDGE components once. There is no separate syntax for Android and iOS.

SuperNative is **the default**. New apps render native screens from the very first route, with no
configuration required.

<aside>

Prefer to keep building with the web view? [Opting out](#is-the-web-view-still-an-option) takes one route
and one component.

</aside>

## What SuperNative is not

SuperNative is not a custom renderer like Skia or Impeller, and it does not try to draw pixel-identical
interfaces on both platforms. Each platform keeps its own look and behaviour. SuperNative gives you one
syntax for describing a screen and lets SwiftUI and Compose render it their own way.

It is not a virtual machine that translates every PHP instruction into a native equivalent. Its job is
narrower: take PHP objects that conform to a known interface and turn them into a fixed-length byte array
that a native-side interpreter can read.

It is not a transpiler or an HTML-to-native converter either. We built our own Blade engine,
[The Renderer](renderer), that compiles real Blade components into a simple binary representation instead
of HTML. That representation goes straight to the native shell with no further serialization and no bridge
function calls, and Swift or Kotlin parse it into a native UI tree.

## Try it now

The fastest way to see SuperNative is to run the demo app,
[`nativephp/super-native`](https://github.com/nativephp/super-native), on a simulator or device.

You'll need a working NativePHP for Mobile [development environment](../getting-started/environment-setup)
first (Xcode for iOS, Android Studio for Android). Then clone the demo, install it, and run it:

```shell
git clone https://github.com/nativephp/super-native
cd super-native
composer install
php artisan native:install
php artisan native:run
```

`native:run` builds the app and launches it on your connected device or simulator. Explore the source to
see how the screens are built, then start swapping in your own.

## How it works

SuperNative rests on three ideas.

The native layer and your PHP application share memory directly. There is no network round-trip and no
web view bridge in between, so a state change in PHP reaches the native UI almost instantly.

Each screen is driven by a PHP component class that holds its state and behaviour, much like a Livewire
component. User interactions call your methods, your properties update, and the UI re-renders to match.

You describe the UI with the [EDGE component](../edge-components/introduction) syntax you already know
from Blade. Those templates compile to SwiftUI and Compose views.

If you've built anything with Livewire, you already know how to build with SuperNative.

The rest of the [Architecture](about-the-new-architecture) section covers how Blade becomes SwiftUI and
Compose, how state crosses the shared-memory boundary, and the threading model behind it.

<aside>

You **don't** need to read any of this to build apps with NativePHP. If you're here to ship an app, start
with the [Quick Start](../getting-started/quick-start) and [The Basics](../the-basics/overview) instead.
This section is for the curious, and for plugin authors and contributors who want to understand the
machinery they're building on.

</aside>

## Why SuperNative?

Two reasons above all.

Performance. Native views render and animate at full platform speed. There is no web view to start up and
no JavaScript bridge to cross. Scrolling and transitions feel the way users expect because the same UI
frameworks every other native app uses are doing the work.

Accessibility. SwiftUI and Jetpack Compose ship with the platform's accessibility support. Screen readers,
dynamic type, contrast settings and assistive controls work with your app out of the box instead of being
approximated through a browser.

There's more detail [in this blog article](/blog/supernative).

## Is the web view still an option?

Yes. It is no longer the default, but it is available as a component you add to a native view. To make
your app behave the way NativePHP for Mobile did before v4:

```php
// routes/mobile.php
Route::native('/home', WebViewScreen::class);

// webviewscreen.blade.php
<webview php url="/" fullscreen />

// routes/web.php
Route::view('/', 'welcome');
```

Then set `NATIVEPHP_START_URL=/home` in your `.env`.

Your existing web view app keeps working, and you can adopt SuperNative one screen at a time whenever
you're ready, or never.

## For plugin developers

SuperNative introduces **no breaking changes** to the plugin architecture.

It gives your plugin a standard target for UI. Your plugin can ship native EDGE components and know they
will render the same way for every developer who installs it. You no longer have to keep plugins UI-less
or support several front-end toolchains.
