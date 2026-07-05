---
title: Lifecycle Hooks
order: 20
---

## Overview

Every SuperNative screen is a component class with a lifecycle. As the user navigates into a screen, away from
it, and back again, the framework calls a set of hooks you can override to load data, refresh state, clean up
resources, and react to input. If you know Livewire, these will feel familiar — but the triggers map to the
native navigation stack rather than HTTP requests.

A screen is **pushed** onto the stack the first time you navigate to it, stays **alive** while other screens sit
on top, **resumes** when those screens pop away, and is **unmounted** when it finally leaves the stack.

## mount()

Runs **once**, right after the screen is pushed onto the stack. This is where you load the screen's initial data
and read any [route params or navigation data](../the-basics/navigation#reading-params-and-data).

```php
class ProductScreen extends NativeComponent
{
    public array $product = [];

    public function mount(): void
    {
        $this->product = Product::findOrFail($this->param('id'))->toArray();
    }
}
```

`mount()` does **not** run again when the user navigates back to an already-open screen — that's what
`onResume()` is for.

## onResume()

Runs when the user returns to a screen that was **already on the stack**, after a screen above it pops away
(via back navigation). The screen's state is intact — nothing was rebuilt — so use `onResume()` to refresh
anything that may have changed while you were away.

```php
public function onResume(): void
{
    // A child screen may have edited this record — re-pull it.
    $this->product = Product::find($this->product['id'])->toArray();
}
```

The distinction matters: a fresh push fires `mount()`; a return fires `onResume()`. A screen visited for the
first time never fires `onResume()`.

## onBackPressed()

Runs when the user presses the device/hardware back button (or the system back gesture). The default
implementation navigates back:

```php
public function onBackPressed(): void
{
    $this->back();
}
```

Override it to intercept — for example, to confirm before discarding an unsaved draft:

```php
public function onBackPressed(): void
{
    if ($this->hasUnsavedChanges) {
        $this->confirmingExit = true;   // show a confirm sheet instead of leaving
        return;
    }

    $this->back();
}
```

If you override it and want to actually leave, call `$this->back()` yourself.

## unmount()

Runs when the screen **leaves the stack** for good (popped, or replaced). Use it — or a registered cleanup
callback — to release resources so they don't leak onto the next screen: unsubscribe from channels, cancel
timers, detach listeners.

```php
public function unmount(): void
{
    $this->channel?->leave();

    parent::unmount(); // keep the framework's own listener teardown
}
```

Most cleanup registered by plugins (e.g. Vibe leaving a channel) is wired automatically; you only override
`unmount()` for resources you opened yourself.

## updated{Property}()

Runs whenever a [model-bound](../the-basics/native-ui) property changes from the UI — the native equivalent of
Livewire's updated hooks. The hook name is `updated` + the studly-cased property name, and it receives the new
value.

@verbatim
```blade
<native:text-input native:model="query" />
```
@endverbatim

```php
public string $query = '';

public function updatedQuery(string $value): void
{
    $this->results = $this->search($value);
}
```

## Instant screens with #[Lazy]

When `mount()` does slow work (a network call, a heavy query), mark the component `#[Lazy]` to paint a
placeholder **immediately** while `mount()` runs in the background, so navigation feels instant:

```php
use Native\Mobile\Attributes\Lazy;

#[Lazy]
class Dashboard extends NativeComponent
{
    public function mount(): void
    {
        $this->stats = $this->crunchExpensiveStats(); // slow
    }

    protected function placeholder(): Element|View
    {
        return view('native.dashboard-skeleton');
    }
}
```

Override `placeholder()` to customize the loading frame; the default is a centered activity indicator wrapped in
the screen's layout chrome.

## Related

Beyond the lifecycle, screens react to the outside world through a few attributes:

- `#[Computed]` — derived, memoized properties that recompute when state changes.
- `#[On(Event::class)]` / `#[OnNative(...)]` — listen for app or native bridge events. See
  [Native Events](../testing/native-events).
- `#[Poll]` — run a method on a repeating interval.
- `onSearchQuery(string $query)` — feed a native search bar's input.

<aside>

Every hook here runs in-process with no device required, so you can drive the whole lifecycle — push, resume,
back, property updates — in a [test](../testing/interactions) without a simulator.

</aside>
