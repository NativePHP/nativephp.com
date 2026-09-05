---
title: Motion
order: 950
---

## Shake Detection

NativePHP detects when the user shakes their device — `motionEnded(.motionShake)` on iOS, the accelerometer on
Android — and delivers it as a [native event](../../the-basics/events), with no facade or setup required.

Listen for it with `#[On]`:

```php
use Native\Mobile\Events\Motion\ShakeDetected;

class FeedbackScreen extends NativeComponent
{
    #[On(ShakeDetected::class)]
    public function onShake(): void
    {
        $this->showFeedbackSheet = true;
    }
}
```

A shake carries no reliable magnitude on iOS, so the event's payload is minimal — just an optional `id` correlation
token, unset unless a future emitter sets one.

<aside>

You can drive this in tests without shaking a real device — `emitNative(ShakeDetected::class)` delivers it straight
to the component. See [Native Events & the Bridge](../../testing/native-events).

</aside>
