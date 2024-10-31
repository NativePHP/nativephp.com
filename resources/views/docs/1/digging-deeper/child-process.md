---
title: Child Processes
order: 700
---

# Child Processes

Child Processes allow your application to spin up managed processes, forked from your app's main process. This is great
for long-running processes that you want to interact with repeatedly during the life of your application.

Child Processes can be managed from your application using a straightforward API. When your app quits, these processes
get shut down gracefully.

"Spawning" a Child Process is like running a command from the CLI. Any command you can run in the terminal can be a
Child Process.

```php
ChildProcess::start(
    cmd: 'tail -f storage/logs/laravel.log',
    alias: 'tail'
);
```

Any process invoked using the ChildProcess facade will be non-blocking and keep running in the background. Even if the request that triggered it has finished.

**Bear in mind that your Child Process ("command line") arguments may need to differ depending on which platform your
application is running on (Mac/Linux vs Windows).**

**Where possible, you should explicitly reference binaries by their full path name, unless you can reliably assume that
the executable you're trying to spawn is available in the user's `PATH`.**

Child Processes are managed by the runtime (Electron/Tauri) but are fully accessible to the Laravel side of your
application.

---

## Alternatives

Before deciding to use a Child Process, consider the alternatives available to you. You should pick the most
appropriate for the problem you're trying to solve:

### Queues

The [queue runner](queues) is useful for very simply offloading _Laravel_ tasks to the background. Each task must be a
Laravel queued [Job](https://laravel.com/docs/queues#creating-jobs).

Any queued jobs that don't get processed before your app is quit, will get processed when your application (and the
queue runner) starts again.

### Scheduler

