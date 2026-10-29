---
title: Native Events & the Bridge
order: 300
---

## Overview

Native APIs are asynchronous: your screen calls the bridge, the device does its work, and later an event comes back
carrying the result. The testing suite lets you drive both halves of that round trip deterministically — you deliver
the event yourself, and you assert on the calls the component made.

The `FakeBridge` records every native call in the order it happened, and can answer synchronous calls with scripted
responses. You reach its assertions through the harness, or the bridge itself through `bridge()`.

## Delivering a native event

`emitNative($event, $payload = [])` delivers a native event — what the device sends when a bridge API completes or a
plugin pushes an event. It fires `#[On]` listeners, fluent `->on()` closures, and any pending `then()` / `catch()`
callbacks the component chained onto its bridge call, then re-renders.

No re-render follows an event that the screen hears only through `when` filters, when none of them matches and
nothing else is there for the event. See [Testing a when filter](#testing-a-when-filter).

Pass the event class and its payload:

```php
use Native\Mobile\Events\Motion\ShakeDetected;

it('counts device shakes delivered as native events', function () {
    Native::visit('/')
        ->emitNative(ShakeDetected::class)
        ->emitNative(ShakeDetected::class)
        ->assertSet('shakes', 2)
        ->assertSee('Shaken 2×!');
});
```

## Testing a when filter

<x-docs.version-badge since="TODO" />

`emitNative()` hands the payload to the screen as you wrote it, so a
[`when` filter](../the-basics/events#filtering-with-when) sees the values and types that you pass. The comparison is
strict: write `42` and not `'42'` when the device sends a number. Only two numbers match across types, so `5` passes a
filter on `5.0`.

Deliver one event that the filter lets through and one that it does not:

```php
use Native\Mobile\Events\Alert\ButtonPressed;

// ItemScreen: #[On(ButtonPressed::class, when: ['id' => 'delete-confirm', 'label' => 'Delete'])]
it('deletes the item only when Delete is pressed', function () {
    Native::test(ItemScreen::class)
        ->emitNative(ButtonPressed::class, ['index' => 0, 'label' => 'Cancel', 'id' => 'delete-confirm'])
        ->assertSet('deleted', false)
        ->assertNotRerendered()
        ->emitNative(ButtonPressed::class, ['index' => 1, 'label' => 'Delete', 'id' => 'delete-confirm'])
        ->assertSet('deleted', true)
        ->assertRerendered();
});
```

A screen that listens for an event only through filters does not re-render when none of them matches, and the
harness does the same: `emitNative()` then renders no new frame. Pin that down with `assertNotRerendered()` or
`assertRenderCount()`, see [Render-count guards](advanced#render-count-guards).

The exception is an event that something else is there for: a closure registered with `->on()`, a fluent callback
that waits for it, or a Laravel listener that is registered for the event. The screen then re-renders, also on a
miss.

The package itself listens for `AppearanceChanged`, `OrientationChanged` and `ThermalStateChanged`, so do not expect
`assertNotRerendered()` to pass after those three. An `Event::listen` in your app for an event that broadcasts
globally has the same effect, and `Event::fake()` does not change that.

## Events that broadcast globally

An event that implements [`BroadcastsGlobally`](../the-basics/events#broadcasting-globally) is heard by the live
screen and by Laravel's listeners. Test each direction from the side where the event starts.

### From the device

`emitNative()` delivers the event to the screen, and a marked event is also sent through `event()`. Fake Laravel's
events to assert on that half. The screen still hears the event:

```php
use Illuminate\Support\Facades\Event;
use Native\Mobile\Events\System\AppearanceChanged;

it('hears a theme change on the screen and in Laravel', function () {
    Event::fake();

    Native::test(SettingsScreen::class)
        ->emitNative(AppearanceChanged::class, ['mode' => 'dark'])
        ->assertSet('mode', 'dark');

    Event::assertDispatched(AppearanceChanged::class);
});
```

### Fired from PHP

<x-docs.version-badge since="TODO" />

The screen under test is the live screen. Fire the event with `event()` in the body of the test, and the harness
delivers it to the screen's `#[On]` methods before the next assertion, with one render:

```php
use App\Events\OrderUpdated;

it('shows an order that shipped', function () {
    $screen = Native::test(OrdersScreen::class);

    event(new OrderUpdated(orderId: 42, status: 'shipped'));

    $screen->assertSet('statuses', [42 => 'shipped'])
        ->assertRerendered();
});
```

When a method of the screen fires the event, its `#[On]` method runs after that method has returned, and the
interaction still renders once:

```php
it('renders once when a handler fires the event', function () {
    Native::test(OrdersScreen::class)
        ->call('ship', 42)   // ship() fires event(new OrderUpdated(42, 'shipped'))
        ->assertSet('statuses', [42 => 'shipped'])
        ->assertRenderCount(2);
});
```

Two things to keep in mind:

- `Event::fake()` holds a marked event back, so it reaches no screen and no listener. To test the screen's side,
  leave the event out of the fake, for example with `Event::fake([OtherEvent::class])`.
- After `follow()`, the destination is the live screen. The screen underneath hears nothing, as on a device.

## Asserting on native calls

After an interaction, assert on what the component sent to the bridge:

- `assertNativeCalled($method, $paramsFilter = null)` — the method was called; an optional closure receives the
  decoded params of each call to narrow the match.
- `assertNativeNotCalled($method)` — the method was never called.
- `assertNativeCalledTimes($method, $times)` — it was called exactly that many times.
- `assertNativeCallOrder($methods)` — the given methods appear in this relative order (other calls may interleave).

```php
use Native\Mobile\Events\Alert\ButtonPressed;

it('alerts then toasts, in that order', function () {
    Native::test(AlertDemo::class)
        ->call('confirmAlert')
        ->emitNative(ButtonPressed::class, ['index' => 1, 'label' => 'Delete'])
        ->assertNativeCalledTimes('Dialog.Alert', 1)
        ->assertNativeCallOrder(['Dialog.Alert', 'Dialog.Toast']);
});
```

The params filter lets you assert on exactly what was sent:

```php
->assertNativeCalled('SecureStorage.Set', fn (array $p) => $p['key'] === 'api-key');
```

## Asserting a pending callback

When a screen chains a fluent handler onto a bridge call — `->locationReceived(...)`, `->photoTaken(...)` — a callback
is registered and waits for that native event. You can assert on the wait itself:

- `assertAwaitingNativeEvent($eventClass)` — a callback is registered and waiting for this event.
- `assertNotAwaitingNativeEvent($eventClass)` — no callback awaits it (e.g. before the call, or after the one-shot
  fired).

This is ideal for a full geolocation round trip: press, confirm the wait, deliver the event, confirm the wait is
consumed and the state landed.

```php
use Native\Mobile\Events\Geolocation\LocationReceived;

it('tracks the pending location callback through its lifecycle', function () {
    Native::test(GeolocationDemo::class)
        ->assertNotAwaitingNativeEvent(LocationReceived::class)
        ->press('get-position')
        ->assertSet('locating', true)
        ->assertAwaitingNativeEvent(LocationReceived::class)
        ->emitNative(LocationReceived::class, [
            'success' => true,
            'latitude' => 48.85,
            'longitude' => 2.35,
        ])
        ->assertNotAwaitingNativeEvent(LocationReceived::class)
        ->assertSet('latitude', 48.85);
});
```

## Scripting synchronous responses

Some bridge calls return a value straight away rather than firing an event later. Script those with `respondTo()` on
the `FakeBridge`. The response shape is what the native side would return — an array becomes JSON on the way back to
the component:

```php
it('turns on when the bridge confirms the toggle', function () {
    Native::fakeBridge()->respondTo('Device.ToggleFlashlight', [
        'success' => true,
        'state' => true,
    ]);

    Native::test(FlashlightDemo::class)
        ->call('toggle')
        ->assertSet('on', true);
});
```

`respondTo()` also accepts a closure, which receives the decoded call params and returns the response — handy when the
answer depends on the request.

## Scripting before mount

Screens often read the bridge during `mount()` to hydrate themselves. Because those calls happen before you get a
harness back, script them up front with `Native::fakeBridge()` — it enables the bridge and returns it so you can
`respondTo()` before mounting:

```php
it('hydrates device info at mount from scripted bridge responses', function () {
    Native::fakeBridge()
        ->respondTo('Device.GetId', ['id' => 'test-device-123'])
        ->respondTo('Device.GetInfo', ['info' => json_encode(['model' => 'iPhone 17 Pro', 'os' => 'iOS 26'])])
        ->respondTo('Device.GetBatteryInfo', ['info' => json_encode(['level' => 0.8, 'state' => 'charging'])]);

    Native::test(DeviceDemo::class)
        ->assertSet('deviceId', 'test-device-123')
        ->assertSet('info', ['model' => 'iPhone 17 Pro', 'os' => 'iOS 26'])
        ->assertSet('battery', ['level' => 0.8, 'state' => 'charging']);
});
```

<aside>

A component that reads the bridge at mount but has no scripted response still renders — the call simply returns
nothing. Write a test for that path too, so an unavailable API is a graceful empty state rather than a crash.

</aside>

## Reaching the bridge directly

For assertions the harness doesn't wrap, `bridge()` returns the `FakeBridge` itself. It exposes `assertNothingCalled()`,
the recorded `calls` and `publishes`, `callsTo($method)`, and `lastPublish()`:

```php
$screen = Native::test(HapticsDemo::class)->tap('vibrate-card');

expect($screen->bridge()->callsTo('Device.Vibrate'))->toHaveCount(1);
```
