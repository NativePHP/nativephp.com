---
title: Broadcasting
order: 100
---

# Broadcasting

NativePHP fires various events during its operations. You may listen for these events using any event listener in your
application as you normally would.

NativePHP also broadcasts these events over a websocket connection to the `nativephp` broadcast channel. This allows
you to listen for these events in real-time using Laravel Echo and react to these events on your application's
front-end.

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
