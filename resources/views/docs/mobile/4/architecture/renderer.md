---
title: The Renderer
order: 20
---

The renderer is the machinery at the heart of SuperNative: everything involved in turning the
[Element Tree](glossary#element-tree) your PHP code produces into native views on screen. It spans three layers
that work as one:

- The **[Element Runtime](glossary#element-runtime)** is native code compiled into the PHP runtime itself. It
  walks your element tree, encodes it into a compact binary [frame](glossary#frame), and manages the shared memory
  both sides communicate through.
- The **native readers** are a small Swift and Kotlin layer on each platform. They receive frames, work out what
  changed since the last one, and hand the result to the UI.
- The **platform renderers** are SwiftUI on iOS and Jetpack Compose on Android. They map each
  [node](glossary#node) to a real platform view and lay everything out.

Together they form the render, publish and mount pipeline, which has [its own page](render-publish-mount). This
page covers the goals that shaped the design.

## Motivations and benefits

PHP and the UI live in the same process and share memory, so nothing is serialized. Publishing a frame means
writing bytes into a buffer. There is no JSON to encode and no bridge to ship it over. The Element Runtime reads
your PHP arrays directly in native code, so even the encoding step is fast.

Layout uses the platform's own layout system. It is implemented natively on each platform: a pure-Swift flexbox
built on SwiftUI's `Layout` protocol, and a matching Compose `Layout` on Android. There's no third-party layout
engine in the middle. Your components take part in SwiftUI and Compose layout like any other view, so platform
features like safe areas, dynamic type and keyboard avoidance behave the way the OS intends. Both implementations
follow the same flexbox semantics, so `justify-center` means the same thing on both platforms.

Only changes hit the screen. The renderer is built around the observation that most frames look a lot like the
previous one. Identical frames are skipped before they're published, unchanged subtrees are
[reused rather than re-sent](subtree-reuse), and the native readers diff each incoming frame so the UI only
re-renders what changed.

The wire format is versioned and type-safe. Every node crosses the boundary as a fixed-layout binary record, and
the format carries an explicit version number. At startup, the native readers check that version against the one
they were built for. A mismatch fails loudly and immediately rather than rendering garbage. Because
[PHP and the framework are built together](embedded-php), both sides of the boundary always speak the same version
in practice. The handshake is the backstop.

Consistency across platforms is built in. The encoding, diffing rules and event format are implemented once, in
[shared native code](cross-platform-implementation), and the per-platform layers are deliberately thin. When we
improve the pipeline, both platforms get the improvement at the same time.

Interactions don't wait for PHP. Press feedback, gestures and [SharedValue](glossary#sharedvalue)-driven animations
are handled on the native side at full frame rate. PHP is woken for the things application code cares about, such
as a press, a committed drag or a text change, through a single ordered [event channel](glossary#wire-events).

## Where components come from

The renderer doesn't know about Blade. It only ever sees element trees. The
[EDGE](../edge-components/introduction) component layer (templates, Tailwind-style classes, slots, chrome like top
bars and tab bars) compiles down to the same primitive elements whether you write Blade tags or build elements in
PHP code. That separation is deliberate: the component language can grow while the wire format underneath stays
small and stable.

Next: follow a screen through the pipeline in [Render, Publish, and Mount](render-publish-mount).
