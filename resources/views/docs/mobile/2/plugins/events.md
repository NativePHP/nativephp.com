---
title: Events
order: 500
---

## Dispatching Events from Native Code

Many native operations are asynchronous — ML inference, sensor readings, background tasks. Your native code needs a
way to send results back to PHP when they're ready. That's where events come in.

Events are dispatched from native code and received by your Livewire components, just like the built-in APIs.

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

Events are simple PHP classes:

```php
namespace Vendor\MyPlugin\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessingComplete
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

## Listening in Livewire

Use the `#[OnNative]` attribute to handle plugin events:

```php
use Native\Mobile\Attributes\OnNative;
use Vendor\MyPlugin\Events\ProcessingComplete;

#[OnNative(ProcessingComplete::class)]
public function handleComplete(string $result, ?string $id = null)
{
    $this->processedResult = $result;
}
```

The attribute wires up the JavaScript event listener automatically.
