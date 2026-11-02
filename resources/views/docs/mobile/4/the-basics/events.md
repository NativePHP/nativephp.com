---
title: Events
order: 200
---

## Overview

Screens react to things that happen outside a user tap — a push notification arrives, a websocket message lands,
a bridge call finishes. A component **listens** for these native events and updates its state in response; the
screen re-renders like any other state change.

This page covers listening for native events from a component and forwarding them to Laravel listeners across your app.

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
[Vibe](../digging-deeper/websockets) websocket message, a push notification tap), bridge-call completions, and any custom
events an async native call resolves with. Because delivery targets the live screen, a listener only fires while
its screen is on the stack.

## App-wide Laravel listeners

On an EDGE screen, `#[On]` and `->on()` receive the native event's payload directly. Ordinary native events do
not automatically pass through Laravel's event dispatcher.

An event class can opt into Laravel dispatch by implementing `Native\Mobile\Events\Concerns\BroadcastsGlobally`.
When that native event arrives, NativePHP constructs the event object from its payload and calls Laravel's
`event($event)` in addition to delivering it to the component. Laravel listeners run even if the active component
has no `#[On]` handler for that event.

`AppearanceChanged` and `ShakeDetected` already implement this interface. For example, register an appearance
listener in your `AppServiceProvider::boot()` method:

```php
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Native\Mobile\Events\System\AppearanceChanged;

public function boot(): void
{
    Event::listen(AppearanceChanged::class, function (AppearanceChanged $event): void {
        Cache::put('preferred-appearance', $event->mode);
    });
}
```

The Laravel listener receives an event object. A component's `#[On(AppearanceChanged::class)]` method still
receives individual payload fields, such as `string $mode`, bound by parameter name.

### Opting in with a custom event

Implement the interface on the PHP event class emitted by your native code:

```php
namespace App\Events;

use Native\Mobile\Events\Concerns\BroadcastsGlobally;

class ConnectionChanged implements BroadcastsGlobally
{
    public function __construct(public bool $connected) {}
}
```

Your native code must emit this event's fully qualified class name with a payload such as `['connected' => true]`.
NativePHP matches payload keys to constructor parameter names. Supply every required constructor argument; if
NativePHP cannot construct the event object, it skips Laravel dispatch. The interface has no methods to implement.

You can then register `Event::listen(ConnectionChanged::class, ...)` in a service provider and keep using
`#[On(ConnectionChanged::class)]` in a component. Implementing the interface alone does not emit an event.

<aside>

`BroadcastsGlobally` belongs to NativePHP. It forwards native events to Laravel listeners within the running app;
it does not broadcast over WebSockets like Laravel's `ShouldBroadcast`. It also does not keep the app running
in the background: the native event must still reach the active app runtime.

</aside>

WebView events delivered through `/_native/api/events` already pass through Laravel's event dispatcher without
this interface. `BroadcastsGlobally` opts an event into that behavior on EDGE screens.

For analytics, telemetry, or crash breadcrumbs tied to screen navigation, use the separate
[screen lifecycle events](../digging-deeper/lifecycle-hooks#observing-the-lifecycle-from-outside).

<aside>

You can drive events in tests without a device — `emitNative(Event::class, [...])` delivers one straight to the
component. See [Native Events & the Bridge](../testing/native-events).

</aside>
