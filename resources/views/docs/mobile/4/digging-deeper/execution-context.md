---
title: Execution Context
order: 15
---

## Overview

Your app doesn't only run because someone tapped its icon. iOS will **cold-launch the whole app into the
background** to run scheduled work — a background task, a silent push, a background fetch — routinely while the
phone is locked in someone's pocket. The process boots, Laravel boots, your command runs, and the app is
suspended again, all without anything ever appearing on screen.

That's a very different world to the one your UI code assumes. There's no user to show a screen to, no
navigation to perform, and the device's protected files and keychain items may be unreadable until the phone is
unlocked.

`ExecutionContext` is how your PHP code asks where it is:

```php
use Native\Mobile\Facades\ExecutionContext;

if (ExecutionContext::isHeadless()) {
    // Woken for background work. Do the job; don't touch the UI.
}
```

## What NativePHP does for you

You don't have to defend against a background launch yourself — NativePHP splits its own boot in two and gates
the second half:

- The **runtime** always boots. PHP starts, your app is extracted, migrations run, the persistent Laravel
  runtime comes up, and plugin callbacks fire. That's what your scheduled work needs to run at all.
- The **interactive boot** is held back. Your [start URL](../getting-started/configuration#start-url) is not
  dispatched, no screen is mounted, and no web view is created until the app is genuinely on screen.

So a background wake never runs your `/` route, never fires a component's `mount()`, and never triggers the
authentication middleware, polling or navigation that would normally follow. When the user later opens the app,
the interactive boot happens then — exactly once.

<aside>

The [queue worker](queues) is held back the same way. It runs a second PHP runtime, and paying for that memory
during a background wake is a good way to get the process killed mid-task. Jobs you dispatch from background
work are still written to the queue — they're picked up when the app is next opened.

</aside>

## Checking the context

Every read goes straight to the platform, so the value is always current — even inside a long-lived native
screen whose request has been open for minutes.

### Is anyone looking?

```php
use Native\Mobile\Facades\ExecutionContext;

ExecutionContext::isHeadless();   // background launch that has never been on screen
ExecutionContext::isForeground(); // on screen (active or momentarily inactive)
ExecutionContext::isBackground(); // not on screen
ExecutionContext::isActive();     // frontmost and receiving events
```

`isHeadless()` is the one you usually want. It's true only for the case that actually matters: the system
started this process for background work and the user has never seen it.

The difference between `isActive()` and `isForeground()` is worth knowing. An app that's on screen but not
receiving events — mid app-switcher, behind an incoming call banner, under a system permission alert — is
*inactive* but still *foreground*. Don't treat that as "the user has gone away".

### Is the device unlocked?

```php
if (! ExecutionContext::isProtectedDataAvailable()) {
    // Encrypted files and keychain items can't be read right now.
    return;
}
```

On iOS this is false whenever the device is locked with Data Protection engaged; on Android it's false before
the user's first unlock after a reboot. Either way it means the same thing: anything in
[secure storage](../plugins/core/secure-storage), and any file protected by the OS, is unreadable until the
device is unlocked.

This matters most in background work, which is precisely when the phone is most likely to be locked. Reading a
token you can't decrypt will fail, so check first and let the system reschedule you.

<aside>

`APP_KEY` is read from the device keychain, so `Crypt::decryptString()` on data you encrypted earlier is
subject to the same restriction. See [Security](security) for how NativePHP manages the key.

</aside>

## Background work

<aside>

Scheduling the work in the first place is provided by the
[Background Tasks plugin](../plugins/core/background-tasks). To run Artisan commands on a schedule while your app
is closed, install the `nativephp/mobile-background-tasks` Composer package.

</aside>

```shell
composer require nativephp/mobile-background-tasks
php artisan native:plugin:register nativephp/mobile-background-tasks
```

Then rebuild your app so the plugin's native code is compiled in — see
[Using Plugins](../plugins/using-plugins) for the full flow.

Once a task is registered, the OS decides when to run it — and it will pick moments when your app is closed and
the phone is locked. Write the command so that's the normal case rather than the exception:

```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Native\Mobile\Facades\ExecutionContext;

class SyncInbox extends Command
{
    protected $signature = 'app:sync-inbox';

    public function handle(): int
    {
        if (! ExecutionContext::isProtectedDataAvailable()) {
            $this->info('Device locked; will retry on the next window.');

            return self::SUCCESS;
        }

        $messages = $this->pullMessages();

        if (ExecutionContext::isHeadless()) {
            // No UI to update — just persist and let the app read it on open.
            $this->store($messages);

            return self::SUCCESS;
        }

        // Running while the user is watching, so it's safe to notify them.
        $this->store($messages);
        $this->refreshVisibleScreen();

        return self::SUCCESS;
    }
}
```

A background window is short and can be cut off at any time. Do the work inline and persist as you go, rather
than dispatching a job and assuming something will pick it up — during a headless launch, nothing will until
the app is opened.

## Methods

All methods are called on the `Native\Mobile\Facades\ExecutionContext` facade.

### `isHeadless()`

Whether the system cold-launched this process for background work and it has never been on screen. The check to
reach for when you want to know "should I skip anything user-facing?".

**Returns:** `bool`

### `isForeground()`

Whether the app is on screen. Covers both `active` and the transient `inactive` state.

**Returns:** `bool`

### `isBackground()`

Whether the app is not on screen. The inverse of `isForeground()`.

**Returns:** `bool`

### `isActive()`

Whether the app is frontmost and receiving events.

**Returns:** `bool`

### `isProtectedDataAvailable()`

Whether OS-protected files and keychain items can be read. False on iOS while the device is locked, and on
Android before the user's first unlock after a reboot.

**Returns:** `bool`

### `launchedInBackground()`

Whether the process was started by the system for background work, regardless of what has happened since. Stays
true even after the user opens the app — unlike `isHeadless()`, which becomes false at that point.

**Returns:** `bool`

### `hasBecomeActive()`

Whether the app has been on screen at least once since the process started.

**Returns:** `bool`

### `interactiveBootStarted()`

Whether NativePHP has run its interactive boot — the point at which the start route was dispatched and the
first screen created. False for the whole of a headless launch.

**Returns:** `bool`

### `state()`

The raw lifecycle state: `'active'`, `'inactive'` or `'background'`.

**Returns:** `string`

### `launch()`

Why the process started: `'foreground'` (someone opened the app) or `'background'` (the system woke it).

**Returns:** `string`

### `all()`

The whole context in one array, if you'd rather read it as data — useful for logging what a background run
actually saw.

**Returns:** `array`

## Platform differences

Background cold launches are an **iOS** behaviour. Android starts your app's runtime from its Activity, so
there's no equivalent headless launch: `isHeadless()` is always false there and `launch()` always reports
`'foreground'`.

`isProtectedDataAvailable()` is meaningful on both, mapping to device lock state on iOS and to the direct-boot
user-unlocked state on Android.

Writing the checks anyway costs nothing and keeps one code path across both platforms.

## Testing

There's no device involved — script the context like any other native call:

```php
use Native\Mobile\Testing\Native;

Native::fakeBridge()->respondTo('System.GetExecutionContext', [
    'headless' => true,
    'protected_data_available' => false,
]);
```

Anything you leave out falls back to an ordinary interactive app, so you only state the part you're testing.
See [Native Events](../testing/native-events) for more on scripting bridge responses.

## Notes

- **Reads are never cached.** A native screen's request stays open for as long as the screen is on the stack, so
  a value captured once would go stale. Call the facade at the moment you need the answer.
- **Old native shells report an interactive app.** If your app's native project predates this API, every read
  falls back to the foreground defaults. Rebuild with `php artisan native:run` to get real answers.
- **Don't use it to hide UI.** `ExecutionContext` tells you whether UI is *possible*, not what to render. During
  a headless launch there is no screen to hide.
