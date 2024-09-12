---
title: Broadcasting
order: 100
---

# Broadcasting

NativePHP facilitates event broadcasting of both [custom events](#custom-events) and [native events](#native-events). You can listen to serverside events using either [Electrons' IPC](#javascript) for updating the client when an event is dispatched from the back-end, or use Laravel Echo and [Websockets](#websockets) for more demanding scenarios. The IPC approach should cover most use cases.

## Native events

NativePHP fires various events during its operations, such as `WindowBlurred` & `NotificationClicked`. A full list of all events fired and broadcast by NativePHP can be found in the
[`src/Events`](https://github.com/nativephp/laravel/tree/main/src/Events) folder.

## Custom events

You can also broadcast your own events. Simply implement the `ShouldBroadcastNow` contract in your event class and define the `broadcastOn` method, returning nativephp as one of the channels it broadcasts to:

```php
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class JobFinished implements ShouldBroadcastNow
{
    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
```

This is particularly useful for scenarios where you want to offload an intensive task to a background queue and await its completion without constantly polling your application for its status.

## Listening with IPC

You can listen to all PHP events in real-time using JavaScript by making use of Electron's [Inter-Process-Communication System](https://electronjs.org/docs/latest/api/ipc-renderer).

This is especially useful as it reduces the overhead and latency of dispatching and listening Laravel events via a WebSocket connection and should cover most use-cases where you need your client to react to a serverside event.

```js
const ipcRenderer = require("electron").ipcRenderer;

ipcRenderer.on("native-event", (_, data) => {
    const eventClassName = data.event;
    const eventPayload = data.payload;
});
```

## Listening with Livewire

To make this process even easier when using [Livewire](https://livewire.laravel.com), you may use the `native:` prefix when listening to events. This is similar to [listening to Laravel Echo events using Livewire](https://livewire.laravel.com/docs/events#real-time-events-using-laravel-echo).

```php
class AppSettings extends Component
{
    public $windowFocused = true;

    #[On('native:\Native\Laravel\Events\Windows\WindowFocused::class')]
    public function windowFocused()
    {
        $this->windowFocused = true;
    }

    #[On('native:\Native\Laravel\Events\Windows\WindowBlurred::class')]
    public function windowBlurred()
    {
        $this->windowFocused = false;
    }
}
```

Alternatively you may also use the `$listeners` property to define all your component's listeners in one place:

```php
// Special Syntax: ['native:{event}' => '{method}']
protected $listeners = [
    'native:'.\Native\Laravel\Events\Windows\WindowFocused::class => 'windowFocused',
    'native:'.\Native\Laravel\Events\Windows\WindowBlurred::class => 'windowBlurred',
];
```

## Listening with Websockets

NativePHP broadcasts events over a websocket connection on the `nativephp` broadcast channel.
This allows you to listen for these events in real-time using Laravel Echo.

To use WebSockets in your NativePHP application, install the [Laravel WebSockets](https://beyondco.de/docs/laravel-websockets) package in your Laravel app.

NativePHP automatically runs `php artisan websockets:serve` to start your WebSocket server when your application runs, enabling you to use Laravel Echo to connect and listen for events in real-time.

For more information on setting up and using Laravel Echo, refer to the [Laravel documentation](https://laravel.com/docs/11.x/broadcasting#client-side-installation).

> Note: The Laravel Websockets package is currently only available for Laravel 10 and lower.
> Laravel Reverb support is coming soon!
