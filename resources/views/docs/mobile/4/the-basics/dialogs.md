---
title: Dialogs
order: 160
---

## Overview

The `Dialog` API shows native alert dialogs and toast/snackbar notifications — a real `UIAlertController` on
iOS and `AlertDialog` on Android, so they look and feel exactly like the platform.

It's a **core built-in**: the facade resolves with nothing to install or register.

```php
use Native\Mobile\Facades\Dialog;
```

## Alerts

Show an alert with a title, message, and optional buttons:

```php
// Simple alert with a default OK button
Dialog::alert('Hello', 'Welcome to the app!');

// Custom buttons
Dialog::alert('Confirm', 'Are you sure?', ['Cancel', 'Delete']);
```

`alert()` returns a `PendingAlert` you can configure fluently. If you don't call `->show()`, the alert displays
automatically when the object goes out of scope.

| Method | Description |
| --- | --- |
| `->id(string $id)` | Tag the alert so you can tell which one a button press came from |
| `->event(string $class)` | Dispatch a custom event class instead of the default `ButtonPressed` |
| `->remember()` | Flash the alert's `id` to the session so you can read it back later with `PendingAlert::lastId()` |
| `->show()` | Display the alert explicitly (otherwise it shows on destruct) |
| `->buttonPressed(Closure $cb)` | Run a callback when a button is tapped (see below) |
| `->on(string $class, Closure $cb)` | Callback for a custom event set via `->event()` |

### Handling button presses

Chain a callback directly onto the alert. It runs on your live component, so `$this` works just like a method:

```php
Dialog::alert('Confirm', 'Are you sure?', ['Cancel', 'Delete'])
    ->buttonPressed(function ($event) {
        if ($event->label === 'Delete') {
            $this->deleteItem();
        }
    });
```

Or listen from the component with `#[On]`. The event's public properties bind to your method parameters by name:

```php
use Native\Mobile\Attributes\On;
use Native\Mobile\Events\Alert\ButtonPressed;

#[On(ButtonPressed::class)]
public function onButton(int $index, string $label, ?string $id = null): void
{
    if ($id === 'delete-confirm' && $label === 'Delete') {
        $this->deleteItem();
    }
}
```

The `ButtonPressed` event carries:

| Property | Type | Description |
| --- | --- | --- |
| `index` | int | The tapped button's index (0-based) |
| `label` | string | The button's label text |
| `id` | ?string | The alert's `id`, if one was set |

If you didn't set an explicit `id`, call `->remember()` to flash the auto-generated one to the session, then
read it back in your listener with `PendingAlert::lastId()`:

```php
Dialog::alert('Confirm', 'Delete this item?', ['Cancel', 'Delete'])->remember();

#[On(ButtonPressed::class)]
public function onButton(string $label, ?string $id = null): void
{
    if ($id === \Native\Mobile\PendingAlert::lastId() && $label === 'Delete') {
        $this->deleteItem();
    }
}
```

## Toasts

A brief, non-blocking message — a `Snackbar` on Android, an overlay on iOS:

```php
Dialog::toast('Item saved!');            // 'long' (~4s) by default
Dialog::toast('Copied', 'short');        // 'short' (~2s)
```

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `message` | string | required | The text to display |
| `duration` | string | `'long'` | `'short'` (~2s) or `'long'` (~4s) |

<aside>

These functions are also callable from JavaScript in a web view via the `Native` library — see
[Native Functions](../the-basics/native-functions).

</aside>
