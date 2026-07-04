---
title: Subtree Reuse
order: 50
---

Declarative UI encourages a simple mental model: describe the whole screen, every time. Your component's
`render()` doesn't compute a diff — it returns the full [Element Tree](glossary#element-tree), whether one
character changed or everything did.

Taken literally, that model would be wasteful: a ticking clock in a top bar would re-encode and re-render an
entire screen once a second. Subtree reuse is the set of optimizations that keeps the simple mental model *and*
the performance — and like the rest of the pipeline, it's automatic. You never opt in, and the rendered output is
pixel-identical.

## Three layers of "don't repeat yourself"

**1. Whole-frame skip.** After encoding a [frame](glossary#frame), the
[Element Runtime](glossary#element-runtime) compares it byte-for-byte against the previous one. Identical frames
are dropped on the spot — the native side is never even woken. Idle re-renders (a poll that found nothing new, a
handler that didn't change state) cost almost nothing.

**2. Subtree reuse markers.** When a frame *does* change, most of it usually hasn't. During the render phase, every
element computes a compact fingerprint of itself and its children — a content hash. At publish time, any subtree
whose fingerprint matches the previous frame is written as a single tiny **reuse marker** instead of being
re-encoded; its children don't appear in the frame at all. The native reader splices the corresponding subtree
from the tree it already has, without decoding anything. A one-line text change publishes a handful of nodes, not
a screen's worth.

**3. Diffing at mount.** Whatever does arrive still gets diffed against the previous native tree before touching
the UI, so SwiftUI and Compose re-render only the views whose nodes actually differ. Views that survive the diff
keep their identity — along with their scroll positions, focus and running animations.

## Helping the diff: keys

Reuse works by matching nodes between frames, and matching needs identity. By default, elements are identified by
their position in the tree, which is perfect for static structure. For dynamic lists, give elements a **key** —
just like you would in Livewire or React:

@verbatim
```blade
@foreach ($messages as $message)
    <native:card :native:key="$message->id">
        <native:text>{{ $message->body }}</native:text>
    </native:card>
@endforeach
```
@endverbatim

With keys, inserting a message at the top of the list is understood as *one new card* rather than *every card
changed* — the other subtrees match their fingerprints from the previous frame and travel as reuse markers.

## Free, by design

Fingerprinting happens as part of the normal render walk, the byte comparison as part of the normal publish, and
the splice as part of the normal decode — there's no separate "optimization pass" burning cycles. And because all
three layers live in the [shared core](cross-platform-implementation), iOS and Android benefit identically, on
every frame, by default.

<aside>

There's one deliberate exception to "never resend": the runtime periodically forces a full frame, and always does
so after navigation, reset or hot reload. That heartbeat guarantees the two sides can never drift apart — reuse is
an optimization, never a source of truth.

</aside>
