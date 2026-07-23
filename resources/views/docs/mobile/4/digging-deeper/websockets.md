---
title: WebSockets
order: 350
---

<aside>

WebSockets functionality is provided by the (free) [Vibe plugin](../plugins/core/vibe). To use this feature, you must
install the `nativephp/mobile-vibe` Composer package.

</aside>

## Overview

Vibe brings live server events into your NativePHP Mobile app over the **Pusher protocol** — so it works with
**[Vask](https://vask.dev)**, **[Laravel Reverb](https://reverb.laravel.com)**, or **Pusher** without changing your
code. Your components subscribe to channels and react to broadcasts, exactly like Laravel Echo does in the browser.

The websocket lives on the native side (Swift/Kotlin, via the official Pusher SDKs — PusherSwift on iOS,
`pusher-java-client` on Android). PHP is purely a client subscriber: it declares what to subscribe to and handles the
events that arrive. Mobile apps never broadcast — they only listen.

Events arrive as native events and re-render the component that subscribed to them.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Nativephp\Vibe\Facades\Vibe;
```

</x-snippet.tab>
</x-snippet>

## Installation

```shell
composer require nativephp/mobile-vibe
php artisan native:plugin:register nativephp/mobile-vibe
```

Then rebuild your app so the plugin's native code is compiled in:

```shell
php artisan native:run
```

## Configuration

Vibe reads the standard Laravel `PUSHER_*` variables from your app's `.env`. These are bundled into the app at build
time.

```dotenv
PUSHER_APP_KEY=your-app-key
PUSHER_HOST=wss.vask.dev      # the WEBSOCKET host
PUSHER_PORT=443
PUSHER_SCHEME=https
```

<aside>

The app **secret is never shipped to the device**. Only the public key and connection details are bundled. Signing for
private and presence channels happens on your remote backend — see the private and presence sections below.

</aside>

For private and presence channels, also point Vibe at your backend's authorization endpoint:

```dotenv
VIBE_AUTH_ENDPOINT=https://your-backend.example.com/api/v1/broadcasting/auth
```

## Subscribing to channels

Subscribe inside a `NativeComponent`'s `mount()` method. The closures you pass run as component methods, so `$this`
refers to the component and any property you assign triggers a re-render.

Subscriptions are torn down automatically when the component unmounts — leave the screen and you leave the channel (or
presence room).

### Public channels

`Vibe::channel()` subscribes to a public channel. Chain `->on()` to react to a broadcast. The `$event` argument is a
plain object of the broadcast payload, so you read fields with `$event->field`.

<x-snippet title="Public Channel">

<x-snippet.tab name="PHP">

```php
use Nativephp\Vibe\Facades\Vibe;

class OrderStatus extends NativeComponent
{
    public string $status = 'pending';

    public function mount(): void
    {
        Vibe::channel('orders')->on('OrderShipped', function ($event) {
            $this->status = $event->status;   // $this is the component
        });
    }
}
```

</x-snippet.tab>
</x-snippet>

The event name must match what your server broadcasts.

<aside>

Add a `broadcastAs()` method to your Laravel event to keep the name short and stable. Without it, the name defaults to
the event's fully-qualified class name, which you'd then have to match exactly on the device.

</aside>

### Authentication for private and presence channels

Private (`private-`) and presence (`presence-`) channels require a signed authorization from **your remote Laravel
backend** — a `/broadcasting/auth` endpoint guarded by `auth:sanctum`, with channel authorization callbacks. The app
authenticates to it with a bearer token.

Register a token resolver **once** (for example in `AppServiceProvider::boot()`). It returns the current bearer token
and is called at subscribe time.

<x-snippet title="Register a Token Resolver">

<x-snippet.tab name="PHP">

```php
use Nativephp\Vibe\Facades\Vibe;
use Native\Mobile\Facades\SecureStorage;

// AppServiceProvider::boot()
Vibe::resolveTokenUsing(fn () => SecureStorage::get('api_token'));
```

</x-snippet.tab>
</x-snippet>

After a re-login or token refresh, push the fresh token to the live connection so subsequent subscriptions authorize
with it:

<x-snippet title="Refresh the Token">

<x-snippet.tab name="PHP">

```php
Vibe::withToken($freshToken);
```

</x-snippet.tab>
</x-snippet>

<aside>

Store tokens with the [SecureStorage](../plugins/core/secure-storage) plugin so they live in the device keychain/keystore rather
than in plain application state.

</aside>

### Private channels

`Vibe::private()` auto-prefixes the channel name with `private-` and authorizes it through your endpoint. Otherwise it
behaves like a public channel.

<x-snippet title="Private Channel">

<x-snippet.tab name="PHP">

```php
use Nativephp\Vibe\Facades\Vibe;

class OrderStatus extends NativeComponent
{
    public string $status = 'pending';

    public function mount(): void
    {
        Vibe::private('orders.42')->on('OrderShipped', function ($event) {
            $this->status = $event->status;
        });
    }
}
```

</x-snippet.tab>
</x-snippet>

### Presence channels

`Vibe::presence()` auto-prefixes the name with `presence-` and, on top of events, tracks who is in the room. The auth
response carries `channel_data` (a member's id and info), which Vibe surfaces through three lifecycle callbacks:

- `->here()` runs once on join with the full roster
- `->joining()` runs when someone joins
- `->leaving()` runs when someone leaves

Each member is an array shaped `['id' => ..., 'info' => [...]]`. You can still chain `->on()` to handle broadcasts on the
same channel.

<x-snippet title="Presence Channel">

<x-snippet.tab name="PHP">

```php
use Nativephp\Vibe\Facades\Vibe;

class ChatRoom extends NativeComponent
{
    public array $online = [];
    public array $messages = [];

    public function mount(): void
    {
        Vibe::presence('room.1')
            ->here(fn (array $members) => $this->online = $members)                    // each: ['id' => ..., 'info' => [...]]
            ->joining(fn (array $member) => $this->online[] = $member)
            ->leaving(function (array $member) {
                $this->online = array_values(array_filter(
                    $this->online,
                    fn ($m) => $m['id'] !== $member['id'],
                ));
            })
            ->on('MessageSent', fn ($event) => $this->messages[] = $event->body);
    }
}
```

</x-snippet.tab>
</x-snippet>

## Handling events with `#[OnEcho]`

As an alternative to the fluent `->on()` callback, you can annotate a public component method with the `#[OnEcho]`
attribute. Method parameters are bound by name from the broadcast payload.

<x-snippet title="OnEcho Attribute">

<x-snippet.tab name="PHP">

```php
use Nativephp\Vibe\Facades\Vibe;
use Nativephp\Vibe\Attributes\OnEcho;

class OrderStatus extends NativeComponent
{
    public string $status = 'pending';

    public function mount(): void
    {
        Vibe::channel('orders');   // subscribe; no ->on() needed
    }

    #[OnEcho('OrderShipped')]
    public function whenShipped(string $status): void
    {
        $this->status = $status;
    }
}
```

</x-snippet.tab>
</x-snippet>

## Methods

All methods are called on the `Vibe` facade. `channel()`, `private()`, `presence()`, and `subscribe()` return a
`PendingSubscription` you can chain.

### `channel()`

Subscribes to a public channel.

**Parameters:**
- `string $name` - The channel name

**Returns:** `PendingSubscription`

### `private()`

Subscribes to a private channel, auto-prefixing the name with `private-`. Requires authentication.

**Parameters:**
- `string $name` - The channel name (without the `private-` prefix)

**Returns:** `PendingSubscription`

### `presence()`

Subscribes to a presence channel, auto-prefixing the name with `presence-`. Requires authentication and tracks members.

**Parameters:**
- `string $name` - The channel name (without the `presence-` prefix)

**Returns:** `PendingSubscription`

### `subscribe()`

Low-level primitive. You pass the full, already-prefixed channel name.

**Parameters:**
- `string $fullChannelName` - The complete channel name, including any `private-` / `presence-` prefix

**Returns:** `PendingSubscription`

### `resolveTokenUsing()`

Registers the bearer-token resolver used to authorize private and presence subscriptions. Register it once, typically in
`AppServiceProvider::boot()`. It is called at subscribe time.

**Parameters:**
- `Closure $resolver` - Returns the current bearer token (e.g. from secure storage)

**Returns:** `void`

### `withToken()`

Pushes a fresh bearer token to the live connection after a re-login or refresh.

**Parameters:**
- `string $token` - The new bearer token

**Returns:** `void`

## `PendingSubscription`

The chainable object returned by the subscription methods above.

### `on()`

Runs `$callback` on each matching broadcast. The `$event` argument is a plain object of the payload; read fields with
`$event->field`.

**Parameters:**
- `string $event` - The broadcast event name (match your server's `broadcastAs()`)
- `Closure $callback` - Receives `$event`, the payload object

**Returns:** `PendingSubscription`

### `here()`

Presence only. Runs once when you join, with the full member roster.

**Parameters:**
- `Closure $callback` - Receives `array $members`; each member is `['id' => ..., 'info' => [...]]`

**Returns:** `PendingSubscription`

### `joining()`

Presence only. Runs when a member joins.

**Parameters:**
- `Closure $callback` - Receives `array $member`, shaped `['id' => ..., 'info' => [...]]`

**Returns:** `PendingSubscription`

### `leaving()`

Presence only. Runs when a member leaves.

**Parameters:**
- `Closure $callback` - Receives `array $member`, shaped `['id' => ..., 'info' => [...]]`

**Returns:** `PendingSubscription`

## Notes

- **Foreground-only:** Websockets are foreground-only on mobile. The OS suspends the socket when your app goes to the
  background. For delivery while the app is closed, use push notifications via the
  [Firebase plugin](../plugins/core/firebase).
- **Liveness, not source of truth:** Treat websocket events as a *liveness* signal, not authoritative state. On
  reconnect, refetch the canonical data from your backend.
- **Auto-teardown:** Subscriptions are removed automatically when the component unmounts. Leaving a screen leaves its
  channels and presence rooms.
- **Private/presence require a remote backend:** Signing happens on your remote Laravel app at `/broadcasting/auth`
  (behind `auth:sanctum`) with channel authorization callbacks. The device never holds the app secret.

<aside>

If a screen mutates a live list frequently (for example a chat feed appending messages on every broadcast), set
`protected bool $forceFullFrames = true;` on the component so each update renders a complete frame.

</aside>
