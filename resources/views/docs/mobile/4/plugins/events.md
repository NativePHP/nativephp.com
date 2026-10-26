---
title: Events
order: 500
---

## Dispatching Events from Native Code

Many native operations are asynchronous — ML inference, sensor readings, background tasks. Your native code needs a
way to send results back to PHP when they're ready. That's where events come in.

Events are dispatched from native code and received by your components.

## Declaring Events

Add your event classes to the manifest:

```json
{
    "events": [
        "Vendor\\MyPlugin\\Events\\ProcessingComplete",
        "Vendor\\MyPlugin\\Events\\ProcessingError"
    ]
}
```

## Creating Event Classes

Events are simple PHP classes that implement the `DispatchableFromNative` marker interface:

```php
namespace Vendor\MyPlugin\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Native\Mobile\Events\Contracts\DispatchableFromNative;

class ProcessingComplete implements DispatchableFromNative
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $result,
        public ?string $id = null
    ) {}
}
```

<aside>

Events don't need `ShouldBroadcast` or channel configuration. NativePHP handles the dispatch directly.

</aside>

### Implement DispatchableFromNative

Since nativephp/mobile 4.6.1 and 3.3.11, the `/_native/api/events` endpoint refuses any class that doesn't implement
`Native\Mobile\Events\Contracts\DispatchableFromNative`. When a web view is live, that endpoint is how a native
event reaches your `Event::listen` listeners and fluent callbacks. An unmarked event is dropped there without an
error. JavaScript listeners and `#[OnNative]` handlers still fire, because they hear the event in the web view, but
your PHP listeners and callbacks never run.

The interface has no methods. Add it to every event class your native code dispatches, and require
`"nativephp/mobile": "^3.3.11 || ^4.6.1"` in your `composer.json`, since older versions don't have the interface.

EDGE screens get native events through the element event queue, which doesn't check the marker. Add it anyway so
your events still reach PHP in apps that use a web view.

## Swift Event Dispatching

Dispatch events using `LaravelBridge.shared.send`:

```swift
// Build your payload
let payload: [String: Any] = [
    "result": processedData,
    "id": requestId
]

// Dispatch to PHP
LaravelBridge.shared.send?(
    "Vendor\\MyPlugin\\Events\\ProcessingComplete",
    payload
)
```

This runs synchronously on the main thread, so wrap in `DispatchQueue.main.async` if needed.

## Kotlin Event Dispatching

Dispatch events using `NativeActionCoordinator.dispatchEvent`:

```kotlin
import android.os.Handler
import android.os.Looper

// Build your payload
val payload = JSONObject().apply {
    put("result", processedData)
    put("id", requestId)
}

// Must dispatch on main thread
Handler(Looper.getMainLooper()).post {
    NativeActionCoordinator.dispatchEvent(
        activity,
        "Vendor\\MyPlugin\\Events\\ProcessingComplete",
        payload.toString()
    )
}
```

<aside>

#### Always Use the Main Thread

Event dispatching involves JavaScript injection into the web view. This must happen on the main/UI thread or it
will silently fail.

</aside>

## Official Plugins & Dev Kit

Skip the complexity — browse ready-made plugins or get the Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace →](https://nativephp.com/plugins)
