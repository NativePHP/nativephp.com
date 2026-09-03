---
title: About the New Architecture
order: 10
---

NativePHP for Mobile runs your Laravel app on the device itself. There is no server and no network round-trip.
Until now, your app's UI rendered as HTML inside a web view. That model is productive and familiar, and it's
[still fully supported](../architecture/super-native#is-the-web-view-still-an-option). But it puts a browser between
your app and the platform, and some things only feel native when they *are* native.

The new architecture, [SuperNative](../architecture/super-native), removes that layer. Your screens are real SwiftUI
and Jetpack Compose views, created and updated directly by your PHP code. Here's why we built it and what it
changes.

## Why a new architecture?

### Truly native rendering

A web view is a remarkable piece of engineering, but users can tell. Scroll physics, text rendering, transitions,
context menus, dark mode, dynamic type, screen readers. Every one of these is approximated in a browser and comes
for free in the platform's own UI framework.

With the new architecture, an EDGE component like `<native:button>` is the same button every other native app on
that device uses, rather than a styled `<div>`. Accessibility, theming and platform conventions come along with it,
because there's nothing between your UI and the operating system.

### Shared memory instead of serialization

Frameworks that drive native UI from another language usually pay a toll at the border. State gets serialized to
JSON, shipped across a bridge, and parsed on the other side, on every update.

NativePHP doesn't have that border. Your PHP runtime is [embedded inside the app process](embedded-php), and the
rendering layer communicates through **shared memory**. When your component re-renders, PHP writes a compact binary
description of the screen directly into a buffer the native side reads from. There is no JSON and no bridge to
cross. The native layer walks your PHP data structures directly, in the same process, in native code.

A state change in PHP reaches the screen in well under a frame.

### Native-speed interaction

Some things should never wait for application code. A drag should track your finger, and an animation should never
drop a frame. The new architecture has a dedicated lane for these. SharedValues live on the native side and are
updated on the UI thread by gestures and animations at the display's full frame rate. PHP holds a handle and hears
about the outcome. The per-frame work never crosses into PHP at all.

You get Reanimated-style, gesture-driven motion, written in Blade.

## What can you expect?

Practically, the same development experience you already have, with a different result on screen.

- Your Laravel app stays the same. Routes, controllers, Eloquent, validation and queues are untouched. Screens are
  registered with `Route::native()` and driven by PHP component classes, Livewire-style.
- Your Blade stays the same. You compose screens from [EDGE components](../edge-components/introduction),
  declarative Blade tags with Tailwind-style utility classes, and the framework turns them into SwiftUI and Compose.
- The output is native. Navigation stacks, tab bars, sheets, lists and gestures are the platform's own, in light and
  dark mode, with the platform's accessibility support built in.
- You adopt at your own pace. The web view is still available as a component, so you can go all-native, all-web, or
  migrate screen by screen.

<aside>

The new architecture makes a different *class* of app possible. It doesn't automatically make an existing web-view
app faster. If your app is happy in the web view, it will keep working exactly as before.

</aside>

## Should you use it today?

SuperNative is **the default**. New apps render native screens from the very first route. It's in beta, so expect
rapid iteration. If you'd rather wait,
[opting out](../architecture/super-native#is-the-web-view-still-an-option) is one route and one component.

Ready to go deeper? Start with [The Renderer](renderer).
