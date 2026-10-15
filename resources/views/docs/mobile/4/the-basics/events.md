---
title: Events
order: 200
---

## Overview

Screens react to things that happen outside a user tap — a push notification arrives, a websocket message lands,
a bridge call finishes. A component **listens** for these native events and updates its state in response; the
screen re-renders like any other state change.

This page covers listening for these native events from a component. It also covers where an event is heard, and
how an event that your own PHP code fires reaches a screen.

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

When several methods listen for one event, all of them run, in the order they are declared in the class. The methods
of a child class run before those of its parent. A method runs once per event, also when two of its attributes
match.

An `#[On]` method has to be public. A child class that overrides a listener of its parent has to repeat the attribute
on its own method, or that listener is lost.

### How a payload is bound

The payload of an event is bound to the parameters of your method by name:

- A scalar is cast to the type of the parameter, so `'3'` arrives as `3` for an `int`.
- A `null` stays `null` for a nullable parameter <x-docs.version-badge changed="TODO" />. For a parameter that does
  not allow `null`, it is cast like any other value, to `0`, `''` or `false`.
- A parameter typed with a backed enum gets the enum, made from its backing value.
- A key that your method has no parameter for is left out.

## Filtering with when

<x-docs.version-badge since="TODO" />

Pass `when` as the second argument of `#[On]` to hear an event only for certain values. The method runs when every
key of the filter is in the event's values, with the same value:

```php
use Native\Mobile\Attributes\On;
use Native\Mobile\Events\Alert\ButtonPressed;

#[On(ButtonPressed::class, when: ['id' => 'delete-confirm', 'label' => 'Delete'])]
public function onDeleteConfirmed(): void
{
    $this->deleteItem();
}
```

The filter follows these rules:

- Every key of the filter has to match. A key that the event does not carry is no match, also when the filter
  value is `null`.
- The comparison is strict. It uses the values as they arrive, before the types of your parameters convert them.
  So `1` does not match `'1'`, and `true` does not match `1`.
- Numbers are the one exception: two numbers match when they are equal as numbers. `when: ['ratio' => 1.0]` matches
  a device payload that carries `1`. A device writes a whole number without a fraction, and the same event class
  can come from PHP with a float.
- An empty filter (`when: []`) is no filter, and the method hears every event.
- The filter is part of an attribute, so its values are fixed when you write the class: literals, constants and
  enum cases. To compare with a value the screen holds, such as a route parameter, do that inside the method.

### Nested values

A dotted key reads a nested value:

```php
use App\Events\MessageReceived;
use Native\Mobile\Attributes\On;

// Payload: ['sender' => ['id' => 7, 'role' => 'support'], 'body' => 'Hi!']
#[On(MessageReceived::class, when: ['sender.role' => 'support'])]
public function fromSupport(string $body): void
{
    $this->supportMessages[] = $body;
}
```

The key reads into arrays. An event that PHP fires can also carry objects, and the key reads into those too: public
properties, the attributes of an Eloquent model and the items of a collection (`lines.0.sku` is the `sku` of the
first line). When the payload has a key that is literally `sender.role`, that key wins.

### Enums

A backed enum is compared by its backing value. This filter matches whether the event carries the enum or the
string `'critical'`:

```php
use Native\Mobile\Attributes\On;
use Native\Mobile\Events\Device\ThermalStateChanged;
use Native\Mobile\ThermalState;

#[On(ThermalStateChanged::class, when: ['state' => ThermalState::Critical])]
public function tooHot(): void
{
    $this->pauseSync();
}
```

An enum without a backing value only matches the same case.

### Several filters on one method

Repeat the attribute to give one method several filters. The method runs once when any of them matches:

```php
use App\Events\StockChanged;
use Native\Mobile\Attributes\On;

#[On(StockChanged::class, when: ['sku' => 'A'])]
#[On(StockChanged::class, when: ['low' => true])]
public function restock(string $sku): void
{
    $this->toRestock[] = $sku;
}
```

### When no filter matches

