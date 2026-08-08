---
title: Events
order: 200
---

## Overview

Screens react to things that happen outside a user tap — a push notification arrives, a websocket message lands,
a bridge call finishes. A component **listens** for these native events and updates its state in response; the
screen re-renders like any other state change.

This page covers listening for these native events from a component.

## Listening with #[On]

Annotate a method with `#[On(EventClass::class)]` and it runs whenever that event fires. The method's parameters
are bound **by name** from the event's public properties:

```php
use Native\Mobile\Attributes\On;
use NativePHP\Vibe\Events\MessageReceived;

class ChatScreen extends NativeComponent
{
    public array $messages = [];

    #[On(MessageReceived::class)]
    public function onMessage(string $body, string $from): void
    {
        $this->messages[] = ['body' => $body, 'from' => $from];
    }
}
```

`#[On]` is repeatable — stack several on one method to handle multiple events, or put several methods on the same
event. Listeners are torn down automatically when the screen unmounts, so they never leak onto the next screen.

Pass the bare event class, as above. The older `'native:'`-prefixed spelling — `#[On('native:' . MessageReceived::class)]` —
also works: the prefix is applied idempotently, so existing prefixed examples keep behaving identically and are never
double-prefixed.

## Listening with ->on()

For a listener you register at runtime — for example inside `mount()`, or conditionally — use the fluent `->on()`
method with a closure:

```php
public function mount(): void
{
    $this->on(OrderShipped::class, function ($event) {
        $this->status = "Shipped: {$event->trackingNumber}";
    });
}
```

Use `#[On]` for the common case (a fixed listener declared on the class) and `->on()` when you need to wire one up
dynamically.

## Callbacks on native calls

Async native APIs return a `Pending*` builder you can chain outcome callbacks onto directly, instead of declaring a
listener on the class. Each outcome event gets a fluent method named after the event class:

```php
use Native\Mobile\Facades\Camera;

Camera::getPhoto()
    ->photoTaken(fn ($event) => $this->path = $event->path)
    ->photoCancelled(fn () => $this->status = 'cancelled');
```

The callback receives the event object and fires once — the first outcome consumes the registration, so a success
callback and its cancel/denied siblings are mutually exclusive.

Every builder also has `onSuccess()`, generic sugar for that builder's success event. It reads the same on every
builder, so you don't need to remember which event a given API resolves with:

```php
use Native\Mobile\Events\Gallery\MediaSelected;

Camera::pickImages('image')->onSuccess(function (MediaSelected $media) {
    $this->images = $media->files;
});
```

`onSuccess()` registers the exact same callback the event-named form would — `->mediaSelected(...)`,
`->photoTaken(...)` and friends are unchanged, and if you override the outcome with `->event(Custom::class)`,
`onSuccess()` follows the override.

## Callback durability

The process that registers a callback may not be the process that receives the result — the OS can kill the app
while the camera or picker is in the foreground. Callbacks are therefore stored in two tiers: a warm in-memory copy,
plus a durable serialized copy in the cache. When the event arrives in a fresh process, the durable copy fires on the
live component as if nothing happened.

The three callback forms differ in how robustly they cross that boundary:

- **Closures** — including ones that use `$this` — are serialized durably; at fire time the closure is rebound to the
  live component, so `$this` works again, private members included. Captured `use` variables must themselves be
  serializable (a resource or PDO handle can't cross), and closures defined in eval'd code can't round-trip. When
  serialization fails, the warm copy still fires — the callback just won't survive a kill.
- **Method-name strings** — `->mediaSelected('onMediaSelected')` resolves the method on the live component when the
  event arrives. Serializes trivially, so this is the most robust form.
- **Invokable class-strings** — resolved from the container and invoked with the event.

<aside>

Durability requires a persistent cache store. With an in-memory cache driver, callbacks still fire normally — they
just die with the process.

</aside>

## Where events come from

Native events originate on the device side and are delivered to whichever screen is alive: plugin events (a
[Vibe](../digging-deeper/websockets) websocket message, a push notification tap), bridge-call completions, and any custom
events an async native call resolves with. Because delivery targets the live screen, a listener only fires while
its screen is on the stack.

Listeners for the same event are guarded independently: a [validation](validation) failure inside one listener
records its errors and aborts that listener only — the other listeners still receive the event.

<aside>

You can drive events in tests without a device — `emitNative(Event::class, [...])` delivers one straight to the
component. See [Native Events & the Bridge](../testing/native-events).

</aside>
