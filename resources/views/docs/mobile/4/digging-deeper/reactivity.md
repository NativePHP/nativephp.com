---
title: Reactivity
order: 30
---

## Overview

SuperNative screens re-render when their state changes — you rarely rebuild the UI by hand. Two tools make state
declarative: **computed properties** for values derived from other state, and **polling** for state that needs to
refresh on a timer.

## Computed properties

Mark a method `#[Computed]` and read it as a property. The result is memoized for the frame and recomputed
whenever state changes, so you never cache derived values by hand or worry about them going stale.

```php
use Native\Mobile\Attributes\Computed;

class CartScreen extends NativeComponent
{
    public array $items = [];

    #[Computed]
    public function total(): float
    {
        return collect($this->items)->sum('price');
    }
}
```

Access it as `$this->total` (no parentheses) in PHP, or `@{{ $this->total }}` in the view:

@verbatim
```blade static
<native:text>Total: {{ $this->total }}</native:text>
```
@endverbatim

The value is cached per render and the whole computed cache is cleared on any state change (every
[model sync](data-binding) drops it), so `total` recomputes exactly when `items` changes — and never more often.

### Persisting across frames

By default a computed value is recomputed each frame. For an expensive result that only depends on inputs which
change rarely, pass `persist: true` to keep it across frames until the next state change:

```php
#[Computed(persist: true)]
public function report(): array
{
    return $this->crunchExpensiveReport(); // survives re-renders until state changes
}
```

## Polling

Refresh state on an interval with `#[Poll]`. On a method, it runs the method then re-renders; the argument is the
interval in milliseconds (default 2000):

```php
use Native\Mobile\Attributes\Poll;

#[Poll(5000)] // every 5 seconds
public function refreshFeed(): void
{
    $this->posts = Post::latest()->take(20)->get()->all();
}
```

On the **class**, `#[Poll]` just re-renders on the interval — handy when the UI reflects time (a countdown, a
"last updated" label) without any method to run:

```php
#[Poll(1000)]
class ClockScreen extends NativeComponent { /* re-renders every second */ }
```

### Polling from Blade

You can also drive a re-render from the view with `native:poll` — useful for a single element that needs to tick
without wiring a method:

@verbatim
```blade static
<native:text native:poll="1s">{{ now()->format('g:i:s A') }}</native:text>
```
@endverbatim

Accepted forms: `native:poll` (default 2s), `native:poll="500ms"` / `native:poll="1s"` (value), and
`native:poll.2s` (modifier).

<aside>

Polling drives a full re-render, so keep intervals as long as the UX allows. For push-style updates that arrive
when something actually happens rather than on a timer, listen for an [event](../the-basics/events) instead.

</aside>
