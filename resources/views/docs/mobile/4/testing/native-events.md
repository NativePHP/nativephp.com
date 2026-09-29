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
