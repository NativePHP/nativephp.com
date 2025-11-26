---
title: Events
order: 200
---

## Overview

Many native mobile operations take time to complete and await user interaction. PHP isn't really set up to handle this
sort of asynchronous behaviour; it is built to do its work, send a response and move on as quickly as possible.

NativePHP for Mobile smooths over this disparity between the different paradigms using a simple event system that
handles completion of asynchronous methods using a webhook-/websocket-style approach to notify your Laravel app.

## Understanding Async vs Sync

Not all actions are async. Some methods run immediately, and in some cases return a result straight away.

Here are a few of the **synchronous** APIs:

```php
Haptics::vibrate();
System::flashlight();
Dialog::toast('Hello!');
```
Asynchronous actions trigger operations that may complete later. These return immediately, usually with a `bool` or
`void`, allowing PHP's execution to finish. In many of these cases, the user interacts directly with a native component.
When the user has completed their task and the native UI is dismissed, the native app

```php
// These trigger operations and fire events when complete
Camera::getPhoto(); // → PhotoTaken event
Biometrics::promptForBiometricID(); // → Completed event
PushNotifications::enrollForPushNotifications(); // → TokenGenerated event
```

## Basic Event Structure

All events are standard [Laravel Event classes](https://laravel.com/docs/12.x/events#defining-events). The public
properties of the events contain the pertinent data coming from the native app side.

## Event Handling

All asynchronous methods follow the same pattern:

1. **Call the method** to trigger the operation.
2. **Listen for the appropriate events** to handle the result.
3. **Update your UI** based on the outcome.

All events get sent directly to JavaScript in the web view _and_ to your PHP application via a special route. This
allows you to listen for these events in the context that best suits your application.

### On the frontend

Events are 'broadcast' to the frontend of your application via the web view through a custom `Native` helper. You can
easily listen for these events in JavaScript in two ways:

- The `Native.on()` helper
- Livewire's `#[On()]` attribute

#### The `Native.on()` helper

Register the event listener directly in JavaScript:

```blade
@@use(Native\Mobile\Events\Alert\ButtonPressed)

<script>
    Native.on(@@js(ButtonPressed::class), (index, label) => {
        alert(`You pressed button ${index}: ${label}`)
    })
</script>
```

#### Livewire's `#[On()]` attribute

Livewire makes listening to 'broadcast' events simple. Just add the event name, prefixed by `native:` to the `#[On()]`
attribute attached to the method you want to use as its handler:

```php
use Native\Mobile\Events\Camera\PhotoTaken;

#[OnNative(PhotoTaken::class)]
public function handlePhoto(string $path)
{
    // Handle captured photo
}
```

### On the backend

You can also listen for these events on the PHP side as they are simultaneously passed to your Laravel application.

Simply [add a listener](https://laravel.com/docs/12.x/events#registering-events-and-listeners) as you normally would:

```php
use App\Services\APIService;
use Native\Mobile\Events\Camera\PhotoTaken;

class UpdateAvatar
{
    public function __construct(private APIService $api) {}
    
    public function handle(PhotoTaken $event): void
    {
        $imageData = base64_encode(
            file_get_contents($event->path)
        );
        
        $this->api->updateAvatar($imageData);
    }
}
```
