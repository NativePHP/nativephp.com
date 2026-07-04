---
title: About the New Architecture
order: 10
---

Since v1, NativePHP for Mobile has run your Laravel app on the device itself — no server, no network round-trip. In
v3 and earlier, your app's UI rendered as HTML inside a web view. That model is productive and familiar, and it's
[still fully supported](../super-native/introduction#is-the-web-view-still-an-option). But it puts a browser between
your app and the platform, and some things can only feel truly native when they *are* native.

The new architecture — [SuperNative](../super-native/introduction) — removes that layer entirely. Your screens are
real SwiftUI and Jetpack Compose views, created and updated directly by your PHP code. Here's why we built it, and
what it changes.

## Why a new architecture?

### Truly native rendering

A web view is a remarkable piece of engineering, but users can tell. Scroll physics, text rendering, transitions,
context menus, dark mode, dynamic type, screen readers — every one of these is approximated in a browser and
effortless in the platform's own UI framework.

With the new architecture, an EDGE component like `<native:button>` isn't a styled `<div>` — it's the same button
every other native app on that device uses. Accessibility, theming and platform conventions come along for free,
because there's nothing between your UI and the operating system.

### Shared memory, not serialization

Frameworks that drive native UI from another language usually pay a toll at the border: state gets serialized to
JSON, shipped across a bridge, and parsed on the other side — for every update.

NativePHP doesn't have that border. Your PHP runtime is [embedded inside the app process](embedded-php), and the
rendering layer communicates through **shared memory**. When your component re-renders, PHP writes a compact binary
description of the screen directly into a buffer the native side reads from — no JSON, no sockets, no copies across
a bridge. The native layer walks your PHP data structures directly, in the same process, in native code.

The result: a state change in PHP reaches the screen in well under a frame.

### Native-speed interaction

Some things should never wait for application code — a drag should track your finger, an animation should never
drop a frame. The new architecture has a dedicated lane for these:
SharedValues live on the native side and are updated on the UI thread by gestures and animations at the display's
full frame rate. PHP holds a handle and hears about the outcome; the
per-frame work never crosses into PHP at all.

You get Reanimated-style, gesture-driven motion — written in Blade.

## What can you expect?

Practically, the same development experience you already have, with a different result on screen:

- **Same Laravel app.** Routes, controllers, Eloquent, validation, queues — nothing about your backend changes.
  Screens are registered with `Route::native()` and driven by PHP component classes, Livewire-style.
- **Same Blade.** You compose screens from [EDGE components](../edge-components/introduction) — declarative Blade
  tags with Tailwind-style utility classes — and the framework turns them into SwiftUI and Compose.
- **Native output.** Navigation stacks, tab bars, sheets, lists and gestures are the platform's own, in light and
  dark mode, with the platform's accessibility support built in.
- **Adopt at your pace.** The web view is still available as a component, so you can go all-native, all-web, or
  migrate screen by screen.

<aside>

The new architecture makes a different *class* of app possible — it doesn't automatically make an existing web-view
app faster. If your app is happy in the web view, it will keep working exactly as before.

</aside>

## Should you use it today?

SuperNative is **the default** in v4: new apps render native screens from the very first route. It's in beta, so
expect rapid iteration — and if you'd rather wait, [opting out](../super-native/introduction#is-the-web-view-still-an-option)
is one route and one component.

Ready to go deeper? Start with [The Renderer](renderer).
