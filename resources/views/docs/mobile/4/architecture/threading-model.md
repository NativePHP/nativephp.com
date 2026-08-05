---
title: Threading Model
order: 60
---

A native app feels native when the UI thread is never kept waiting. SuperNative achieves that by giving every kind
of work its own lane and keeping the hand-offs between lanes tiny.

## The three threads

**The PHP thread.** All of your application code runs on a single dedicated thread that lives as long as your app
does. It hosts the [persistent runtime](glossary#persistent-runtime) — PHP, Composer and Laravel are booted once,
then kept warm. For each native screen, this thread runs the [runloop](glossary#runloop): render, publish, then
sleep until an event arrives. Between events it costs nothing.

**The reader thread.** Each platform has a background thread that receives published [frames](glossary#frame),
decodes them, and diffs them against the previous tree. It also **coalesces**: if PHP publishes several frames
while a diff is in progress, intermediate frames are dropped and only the newest is processed — the screen always
jumps to the latest state instead of queueing through stale ones.

**The UI thread.** The platform's main thread is the only one that touches views. It receives the diffed tree from
the reader thread and lets SwiftUI or Compose re-render the changed parts. Because encoding, decoding and diffing
all happened elsewhere, the UI thread's share of an update is as small as it can be.

## How a typical update flows

A tap lands on the UI thread → the press event is posted to the [event channel](glossary#wire-events) → the PHP
thread wakes, runs your handler, re-renders and publishes → the reader thread diffs → the UI thread mounts the
change. Four hand-offs, each passing a small, well-defined payload — and at no point does the UI thread wait on
PHP.

## When PHP isn't consulted at all

Continuous interactions never make that trip per frame:

- **Gestures and animations** driven by [SharedValues](glossary#sharedvalue) are evaluated directly on the UI
  thread at the display's frame rate. A drag updates its SharedValue and every view bound to it, natively; PHP
  receives one event when the gesture completes.
- **Scrolling, press feedback and transitions** are the platform's own, running exactly where the platform runs
  them.

Your PHP code could be querying the database mid-drag and the drag wouldn't stutter — the lanes don't share a
queue.

## Background work

Laravel queues run on their own embedded PHP instance — a separate **worker runtime** with its own thread,
processing jobs with `queue:work` semantics. A heavy job can't block a button press, because it isn't sharing a
runtime with the UI's PHP thread. Plugins can request additional short-lived runtimes for their own background
work.

## Lifecycle: booted once, kept warm

Booting a framework is the expensive part, so SuperNative avoids ever doing it twice:

- The persistent runtime boots on first launch and stays resident. Every screen, navigation and event reuses the
  already-booted Laravel app.
- On Android, where the OS routinely destroys and re-creates the activity (rotation, theme change, backgrounding),
  the runtime is **parked** rather than torn down: the runloop is asked to exit via a shutdown event, PHP stays
  booted, and the re-created activity re-attaches to it. Re-opening your app is a re-attach, not a re-boot.
- Hot reload during development is the one case where the runtime is genuinely restarted — with your navigation
  stack saved and restored around it.

<aside>

The PHP thread is singular on purpose. One writer, one ordered event queue, and atomic version counters on the
shared region mean the two worlds stay consistent without your code ever thinking about locks.

</aside>
