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

## How It Works

When your app boots, NativePHP automatically starts a dedicated PHP runtime on a separate thread. This worker
polls `queue:work --once` in a loop, picking up and executing queued jobs as they come in.

Because it runs on its own thread with its own PHP runtime, your queued jobs are fully isolated from the main
request cycle — long-running tasks won't affect app responsiveness.

<aside>

The queue worker starts automatically. You don't need to run any artisan commands or configure a supervisor.
NativePHP manages the worker lifecycle natively on both platforms.

</aside>

### Background launches

The worker starts when your app is **on screen**. When iOS cold-launches your app in the background to run
scheduled work, that second PHP runtime is deliberately not started — it's memory a background wake didn't ask
for, and paying for it there risks the system killing the process mid-task.

Jobs you dispatch from background work are still written to the queue as normal; they're picked up the next time
the app is opened. If a background window needs work *finished* rather than queued, do it inline in the command.
See [Execution Context](execution-context) for how to tell which situation you're in.

## Things to Note

- The queue worker requires ZTS (Thread-Safe) PHP, which NativePHP includes by default.
- Only the `database` queue connection is supported. This uses the same SQLite database as your app.
- Jobs are persisted to the database, so they survive app restarts.
- If a job fails, Laravel's standard retry and failure handling applies.