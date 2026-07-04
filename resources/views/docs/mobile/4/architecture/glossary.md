---
title: Glossary
order: 80
---

Definitions for the terms used throughout the Architecture section.

## SuperNative

The engine behind native rendering in NativePHP for Mobile v4. It spans everything described in this section:
components, the render pipeline, the shared-memory boundary and the platform renderers.
[About the New Architecture](about-the-new-architecture) is the best starting point.

## EDGE (Element Definition and Generation Engine)

The component language: the suite of `native:` Blade components — layout containers, typography, forms, navigation
chrome, overlays — that screens are built from. EDGE templates compile down to [Elements](#element);
the renderer only ever sees the resulting tree. See [EDGE Components](../edge-components/introduction).

## Native Component

The PHP class that drives a screen — the Livewire-style component you register with `Route::native()`. It holds
the screen's state, renders the [Element Tree](#element-tree), and its methods are what event handlers like
`@press` call.

## Element

A plain PHP object describing one piece of UI — a column, a text, a button — including its layout, style, props
and handlers. Primitive elements are what appear in the Element Tree; higher-level EDGE components flatten into
them during rendering.

## Element Tree

The tree of Elements produced by a Native Component's `render()` — the PHP-side description of the entire screen.
The input to the [Publish phase](render-publish-mount#phase-2-publish).

## Element Runtime

The native code compiled into the [embedded PHP](embedded-php) runtime (as part of the `nativephp` extension) that
implements the PHP side of rendering: encoding Element Trees into [frames](#frame), managing the shared memory
region, and carrying [wire events](#wire-events) back to PHP.

## Frame

One published snapshot of the screen: the binary encoding of an Element Tree, written into shared memory as a
sequence of [nodes](#node) plus their props. Frames are versioned, skipped when identical to their predecessor,
and shrunk by [subtree reuse](subtree-reuse).

## Node

The fixed-layout binary record representing one Element inside a frame — its type, layout values, style values and
references to its props and callbacks. Because every node has the same shape, the native readers can decode frames
extremely quickly, and both platforms interpret every field identically.

## Node Tree

The native-side tree the readers decode from a frame and hand to the platform renderers. The previous Node Tree is
what incoming frames are diffed against, and what reuse markers splice from.

## Renderers

The per-platform layer that maps each node type to real UI: SwiftUI views on iOS and composables on Android, each
with a native flexbox implementation built on the platform's own `Layout` system. See
[Cross-Platform Implementation](cross-platform-implementation).

## Runloop

The long-lived cycle that drives a native screen on the [PHP thread](threading-model): render → publish → wait for
an event → dispatch it → repeat. A screen's runloop lives for as long as the screen does.

## Persistent Runtime

The embedded PHP instance that boots your Laravel app once at launch and stays resident for the app's lifetime.
Separate runtimes exist for queue workers and plugin background work. See
[Threading Model](threading-model#lifecycle-booted-once-kept-warm).

## Callback ID

The stable identifier assigned to an event handler (like `@press="refresh"`) during rendering. Nodes reference
handlers by ID, native events carry the ID back, and PHP resolves it to your method. Stable IDs are also what let
unchanged subtrees be [reused](subtree-reuse) even though they contain handlers.

## Wire Events

The ordered event channel from native to PHP: presses, text changes, toggles, scrolls, system back, lifecycle
signals and plugin events all travel through it as compact binary messages, waking the runloop exactly when
there's something for your code to do.

## SharedValue

A value that lives on the native side and is updated on the UI thread — by gestures or animations — at the
display's full frame rate. PHP holds a handle, binds it to animatable props in Blade, and receives discrete events
when something meaningful happens. The mechanism behind PHP-free interaction, described in
[Render, Publish, and Mount](render-publish-mount#native-side-state-updates).

## Bridge Functions

The second, simpler PHP↔native seam: a registry of named native functions (camera, biometrics, geolocation, and
everything plugins add) that PHP calls with JSON parameters. Independent from the rendering path — see
[Cross-Platform Implementation](cross-platform-implementation#two-seams-one-boundary) and the
[plugin docs](../plugins/bridge-functions).

## Embed SAPI

The PHP build mode that produces PHP as an embeddable C library instead of a standalone binary, allowing the
native app to run PHP inside its own process. The foundation of [Embedded PHP](embedded-php).
