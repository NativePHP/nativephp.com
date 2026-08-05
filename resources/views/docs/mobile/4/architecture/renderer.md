---
title: The Renderer
order: 20
---

The renderer is the machinery at the heart of SuperNative: everything involved in turning the
[Element Tree](glossary#element-tree) your PHP code produces into native views on screen. It spans three layers
that work as one:

- The **[Element Runtime](glossary#element-runtime)** — native code compiled into the PHP runtime itself. It walks
  your element tree, encodes it into a compact binary [frame](glossary#frame), and manages the shared memory both
  sides communicate through.
- The **native readers** — a small Swift and Kotlin layer on each platform that receives frames, works out what
  changed since the last one, and hands the result to the UI.
- The **platform renderers** — SwiftUI on iOS and Jetpack Compose on Android, which map each
  [node](glossary#node) to a real platform view and lay everything out.

The pipeline they form — render, publish, mount — has [its own page](render-publish-mount). This page covers the
goals that shaped the design.

## Motivations and benefits

- **One process, zero serialization.** PHP and the UI live in the same process and share memory. Publishing a
  frame means writing bytes into a buffer, not encoding JSON and shipping it over a bridge. The Element Runtime
  reads your PHP arrays directly in native code, so even the encoding step is fast.

- **The platform's own layout system.** Layout is implemented natively on each platform — a pure-Swift flexbox
  built on SwiftUI's `Layout` protocol, and a matching Compose `Layout` on Android. There's no third-party layout
  engine in the middle: your components participate in SwiftUI and Compose layout as first-class citizens, which
  means platform features like safe areas, dynamic type and keyboard avoidance behave the way the OS intends.
  Both implementations follow the same flexbox semantics, so `justify-center` means precisely the same thing on
  both platforms.

- **Only changes hit the screen.** The renderer is built around the idea that most frames look a lot like the
  previous frame. Identical frames are skipped before they're even published; unchanged subtrees are
  [reused rather than re-sent](subtree-reuse); and the native readers diff each incoming frame so the UI only
  re-renders what actually changed.

- **A versioned, type-safe wire format.** Every node crosses the boundary as a fixed-layout binary record, and the
  format carries an explicit version number. At startup, the native readers check that version against the one
  they were built for — a mismatch fails loudly and immediately rather than rendering garbage. Because
  [PHP and the framework are built together](embedded-php), both sides of the boundary always speak the same
  version in practice; the handshake is the backstop.

- **Consistency across platforms by construction.** The encoding, diffing rules and event format are implemented
  once, in [shared native code](cross-platform-implementation), and the per-platform layers are deliberately thin.
  When we improve the pipeline, both platforms get the improvement at the same time.

- **Interactions that don't wait for PHP.** Press feedback, gestures and [SharedValue](glossary#sharedvalue)-driven
  animations are handled on the native side at full frame rate. PHP is woken for the things application code
  actually cares about — a press, a committed drag, a text change — through a single ordered
  [event channel](glossary#wire-events).

## Where components come from

The renderer doesn't know about Blade — it only ever sees element trees. The
[EDGE](../edge-components/introduction) component layer (templates, Tailwind-style classes, slots, chrome like top
bars and tab bars) compiles down to the same primitive elements whether you write Blade tags or build elements in
PHP code. That separation is deliberate: the component language can grow rich while the wire format underneath
stays small, stable and fast.

Next: follow a screen through the pipeline in [Render, Publish, and Mount](render-publish-mount).
