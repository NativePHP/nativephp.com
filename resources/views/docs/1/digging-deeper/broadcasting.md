---
title: Broadcasting
order: 100
---

# Broadcasting

NativePHP facilitates event broadcasting of both [native events](#native-events) (emitted by Electron/Tauri) and
[custom events](#custom-events) dispatched by your Laravel app. You can listen to all of these events in your
Laravel application as you normally would or in the [JavaSscript](#javascript) on your pages.

## Native events

NativePHP fires various events during its operations, such as `WindowBlurred` & `NotificationClicked`. A full list
of all events fired and broadcast by NativePHP can be found in the
[`src/Events`](https://github.com/nativephp/laravel/tree/main/src/Events) folder.

## Custom events

You can also broadcast your own events. Simply implement the `ShouldBroadcastNow` contract in your event class and
define the `broadcastOn` method, returning `nativephp` as one of the channels it broadcasts to:

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

This is particularly useful for scenarios where you want to offload an intensive task to a background queue and await
its completion without constantly polling your application for its status.

## Listening with IPC in Electron

You can listen to all PHP events in real-time using JavaScript by making use of Electron's
[Inter-Process-Communication System](https://electronjs.org/docs/latest/api/ipc-renderer).

```js
const ipcRenderer = require("electron").ipcRenderer;

ipcRenderer.on("native-event", (_, data) => {
    const eventClassName = data.event;
    const eventPayload = data.payload;
});
```

## Listening with Livewire

To make this process even easier when using [Livewire](https://livewire.laravel.com), you may use the `native:` prefix when
listening to events. This is similar to
[listening to Laravel Echo events using Livewire](https://livewire.laravel.com/docs/events#real-time-events-using-laravel-echo).

```php
class AppSettings extends Component
{
    public $windowFocused = true;

    #[On('native:\Native\Laravel\Events\Windows\WindowFocused')]
    public function windowFocused()
    {
        $this->windowFocused = true;
    }

    #[On('native:\Native\Laravel\Events\Windows\WindowBlurred')]
    public function windowBlurred()
    {
        $this->windowFocused = false;
    }
}
```
