---
title: Queues
order: 250
---

## Background Queue Worker

NativePHP runs a background queue worker alongside your app's main thread. Queued jobs execute off the main thread,
so they won't block your UI or slow down user interactions.

Both iOS and Android are supported.

## Setup

Set your queue connection to `database` in your `.env` file:

```dotenv
QUEUE_CONNECTION=database
```

That's it. NativePHP handles the rest — the worker starts automatically when your app boots.

## Usage

Use Laravel's standard queue dispatching. Everything works exactly as you'd expect:

```php
use App\Jobs\SyncData;

SyncData::dispatch($payload);
```

Or using the `dispatch()` helper:

```php
dispatch(new App\Jobs\ProcessUpload($file));
```

### Example Job

Here's a simple job that makes an API call in the background:

```php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use NativePHP\Plugins\Dialog\Dialog;

class SyncData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload) {}

    public function handle()
    {
        Http::post('https://api.example.com/sync', $this->payload);

        Dialog::toast('Sync complete!');
    }
}
```

## Sending Events to a Screen

<x-docs.version-badge since="TODO" />

A job runs in its own PHP runtime, so it cannot change a screen directly. Fire an event that implements
`BroadcastsGlobally` instead, and the live screen hears it in its `#[On]` methods:

```php
use App\Events\SyncFinished;

public function handle()
{
    Http::post('https://api.example.com/sync', $this->payload);

    event(new SyncFinished(count: count($this->payload)));
}
```

The event only reaches a screen while a native screen is showing. Its `Event::listen` listeners then run in the
runtime of the screens, and not in the job. Otherwise the event stays in the job, as any Laravel event does.

This does not work under [Jump](../the-basics/jump): a job that `php artisan queue:work` runs on your machine does
not reach the screen.

See [Events from queued jobs and async tasks](../the-basics/events#events-from-queued-jobs-and-async-tasks) for how
to write the event and the screen's method, and for the limits.

## How It Works

When your app boots, NativePHP automatically starts a dedicated PHP runtime on a separate thread. This worker
polls `queue:work --once` in a loop, picking up and executing queued jobs as they come in.

Because it runs on its own thread with its own PHP runtime, your queued jobs are fully isolated from the main
request cycle — long-running tasks won't affect app responsiveness.

<aside>

The queue worker starts automatically. You don't need to run any artisan commands or configure a supervisor.
NativePHP manages the worker lifecycle natively on both platforms.

</aside>

## Things to Note

- The queue worker requires ZTS (Thread-Safe) PHP, which NativePHP includes by default.
- Only the `database` queue connection is supported. This uses the same SQLite database as your app.
- Jobs are persisted to the database, so they survive app restarts.
- If a job fails, Laravel's standard retry and failure handling applies.