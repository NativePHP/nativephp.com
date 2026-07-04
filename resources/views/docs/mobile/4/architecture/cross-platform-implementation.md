---
title: Cross-Platform Implementation
order: 40
---

NativePHP renders with each platform's own UI framework — SwiftUI on iOS, Jetpack Compose on Android — yet one
Blade template produces the same screen on both. That consistency isn't achieved by testing hard; it's achieved by
sharing the core.

## One core, two thin shells

Everything that defines *what* gets rendered lives in a single shared implementation, written in C and
[compiled directly into the PHP runtime](embedded-php) that ships on both platforms:

- walking your PHP element trees and encoding them into [frames](glossary#frame),
- the binary [node](glossary#node) format itself — every field, in the same order, with the same meaning,
- the skip-and-reuse logic described in [Subtree Reuse](subtree-reuse),
- and the [event channel](glossary#wire-events) that carries interactions back to PHP.

What remains per platform is deliberately thin: a reader that decodes frames and diffs trees, and a renderer that
maps each node type to a SwiftUI view or a composable. Even those two layers are written as mirrors of each other
— the same diffing rules, the same frame-coalescing behavior, the same enums for flexbox values, so
`justify-content: center` is the identical byte on the wire and the identical concept on both screens.

The practical consequence: when we fix a bug or land a performance improvement in the pipeline, both platforms get
it simultaneously, because there's only one pipeline.

## Native layout on both platforms

Layout follows the same philosophy. Each node carries flexbox-style layout values, and each platform implements
flexbox *inside its own layout system* — a pure-Swift `Layout` on iOS and a Compose `Layout` on Android, built to
the same semantics. There's no shared C++ layout engine sitting apart from the UI framework: your components
measure and place like any other SwiftUI or Compose view, which is exactly why safe areas, dynamic type and
platform navigation transitions work naturally with them.

iOS is the reference implementation for visual behavior; Android is held to it. Where the platforms disagree by
default (text padding, stretch behavior, pixel-versus-point units), the Android renderer adapts so the same
classes produce the same result.

## Two seams, one boundary

Rendering isn't the only traffic between PHP and native. Device features — camera, biometrics, geolocation,
secure storage and everything the [plugin system](../plugins/introduction) exposes — travel over a separate,
simpler seam: [bridge functions](../plugins/bridge-functions), a registry of named native functions PHP can call
with JSON in and JSON out.

The two seams are independent by design. The rendering path is a hot loop measured in microseconds, so it gets the
binary shared-memory treatment; device APIs are occasional calls where clarity and extensibility matter more, so
they get a friendly, pluggable interface. Plugin authors only ever touch the second one.

## Failing loud, not weird

A shared binary format is only safe if both sides agree on it, so the format carries an explicit **version
number**. At startup, each native reader checks the version reported by the Element Runtime against the one it was
compiled for; on mismatch it refuses to render rather than misinterpret bytes. Because the PHP runtime and the
native shells [ship together in lockstep](embedded-php), the check should never fire in a real app — it exists so
that if the impossible happens, you get one clear error instead of a subtly broken UI.
