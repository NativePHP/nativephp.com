---
title: Render, Publish, and Mount
order: 30
---

The render pipeline is the sequence of steps that turns your PHP into pixels. It has three phases:

1. **Render** — PHP builds an [Element Tree](glossary#element-tree) describing what the screen should look like.
2. **Publish** — the [Element Runtime](glossary#element-runtime) encodes that tree into a binary
   [frame](glossary#frame) in shared memory and signals the native side.
3. **Mount** — the native side works out what changed and updates the SwiftUI or Compose view tree.

The same pipeline runs for the first paint of a screen and for every update after it. Let's follow a concrete
component through it.

@verbatim
```blade
<native:column class="p-4 gap-4 bg-theme-background">
    <native:text class="text-2xl font-bold">{{ $title }}</native:text>
    <native:button label="Refresh" @press="refresh" />
</native:column>
```
@endverbatim

## Phase 1: Render

When a screen starts — or when its state changes — the framework calls your component's `render()` method. Blade
compiles the template, and each `native:` tag emits an **Element**: a plain PHP object describing one piece of UI.
Utility classes like `p-4` and `text-2xl` are parsed into concrete layout and style values at this stage, and
event handlers like `@press="refresh"` are registered and replaced with stable
[callback IDs](glossary#callback-id).

The result is the Element Tree — for our example: a screen containing a column containing a text and a button.
Higher-level EDGE components you compose yourself flatten into these primitives; only primitive elements appear in
the tree.

## Phase 2: Publish

The Element Tree is handed to the Element Runtime — native code living inside the PHP runtime. It walks the tree
and writes each element into shared memory as a [node](glossary#node): a compact, fixed-layout binary record
carrying the element's type, layout, style, and references to its props and handlers.

Before signaling the other side, the runtime is ruthless about not doing unnecessary work:

- If the new frame is **byte-for-byte identical** to the previous one, nothing is published at all.
- If only part of the tree changed, unchanged subtrees are replaced by tiny **reuse markers** instead of being
  re-encoded — see [Subtree Reuse](subtree-reuse).

If anything did change, the runtime atomically bumps the frame version and wakes the native reader. All of this
happens on the [PHP thread](threading-model), off the UI.

## Phase 3: Mount

On the native side, a dedicated reader thread picks up the frame, decodes the nodes, and **diffs** the result
against the tree it rendered last time. Reuse markers are spliced from the previous tree without any decoding.
The diffed tree is then handed to the main thread, where SwiftUI or Compose re-renders exactly the views whose
nodes changed — everything else keeps its identity, its state and its in-flight animations.

Layout happens here too, inside the platform's own layout pass: the flexbox values carried by each node
(direction, gap, padding, flex grow…) drive a native `Layout` implementation on each platform, and the OS
composites the result to the screen.

## State updates

Interaction runs the same pipeline in a loop. When someone taps our **Refresh** button:

1. The native button fires a press event carrying its callback ID into the
   [event channel](glossary#wire-events).
2. The PHP thread — which was waiting for exactly this — wakes up, resolves the callback ID to your `refresh()`
   method, and calls it.
3. Your method mutates component state; the framework re-renders (**Phase 1**), publishes the new frame
   (**Phase 2**), and the native side mounts the difference (**Phase 3**).

Because callback IDs are stable across frames and unchanged subtrees are reused, an update that changes one line
of text costs about as much as… changing one line of text.

## Native-side state updates

Some state changes never enter the pipeline at all. Scroll positions, drag offsets and
[SharedValue](glossary#sharedvalue)-driven animations are owned by the native side and updated directly on the UI
thread, frame by frame. PHP isn't consulted per frame — it receives a discrete event when something it cares
about happens (a drag ends, a threshold is crossed) and can then run the normal three phases in response.

This split is what keeps gestures glued to your finger even while your PHP code is busy doing something else —
more on that in the [Threading Model](threading-model).
