---
title: Broadcasting
order: 100
---

# Broadcasting

NativePHP fires various events during its operations. You may listen for these events using any event listener in your
application as you normally would.

## Reacting to NativePHP events in real-time

In order to react to NativePHP events as they happen, such as focusing a Window, NativePHP broadcasts these events in two different ways:

### WebSockets
NativePHP broadcasts events over a websocket connection on the `nativephp` broadcast channel.
This allows you to listen for these events in real-time using Laravel Echo and react to these events on your application's
front-end or via Laravel Livewire.

In order to make use of WebSockets in your NativePHP application, you can install the [Laravel WebSockets](https://beyondco.de/docs/laravel-websockets) package in your Laravel app.
NativePHP automatically runs `php artisan websockets:serve` to start your WebSocket server once your application runs, allowing you to use Laravel Echo to connect to it and listen for events in real-time.

### Browser Events

In addition to connecting to a local WebSocket server using Laravel Echo, you can also listen to all internal PHP events in real-time using JavaScript by making use of Electron's [Inter-Process-Communication System](https://electronjs.org/docs/latest/api/ipc-renderer). 
This is especially useful as it reduces the overhead and latency of dispatching and listening Laravel events via a WebSocket connection.

### JavaScript

You can listen to these events in real-time by making use of Electron's `ipcRenderer` API.
This API dispatches a `native-event` event, that contains the following payload:

* `event` - A string containing the fully qualified class name of the NativePHP event class that got dispatched
* `payload` - An object that contains the event payload

```js
import * as remote from '@electron/remote'

ipcRenderer.on('native-event', (_, data) => {
    const eventClassName = data.event;
    const eventPayload = data.payload;
});
```

### Livewire

To make this process even easier when using [Livewire](https://laravel-livewire.com), you may use the `native:` prefix when listening to events.
This is similar to [listening to Laravel Echo events using Livewire](https://laravel-livewire.com/docs/2.x/laravel-echo).

```php
class AppSettings extends Component
{
    public $windowFocused = true;
    
    // Special Syntax: ['native:{event}' => '{method}']
    protected $listeners = [
        'native:'.\Native\Laravel\Events\Windows\WindowFocused::class => 'windowFocused',
        'native:'.\Native\Laravel\Events\Windows\WindowBlurred::class => 'windowBlurred',
    ];
    
    public function windowFocused()
    {
        $this->windowFocused = true;
    }
    
    public function windowBlurred()
    {
        $this->windowFocused = false;
    }
}
```

## Available Events

A full list of all events fired and broadcast by NativePHP can be found in the
[src/Events/](https://github.com/nativephp/laravel/tree/main/src/Events) folder.

## Broadcasting custom events

You can also broadcast your own custom events. Simply instruct your event to implement the `ShouldBroadcastNow` contract
and define the `broadcastsOn` method in your event, returning `nativephp` as one of the channels it broadcasts to:

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

This is great for times when you want to offload an intensive task to a background queue and await its completion
without constantly polling your application for its status.

Your fired event will be broadcast and your application can listen for it and just as your normally would.
