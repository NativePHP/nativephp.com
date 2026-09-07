---
title: Async Tasks
order: 240
---

## Overview

Some work is too slow to do while the user waits — building a report, resizing an image, hitting a slow API.
Run it inline in an event handler and the screen freezes: your component's runloop is busy, so nothing
re-renders until the work finishes.

Async tasks move that work onto a **separate PHP thread** and hand you the result back on the UI thread, where
you can update state and let the screen re-render:

```php
use Native\Mobile\AsyncTask;

public function generateReport(): void
{
    $this->generating = true;   // paints immediately — spinner shows

    AsyncTask::dispatch(static function () {
        return ExpensiveReport::build()->toArray();
    })->finished(function (array $report) {
        $this->report = $report;
        $this->generating = false;
    });
}
```

`dispatch()` returns straight away. Your handler finishes, the screen re-renders with the spinner, and the
task runs on its own PHP interpreter. When it completes, `finished()` fires **on the UI thread**, bound to your
live component — so `$this->report = ...` works exactly like it does in any other handler, and the screen
updates.

<aside>

Async tasks are **not** queued jobs. They start immediately, run concurrently, and never touch your database or
the queue. If you want durable, retryable background work that survives an app restart, use [Queues](queues)
instead.

</aside>

## The work closure must be static

The work runs in a **different PHP interpreter** with its own memory. It cannot see `$this`, your component's
properties, or anything else from the dispatching thread — so the closure must be declared `static`:

```php
// ✅ Static — nothing captured from the component
AsyncTask::dispatch(static fn () => Report::build());

// ❌ Throws InvalidArgumentException at dispatch time
AsyncTask::dispatch(fn () => Report::build($this->month));
```

A non-static closure throws immediately, in your handler, where you can see it — not silently in a background
log.

Pass data in by capturing plain serializable values with `use`:

```php
$month = $this->month;

AsyncTask::dispatch(static fn () => Report::build($month));
```

Everything the closure captures must be serializable. Resources, PDO handles, and open file pointers can't
cross the thread boundary and will throw.

## Handling results

### finished()

Fires with whatever the work returned:

```php
AsyncTask::dispatch(static fn () => Http::get('https://api.example.com/stats')->json())
    ->finished(function (array $stats) {
        $this->stats = $stats;
    });
```

The result travels back as JSON, so return **scalars and arrays**. For anything large — an image, a PDF, a big
export — write it to disk in the task and return the path:

```php
AsyncTask::dispatch(static function () {
    $path = storage_path('app/report-'.now()->timestamp.'.pdf');
    Report::build()->save($path);

    return $path;   // ✅ return a path, not the file contents
})->finished(fn (string $path) => $this->reportPath = $path);
```

### failed()

If the task throws, `failed()` receives an `AsyncTaskException` carrying the original message and class:

```php
AsyncTask::dispatch(static fn () => Http::get($url)->json())
    ->finished(function (array $data) {
        $this->data = $data;
        $this->loading = false;
    })
    ->failed(function (\Throwable $e) {
        $this->error = $e->getMessage();
        $this->loading = false;
    });
```

The original exception object can't cross threads, so you get a stand-in. `getMessage()` returns the original
message; `originalClass()` gives you the class name it was thrown as.

<aside>

Async tasks run **once**. There's no automatic retry — silently re-running a button press is rarely what you
want. If a task should retry, handle it in `failed()` or reach for a queued job.

</aside>

## Callbacks are scoped to the screen

By default, a `finished()` / `failed()` callback only fires if the screen that dispatched it is **still the one
on top**. If the user navigated away before the task completed, the callback is dropped.

That's deliberate: the callback is bound to a live component instance so it can mutate state, and firing it
against a screen the user has left would update something nobody is looking at — or worse, the wrong screen.

For a fire-and-forget task whose result matters regardless of where the user is — a background upload feeding a
status bar, a sync that refreshes a badge — use `shared()`:

```php
AsyncTask::dispatch(static fn () => Sync::run())
    ->shared('sync-complete');
```

`shared()` delivers the result as a **named event** instead of a scoped callback. Any active screen can pick it
up with `#[On]`:

```php
use Native\Mobile\Attributes\On;

#[On('sync-complete')]
public function syncComplete($event): void
{
    $this->lastSync = $event->result;
}
```

The event payload carries `id`, a `status` of `finished` or `failed`, and either `result` or the failure
details.

## Running several at once

Tasks run on a small pool of background PHP contexts, so several can be in flight together:

```php
public function loadDashboard(): void
{
    AsyncTask::dispatch(static fn () => Stats::revenue())
        ->finished(fn ($r) => $this->revenue = $r);

    AsyncTask::dispatch(static fn () => Stats::orders())
        ->finished(fn ($o) => $this->orders = $o);
}
```

Each callback fires as its own task completes — there's no ordering guarantee between them. When more tasks are
dispatched than the pool has slots, the extras queue up and run as slots free.

## Task classes

For anything you'd rather not write inline, extend `AsyncTask` and put the work in `handle()`:

```php
namespace App\Async;

use Native\Mobile\AsyncTask;

class BuildReport extends AsyncTask
{
    public function handle(int $month): array
    {
        return Report::forMonth($month)->toArray();
    }
}
```

Dispatch it with its arguments — they're passed to `handle()` in the background context:

```php
use App\Async\BuildReport;

BuildReport::dispatch($this->month)
    ->finished(fn (array $report) => $this->report = $report);
```

The arguments must be serializable, same as a closure's captures. This form reads like a Laravel job, but it's
still an async task — no queue, no database, no retries.

## Testing

`AsyncTask::fake()` runs tasks **inline and synchronously**, so your `finished()` and `failed()` callbacks fire
during the test with no threads involved:

```php
use Native\Mobile\AsyncTask;

it('loads the report', function () {
    AsyncTask::fake();

    Native::test(ReportScreen::class)
        ->tap('generateReport')
        ->assertSee('Revenue');
});
```

The fake also records every dispatch:

```php
$fake = AsyncTask::fake();

Native::test(ReportScreen::class)->tap('generateReport');

$fake->assertDispatched()
     ->assertDispatchedTimes(1);
```

Available assertions: `assertDispatched()` (optionally with a filter closure), `assertNotDispatched()`,
`assertDispatchedTimes()`, and `assertShared('alias')`.

## How It Works

When you dispatch a task, the work closure is serialized and handed to a background PHP context — a full PHP
interpreter on its own thread, with its own memory, booted once and reused. Your handler returns immediately and
the screen re-renders.

When the task finishes, the background context sends the result through the same native event channel that
delivers camera results and push notifications. That wakes your screen's runloop, which resolves the matching
callback, binds it to the live component, and runs it — then re-renders.

This is why the work closure is isolated but the callbacks aren't: the work runs on the background thread, and
the callbacks run on your UI thread, in your component.

## Things to Note

- The work closure must be `static` and everything it captures must be serializable.
- Results round-trip as JSON — return scalars and arrays; pass large data by file path or cache key.
- Tasks run once, with no automatic retry.
- Scoped callbacks are dropped if the user navigates away; use `shared()` when the result matters regardless.
- Async tasks don't survive the app being killed. For durable background work, use [Queues](queues).
- Device APIs that need the UI (camera, dialogs, biometrics) don't belong inside a task — the work runs off the
  UI thread with no screen attached. Fetch and compute in the task; drive UI from the callback.