When a screen listens for an event only through filters and none of them matches, no method runs. The screen also
does not re-render, unless something else is there for the event:

- a closure registered with `->on()`;
- a fluent callback that waits for it, such as [`->buttonPressed()`](dialogs#handling-button-presses) on an alert;
- a Laravel listener that is registered for the event class, or for an interface that the class implements.

Any of these may have changed something that the screen shows, so the screen is drawn again.

A registered Laravel listener counts whether it runs at once or is queued. A wildcard listener, as logging tools
register for every event, does not count. Laravel only hears a device event that
[broadcasts globally](#broadcasting-globally), so for any other device event a Laravel listener plays no part.

The package itself listens for `AppearanceChanged`, `OrientationChanged` and `ThermalStateChanged`, to keep `System`
and `Device` up to date. So a screen always re-renders after those three, also when its own filter misses.

For an event that nothing else listens for, a miss costs nothing. That is the point of `when` for an event that fires
often: the screen is left alone until a value it cares about comes in.

All of this holds for an event from the device, and for one that reaches the screen from a
[queued job, an async task or a web view](#events-from-queued-jobs-and-async-tasks).

### Component events

For a [component event](nested-components#events-up), the filter is matched against the named arguments of `emit()`
or `dispatch()`. `$this->emit('item-picked', id: 5)` matches `when: ['id' => 5]`. A positional argument has no name,
so `$this->emit('item-picked', 5)` does not match.

A component event is part of an interaction, so the screen renders whether or not a filter matched.

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
its screen is the one on top.

That last part is also the limit of this page. If something outside your screens needs to follow what the user
is doing — analytics, telemetry, crash breadcrumbs — listen for the app-wide
[screen lifecycle events](../digging-deeper/lifecycle-hooks#observing-the-lifecycle-from-outside) instead.

<aside>

You can drive events in tests without a device — `emitNative(Event::class, [...])` delivers one straight to the
component. See [Native Events & the Bridge](../testing/native-events).

</aside>

### How a device event arrives

On a native screen, a device event comes in through the native event queue. That queue is kept in memory, between the
native side and the screen that is showing. It is not Laravel's queue and nothing of it is in the database. It lasts
as long as the native screen session: when that ends, what still waited in it is gone.

A web view app does not use that queue. There the device posts each event to `/_native/api/events`, where a
controller of the package builds the event from its payload and calls `event()`. So in a web view app Laravel's
listeners hear every device event.

### The order on a native screen

One device event is handed out in this order:

1. the fluent callback that waits for it, which runs once;
2. the closures registered with `->on()`;
3. Laravel's listeners, when the event [broadcasts globally](#broadcasting-globally);
4. the `#[On]` methods of the screen.

## Where an event is heard

An event starts in one of two places: on the device, or in PHP with Laravel's `event()`. It stays on the side where
it started, unless its class implements [`BroadcastsGlobally`](#broadcasting-globally). Then both sides hear it.

| Direction | What happens | Needs `BroadcastsGlobally` |
| --- | --- | --- |
| Device to screen | The device sends an event, and the `#[On]` methods of the live screen run | No |
| Device to Laravel | That event is also sent through `event()`, so `Event::listen` hears it | Yes |
| Screen to Laravel | A method on your screen calls `event()`. Laravel's listeners hear it, as in any Laravel app | No |
| Laravel to screen | An event fired with `event()` also runs the `#[On]` methods of the live screen | Yes |

The table is about native screens. In a web view app, Laravel hears every device event without the marker, see
[How a device event arrives](#how-a-device-event-arrives).

### The live screen

Events go to the live screen, the one on top of the stack. A screen that is covered by another screen hears nothing,
and nothing is kept for it: when the user comes back, the events it missed do not arrive. Read the state again in
[`onResume()`](../digging-deeper/lifecycle-hooks#onresume):

```php
use App\Models\Order;

public function onResume(): void
{
    // Events that fired while this screen was covered never reached it.
    $this->orders = Order::latest()->get()->toArray();
}
```

A device event reaches the screen only, never a nested component. The same goes for a marked event from `event()`.
The screen hears them and hands what a child needs down as a [prop](nested-components#props).

## Broadcasting globally

`BroadcastsGlobally` is a marker interface without methods. An event class that implements it is heard in both
places at once: by the `#[On]` methods of the live screen and by Laravel's listeners.

"Globally" means those two places. It does not mean every PHP runtime of your app, see
[One way, towards the main runtime](#one-way-towards-the-main-runtime).

```php
namespace App\Events;

use Native\Mobile\Events\Concerns\BroadcastsGlobally;

class OrderUpdated implements BroadcastsGlobally
{
    public function __construct(
        public int $orderId,
        public string $status,
    ) {}
}
```

An event without the interface stays on the side where it started.

### From the device to Laravel

A marked event that arrives from the device is also sent through `event()`. `Event::listen` hears it next to the
screen's `#[On]` methods, so code anywhere in your app can react:

```php
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Native\Mobile\Events\System\AppearanceChanged;

Event::listen(AppearanceChanged::class, function (AppearanceChanged $event) {
    Cache::forget('theme');
});
```

Laravel's listeners hear the event whether or not the live screen has an `#[On]` method for it.

### From Laravel to the screen

<x-docs.version-badge since="TODO" />

A marked event that PHP fires with `event()` also reaches the live screen. Its public properties are the payload.
They are bound to the method's parameters by name and matched against `when`, as the payload of a device event is:

```php
use App\Events\OrderUpdated;

event(new OrderUpdated(orderId: 42, status: 'shipped'));
```

```php
use App\Events\OrderUpdated;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\NativeComponent;

class OrdersScreen extends NativeComponent
{
    public array $statuses = [];

    #[On(OrderUpdated::class)]
    public function onOrderUpdated(int $orderId, string $status): void
    {
        $this->statuses[$orderId] = $status;
    }
}
```

The method does not run inside `event()`. It runs after the current handler returns, on the screen's next turn. The
screen then renders once for everything that arrived, so a burst of events costs one render.

An event that a handler fires right before it navigates away reaches no screen. The screen it leaves is not live
any more, and the next screen is not live yet. Laravel's listeners still hear it.

<aside>

This direction needs Laravel 10.30 or newer, and Laravel's own event dispatcher. If your app replaces the dispatcher
with a class of its own, marked events that PHP fires do not reach a screen, and a warning in the log says so.

</aside>

### Package events that broadcast globally

These events of the package carry the marker:

| Event | Fires when | Arguments | More |
| --- | --- | --- | --- |
| `Native\Mobile\Events\System\AppearanceChanged` | The system switches between light and dark | `mode` | [Theming](../digging-deeper/theming#reacting-to-changes) |
| `Native\Mobile\Events\System\OrientationChanged` | The app window turns to portrait or landscape | `orientation` | [System](system#orientation) |
| `Native\Mobile\Events\Device\ThermalStateChanged` | The thermal state of the device changes | `state`, `previous` | [Device](device#events) |
| `Native\Mobile\Events\Motion\ShakeDetected` | The user shakes the device | None | [Device](device#events) |

Any other event class can implement the interface too: one of your own, or one in a plugin that you write.

## Events from queued jobs and async tasks

<x-docs.version-badge since="TODO" />

A [queued job](../digging-deeper/queues), an [async task](../digging-deeper/async-tasks) and a
[web view inside a native screen](../edge-components/web-view#php-mode) each run in a PHP runtime of their own inside
the app. A marked event that is fired there is carried to the main runtime, the one that runs your screens, and the
live screen hears it:

```php
namespace App\Jobs;

use App\Events\OrderUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ShipOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        // The slow work happens here.

        event(new OrderUpdated(orderId: $this->orderId, status: 'shipped'));
    }
}
```

The `OrdersScreen` above hears this event without a change.

The rules below say "the job". They hold in the same way for an async task and for a web view.

### One way, towards the main runtime

"Globally" does not mean everywhere. A marked event is heard in the main runtime: by the `Event::listen` listeners
there and by the live screen. It is never sent out to a queued job, an async task or the runtime of a web view that
happens to be running, and not from one job to another.

### Where the listeners run

When an event is carried over, its `Event::listen` listeners run in the main runtime, and not in the job. In the job,
`event()` then returns an empty array, as it does when nothing listens.

For a web view this means that the listeners run outside the request that fired the event. Do not count on that
request, its session or its authenticated user in such a listener.

### Only while a native screen is showing

An event is carried over only when a native screen is showing at that moment. Otherwise it stays in the job and its
listeners run there, as for any Laravel event. No warning is logged for that, and nothing is kept for a screen that
opens later.

### What the event can carry

The event is carried over as itself, the way Laravel carries the data of a queued job: it is serialized where it was
fired and unserialized in the main runtime. So:

- models, dates and collections arrive, and a method on the screen can type-hint them;
- properties that are not constructor parameters arrive too.

On a device the event travels in memory: nothing of it is written to disk or to the database. It is signed with
your app key, and the main runtime checks that signature before it reads the event.

### Events that hold a model

Put the `SerializesModels` trait on a marked event that holds a model. Then only the class and the key of the model
travel, and the main runtime fetches the model again, as a queued job does. That gives you two things: the model is
fresh when the screen gets it, and the event stays small.

```php
namespace App\Events;

use App\Models\Order;
use Illuminate\Queue\SerializesModels;
use Native\Mobile\Events\Concerns\BroadcastsGlobally;

class OrderRefunded implements BroadcastsGlobally
{
    use SerializesModels;

    public function __construct(public Order $order) {}
}
```

```php
use App\Events\OrderRefunded;
use App\Models\Order;
use Native\Mobile\Attributes\On;

#[On(OrderRefunded::class)]
public function onRefunded(Order $order): void
{
    $this->statuses[$order->id] = $order->status;
}
```

Measured for an event that holds one model:

| The model | Without the trait | With `SerializesModels` |
| --- | --- | --- |
| 10 short columns | 2.3 KB | about 220 bytes |
| 60 columns of 200 characters | 28 KB | about 220 bytes |
| 200 columns of 500 characters | 211 KB | about 220 bytes |

Without the trait, a model takes about twice the size of its data, because Eloquent keeps the attributes and a copy
of their original values.

If the row is gone when the event arrives, the event is dropped: nothing hears it, and a warning is logged.

### When an event cannot be carried over

An event that cannot be carried over never throws. It stays in the job, and its listeners run there. That happens,
with a warning in the log, when:

- the event cannot be serialized, in practice because it holds a closure;
- the event is too large (`SerializesModels` keeps an event that holds a model small);
- the app has no usable app key to sign the event with;
- the native event queue is full (see [A burst of events](#a-burst-of-events)).

Such a warning is logged once, not for every event. The event also stays in the job, without a warning, when no
native screen is showing.

### A burst of events

Each event from a job arrives on its own and, when something hears it, costs its own render. So a burst of fifty
events from a job costs fifty renders, where the same burst fired in the main runtime costs one.

A large burst also runs into the size of the native event queue. The queue holds 1,024 events, or 8 MB of them, and
taps and other device events share it with the events from jobs, async tasks and web views. A job in a tight loop
fires events much faster than the main runtime takes them, so a loop of more than about a thousand events fills the
queue by itself. An event that finds the queue full stays in the job: its listeners run there, and the screen does
not hear it.

So do not fire one event per item in a tight loop. Fire the end result, or a progress event every so often.

A full queue can still lose an event: a tap that arrives while it is full pushes out the oldest event that waits.

### Limits

- Livewire components in a web view do not hear a marked event that PHP fires. Only native screens do.
- Under [Jump](jump), PHP runs on your machine. A marked event that an async task fires reaches the screen there. One
  from a job that `php artisan queue:work` runs on your machine does not, and neither does one from a web view: both
  stay where they were fired.
- Under Jump, an async task cannot tell whether a native screen is showing. Its event leaves the task either way: its
  listeners do not run in the task, and the event waits until a screen takes it, however late that is.
