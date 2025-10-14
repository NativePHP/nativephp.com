---
title: Broadcasting
order: 100
---

# Broadcasting

NativePHP facilitates event broadcasting of both [native events](#native-events) (emitted by Electron) and
[custom events](#custom-events) dispatched by your Laravel app. You can listen to all of these events in your
Laravel application as you normally would or in the [JavaScript](#listening-with-javascript) on your pages.

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

## Listening with JavaScript

You can listen to all native and custom events emitted by your application in real-time using JavaScript.

NativePHP injects a `window.Native` object into every window. The `on()` method allows you to register a callback as
the second parameter that will run when the event specified in the first parameter is fired:

```js
Native.on("Native\\Laravel\\Events\\Windows\\WindowBlurred", (payload, event) => {
    //
});
```

## Listening with Livewire

To make this process even easier when using [Livewire](https://livewire.laravel.com), you may use the `native:` prefix when
listening to events. This is similar to
[listening to Laravel Echo events using Livewire](https://livewire.laravel.com/docs/events#real-time-events-using-laravel-echo).

You may use a string name:

```php
class AppSettings extends Component
{
    public $windowFocused = true;

    #[On('native:\\Native\\Laravel\\Events\\Windows\\WindowFocused')]
    public function windowFocused()
    {
        $this->windowFocused = true;
    }

    #[On('native:\\Native\\Laravel\\Events\\Windows\\WindowBlurred')]
    public function windowBlurred()
    {
        $this->windowFocused = false;
    }
}
```

You may find it more convenient to use PHP's class name resolution keyword, `::class`:

```php
use Native\Laravel\Events\Windows\WindowBlurred;
use Native\Laravel\Events\Windows\WindowFocused;

class AppSettings extends Component
{
    public $windowFocused = true;

    #[On('native:'.WindowFocused::class)]
    public function windowFocused()
    {
        $this->windowFocused = true;
    }

    #[On('native:'.WindowBlurred::class)]
    public function windowBlurred()
    {
        $this->windowFocused = false;
    }
}
```