The Laravel scheduler runs as normal (every minute) inside a NativePHP application. You can add
[scheduled tasks](https://laravel.com/docs/scheduling) to your application just as you normally would, to have them run
on a regular schedule.

Any scheduled tasks that would have run while your application isn't running will be skipped.

**The queue runner and the scheduler are tied to your _application_, not the operating system, so they will
only be able to run while your application is running.**

### `shell_exec`, `proc_open` etc

PHP has good built-in support for running arbitrary programs in separate processes. For example:

-   [`shell_exec`](https://www.php.net/manual/en/function.shell-exec.php) allows you to run commands and return their
    output to your application.
-   [`proc_open`](https://www.php.net/manual/en/function.proc-open.php) allows you to spin up a command with more control
    over how its input and output streams are handled.

While these can be used in your NativePHP application, consider that they:

-   May block the script that is executing them until the sub-process has finished.
-   May become orphaned from your application, allowing them to continue running after your app has quit.

Runaway orphaned processes could negatively impact your user's system and can become tricky to manage without user
intervention. You should be cautious about starting processes this way.

---

## Starting a Child Process

Each Child Process must have a unique alias. This is the name you will use to reference and interact with this process
throughout your application.

You may start a process using the `ChildProcess` facade:

```php
use Native\Laravel\Facades\ChildProcess;

ChildProcess::start(
    cmd: 'tail -n50 storage/logs/laravel.log',
    alias: 'tail'
);
```

The `start` method will return a `Native\Laravel\ChildProcess` instance, which represents the process. You may interact
directly with this instance to make changes to that process, but this does not necessarily mean that the
process was started.

The timing of process initilization is controlled by the user's operating system and spawning
may fail for a number of reasons.

**To determine if the process has started successfully, you should listen for the
[`ProcessSpawned` event](#codeprocessspawnedcode).**

### Current Working Directory

By default, the child process will use the working directory of your application as it's "current working directory"
(`cwd`). However, you can explicitly change this if needed by passing a string path to the `$cwd` parameter of the
`start` method:

```php
ChildProcess::start(
    cmd: ['tail', '-n50', 'logs/laravel.log'],
    alias: 'tail',
    cwd: storage_path()
);
```

### Persistent Processes

You may mark a process as `persistent` to indicate that the runtime should make sure that once it has been started it
is always running. This works similarly to tools like [`supervisord`](http://supervisord.org/), ensuring that the
process gets booted up again in case it crashes.

```php
ChildProcess::start(
    cmd: ['tail', '-n50', 'logs/laravel.log'],
    alias: 'tail',
    persistent: true
);
```

**The only way to stop a persistent process is for your application to quit.**

### PHP scripts

For your convenience NativePHP provides a simple method to execute PHP scripts with in the background using NativePHP's packaged PHP binary:

```php
ChildProcess::php('path/to/script.php', alias: 'script');
```

### Artisan commands

NativePHP provides a similar method to run artisan commands:

```php
ChildProcess::artisan('smtp:serve', alias: 'smtp-server');
```

## Getting running processes

### Getting a single process

You can use the `ChildProcess` facade's `get` method to get a running process with a given alias:

```php
$tail = ChildProcess::get('tail');
```

This will return a `Native\Laravel\ChildProcess` instance.

### Getting all processes

You can use the `ChildProcess` facade's `all` method to get all running processes:

```php
$tail = ChildProcess::all();
```

This will return an array of `Native\Laravel\ChildProcess` instances.

## Sending input

There are multiple ways to provide input to your Child Process:

-   The environment.
-   Arguments to the command.
-   Its standard input stream (`STDIN`).
-   A custom interface, e.g. a network socket.

Which you use will depend on what the program is capable of handling.

### Environment

Child Processes will inherit the environment available to your application by default. If needed, you can provide extra
environment variables when starting the process via the `$env` parameter of the `start` method:

```php
ChildProcess::start(
    cmd: 'tail ...',
    alias: 'tail',
    env: [
        'CUSTOM_ENV_VAR' => 'custom value',
    ]
);
```

### Command line arguments

You can pass arguments to the program via the `$cmd` parameter of the `start` method. This accepts a `string` or an
`array`, whichever you prefer to use:

```php
ChildProcess::start(
    cmd: ['tail', '-n50', 'storage/logs/laravel.log'],
    alias: 'tail'
);
```

### Messaging a Child Process

You may send messages to a running child process's standard input stream (`STDIN`) using the `message` method:

```php
$tail->message('Hello, world!');
```

Alternatively, you may use the `ChildProcess` facade to message a process via its alias:

```php
ChildProcess::message('Hello, world!', 'tail');
```

The message format and how they are handled will be determined by the program you're running.

## Handling output

A Child Process may send output via any of the following interfaces:

-   Its standard output stream (`STDOUT`).
-   Its standard error stream (`STDERR`).
-   A custom interface, e.g. a network socket.

### Listening for Output (`STDOUT`)

You may receive standard output for a process by registering an event listener for the
[`MessageReceived`](#codemessagereceivedcode) event:

```php
use Illuminate\Support\Facades\Event;
use Native\Laravel\Events\ChildProcess\MessageReceived;

Event::listen(MessageReceived::class, function (MessageReceived $event) {
    if ($event->alias === 'tail') {
        info($event->data);
    }
});
```

### Listening for Errors (`STDERR`)

You may receive standard errors for a process by registering an event listener for the
[`ErrorReceived`](#codeerrorreceivedcode) event:

```php
use Illuminate\Support\Facades\Event;
use Native\Laravel\Events\ChildProcess\ErrorReceived;

Event::listen(ErrorReceived::class, function (ErrorReceived $event) {
    if ($event->alias === 'tail') {
        info($event->data);
    }
});
```

## Stopping a Child Process

Your child processes will shut down when your application exits. However, you may also choose to stop them manually or
provide this control to your user.

If you have a `Native\Laravel\ChildProcess` instance, you may call the `stop` method on it:

```php
$tail->stop();
```

Alternatively, you may use the `ChildProcess` facade to stop a process via its alias:

```php
ChildProcess::stop('tail');
```

This will attempt to stop the process gracefully. The [`ProcessExited`](#codeprocessexitedcode) event will be
dispatched if the process exits.

## Restarting a Child Process

As a convenience, you may simply restart a Child Process using the `restart` method. This may be useful in cases where
the program has become unresponsive and you simply need to "reboot" it.

If you have a `Native\Laravel\ChildProcess` instance, you may call the `restart` method on it:

```php
$tail->stop();
```

Alternatively, you may use the `ChildProcess` facade to restart a process via its alias:

```php
ChildProcess::restart('tail');
```

## Events

NativePHP provides a simple way to listen for Child Process events.

All events get dispatched as regular Laravel events, so you may use your `EventServiceProvider` to register listeners.

```php
protected $listen = [
    'Native\Laravel\Events\ChildProcess\MessageReceived' => [
        'App\Listeners\MainLoopEvent',
    ],
];
```

Sometimes you may want to listen and react to these events in real-time, which is why NativePHP also broadcasts all
Child Process events to the `nativephp` broadcast channel.

To learn more about NativePHP's broadcasting capabilities, please refer to the
[Broadcasting](/docs/digging-deeper/broadcasting) section.

### `ProcessSpawned`

This `Native\Laravel\Events\ChildProcess\ProcessSpawned` event will be dispatched when a Child Process has successfully
been spawned. The payload of the event contains the `$alias` and the `$pid` of the process.

**In Electron, the `$pid` here will be the Process ID of an Electron Helper process which spawns the underlying
process.**

### `ProcessExited`

This `Native\Laravel\Events\ChildProcess\ProcessExited` event will be dispatched when a Child Process exits. The
payload of the event contains the `$alias` of the process and its exit `$code`.

### `MessageReceived`

This `Native\Laravel\Events\ChildProcess\MessageReceived` event will be dispatched when the Child Process emits some
output via its standard output stream (`STDOUT`). The payload of the event contains the `$alias` of the process and the
message `$data`.

### `ErrorReceived`

This `Native\Laravel\Events\ChildProcess\ErrorReceived` event will be dispatched when the Child Process emits an error
via its standard error stream (`STDERR`). The payload of the event contains the `$alias` of the process and the
error `$data`.
