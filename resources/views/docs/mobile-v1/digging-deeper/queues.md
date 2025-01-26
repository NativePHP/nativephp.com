---
title: Queues
order: 500
---
# Queues

Queueing tasks to be run in the background is a critical part of creating a great user experience.

NativePHP has built-in support for Laravel's [Queues](https://laravel.com/docs/queues).

## Queueing a job
If you're familiar with queueing jobs in Laravel, you should feel right at home. There's nothing special you need to do.

Jobs live in the SQLite [database](/docs/digging-deeper/databases) that your app uses by default and the `jobs` table
migration will have been created and migrated for you.

## Processing Jobs / Working the Queue
By default, NativePHP will boot up a single queue worker which will consume jobs from the `default` queue.

If you wish to modify the configuration of this worker or run more workers, see [Configuring workers](#configuring-workers).

### Configuring workers
Once you publish the NativePHP config file using `php artisan vendor:publish`, you will find a `queue_workers` key in 
`config/nativephp.php`. Here are some acceptable values to get you started:

```php
'queue_workers' => [
    'one' => [],
    'two' => [],
    'three' => [
        'queues' => ['high'],
        'memory_limit' => 1024,
        'timeout' => 600,
    ],
    'four' => [
        'queues' => ['high'],
    ],
    'five' => [
        'memory_limit' => 1024,
    ],
],
```

Each item in the array will be spun up as a persistent [Child Process](/docs/digging-deeper/child-processes), with the key
name you provide being used as both the process's and the worker's alias.

You may configure which queues a worker is able to process jobs from, its memory limit and its timeout.

If you do not provide values for any of these settings, the following sensible defaults will be used:

```php
'queues' => ['default'],
'memory_limit' => 128,
'timeout' => 60,
```

### Managing workers

The handy `QueueWorker::up()` and `QueueWorker::down()` methods available on `Facades\QueueWorker` can be used to start
and stop workers, should you need to.

```php
use Native\DTOs\QueueConfig;
use Native\Laravel\Facades\QueueWorker;

$queueConfig = new QueueConfig(alias: 'manual', queuesToConsume: ['default'], memoryLimit: 1024, timeout: 600);

QueueWorker::up($queueConfig);

// Alternatively, if you already have the worker config in your config/nativephp.php file, you may simply use its alias:
QueueWorker::up(alias: 'manual');

// Later...
QueueWorker::down(alias: 'manual');
```

## When to Queue
Given that your database and application typically exist on the same machine (i.e. there's no network involved),
queueing background tasks can mostly be left for very intense operations and when making API calls over the network.

Even so, you may find it more user-friendly to have slow tasks complete in the main application thread. You may simply
choose to have your UI indicate that something is occurring (e.g. with a loading spinner) while the user waits for the
process to finish.

This may be clearer for the user and easier to handle in case issues arise, as you can provide visual feedback to the
user and they can try again more easily.
