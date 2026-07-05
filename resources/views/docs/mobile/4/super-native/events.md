---
title: Events
order: 40
---

## Overview

Screens react to things that happen outside a user tap — a push notification arrives, a websocket message lands,
a bridge call finishes. A component **listens** for these native events and updates its state in response; the
screen re-renders like any other state change.

This page covers listening from a component. For the async bridge-response side (calling a native API and handling
its result or a custom event class), see [Events](../the-basics/events).

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

## Where events come from

Native events originate on the device side and are delivered to whichever screen is alive: plugin events (a
[Vibe](../plugins/vibe) websocket message, a push notification tap), bridge-call completions, and any custom
events an async native call resolves with. Because delivery targets the live screen, a listener only fires while
its screen is on the stack.

<aside>

You can drive events in tests without a device — `emitNative(Event::class, [...])` delivers one straight to the
component. See [Native Events & the Bridge](../testing/native-events).

</aside>
