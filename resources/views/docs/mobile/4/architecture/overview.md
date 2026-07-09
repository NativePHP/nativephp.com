---
title: Architecture Overview
order: 1
---

Welcome! This section is a look under the hood of [SuperNative](../super-native/introduction), the engine that powers
native rendering in NativePHP for Mobile. It explains how your Blade templates become real SwiftUI and Jetpack
Compose views, how state flows between PHP and the screen, and why the whole thing is fast.

<aside>

You **don't** need to read any of this to build apps with NativePHP. If you're here to ship an app, start with the
[Quick Start](../getting-started/quick-start) and [The Basics](../the-basics/overview) instead. This section is for
the curious — and for plugin authors and contributors who want to understand the machinery they're building on.

</aside>

These pages describe the internals as they exist today. SuperNative is in beta and moving quickly, so details may
evolve — the concepts, however, are stable.

## Table of Contents

- [About the New Architecture](about-the-new-architecture) — why we rebuilt rendering from the ground up, and what
  it means for your apps.

### Rendering

- [The Renderer](renderer) — the rendering system at the heart of SuperNative, and the goals that shaped it.
- [Render, Publish, and Mount](render-publish-mount) — the three-phase pipeline that turns your PHP into pixels.
- [Cross-Platform Implementation](cross-platform-implementation) — how one shared core keeps iOS and Android in
  perfect agreement.
- [Subtree Reuse](subtree-reuse) — the optimizations that make re-rendering cheap, automatically.
- [Threading Model](threading-model) — which threads do what, and how your UI stays responsive.

### Build Tools

- [Embedded PHP](embedded-php) — how a full PHP runtime ends up inside your app, built in lockstep with the
  framework.

### Reference

- [Glossary](glossary) — every term used in these pages, defined in one place.

If anything here is unclear or you'd like more depth on a particular topic, we'd love to hear from you.
