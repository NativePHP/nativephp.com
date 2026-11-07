---
title: Performance
order: 35
---

## Overview

SuperNative's model is simple: a screen re-renders itself whenever its state changes, and the
[render → publish → mount](../architecture/render-publish-mount) pipeline keeps that cheap through
[subtree reuse](../architecture/subtree-reuse). For the vast majority of screens you never think about it — taps
feel instant and you move on.

This page is for the exception: a screen that has grown large — a long form, a dense dashboard, a big table —
where taps start to feel heavy. It covers what a single interaction actually costs, what scales with screen size,
and the levers (in the order worth reaching for them). The golden rule up front: **measure before you optimize.**

## What a tap costs

When someone taps a control, the update runs a fixed pipeline:

1. **Event** — the native press crosses the bridge into the waiting PHP thread (transport + serialization).
2. **Render** — your handler mutates state and the framework re-renders. Reactivity is per **screen**: a state
   change re-renders the *whole* component that owns it, top to bottom. (Child components are composition, not
   independent reactive boundaries — a parent's state change re-renders them too. See the note below.)
3. **Publish** — the new [frame](../architecture/glossary#frame) is encoded. [Subtree reuse](../architecture/subtree-reuse)
   means only the parts that actually changed are re-encoded; everything else travels as a tiny reuse marker.
4. **Diff + paint** — the native side diffs the incoming frame against the current tree and re-renders only the
   views whose nodes differ.

Steps 1–3 are largely bounded by *what changed*, which is why "a one-line text change costs about as much as
changing one line of text." Step 4 is the one to keep in mind on big screens: the native diff and the
SwiftUI/Compose layout pass still operate over the on-screen view hierarchy, so **the diff and paint costs grow
with the number of on-screen nodes**, even when only a small part changed.

## The lever that matters: node count

Because encode/serialize is already minimized by reuse, the practical lever on a heavy screen is the **number of
elements on screen**. Roughly in the order worth trying:

**1. Key your dynamic lists.** Give repeated items a `:native:key` so an insert or reorder is understood as one
changed item instead of every item changing — see [Subtree Reuse](../architecture/subtree-reuse). This is the
single highest-leverage change for list-shaped screens.

**2. Flatten the tree.** Every wrapper element is a node the diff and layout must consider. A row of a few
elements with alternating background stripes is cheaper than the same row wrapped in nested cards-within-cards.
Prefer the shallowest markup that gets the look you want.

**3. Split a very large screen into its own route.** The screen is the unit of re-render, so a smaller screen is
a cheaper interaction. If a single screen hosts several heavy sections (say a setup form *and* a long scored
checklist), giving the heavy section its own `Route::native(...)` step means a tap there re-renders only that
section's tree — not the whole page. This also tends to make multi-step flows clearer.

**4. Reach for child components for *reuse*, not for isolation.** Extracting a section into a child component (or
a Blade partial) is great for organisation and reuse, but it does **not** create an independent update boundary —
the owning screen still re-renders as a whole. Don't split into components expecting per-component reactivity;
split into *screens* if you need to shrink the re-render.

**5. Keep polling lean.** [Polling](reactivity#polling) drives a full re-render on every tick — keep intervals as
long as the UX allows, and prefer [events](../the-basics/events) for updates that arrive when something actually
happens.

## Measure, don't guess

The package ships a benchmark harness so you can see the real numbers instead of guessing. Point a route at it and
launch straight into it:

```php
use Native\Mobile\Edge\BenchmarkComponent;

Route::native('/bench', BenchmarkComponent::class);
```

@verbatim
```bash
php artisan native:run ios --start-url=/bench
```
@endverbatim

Run the **Counter Tap** (minimal screen) and **Large Tree Tap** (~200 nodes) scenarios and compare their pipeline
breakdown — **event**, **post** (diff), and **paint** are reported separately. The diff stage grows markedly with
node count, and on real hardware so does paint. That breakdown tells you which lever to pull: a big *event* number
points at bridge/transport, a big *diff/paint* number points at node count.

<aside>

Run the benchmark on a **real device** for meaningful numbers — a simulator/emulator has no real GPU frame clock,
so its absolute round-trip figures (and FPS) are unreliable even though the per-stage breakdown is still
directionally useful.

</aside>

<aside>

Most apps never need anything on this page. The reuse pipeline makes ordinary screens fast by default — reach for
these levers only when a specific screen measurably feels heavy, and confirm the win by measuring.

</aside>
