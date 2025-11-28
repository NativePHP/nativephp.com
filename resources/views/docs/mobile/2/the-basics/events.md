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

When the user has completed their task and the native UI is dismissed, the app will emit an event that represents the
outcome.

The _type_ (the class name) of the event and its properties all help you to choose the appropriate action to take in
response to the outcome.

```php
// These trigger operations and fire events when complete
Camera::getPhoto(); // → PhotoTaken event
Biometrics::prompt(); // → Completed event
PushNotifications::enroll(); // → TokenGenerated event
```

## Basic Event Structure

All events are standard [Laravel Event classes](https://laravel.com/docs/12.x/events#defining-events). The public
properties of the events contain the pertinent data coming from the native app side.

## Custom Events

Almost every function that emits events can be customized to emit events that you define. This is a great way to ensure
only the relevant listeners are executed when these events are fired.

Events are simple PHP classes that receive some parameters. You can extend existing events for convenience.

Let's see a complete example...

### Define your custom event class

```php
namespace App\Events;

use Native\Mobile\Events\Alert\ButtonPressed;

class MyButtonPressedEvent extends ButtonPressed
{}
```

### Pass this class to an async function

```php
use App\Events\MyButtonPressedEvent;

Dialog::alert('Warning!', 'You are about to delete everything! Are you sure?', [
        'Cancel',
        'Do it!'
    ])
    ->event(MyButtonPressedEvent::class)
```

### Handle the event

Here's an example handling a custom event class inside a Livewire component.

```php
use App\Events\MyButtonPressed;
use Native\Mobile\Attributes\OnNative;

#[OnNative(MyButtonPressed::class)]
public function buttonPressed()
{
    // Do stuff
}
```

## Event Handling

All asynchronous methods follow the same pattern:

1. **Call the method** to trigger the operation.
2. **Listen for the appropriate events** to handle the result.
3. **Update your UI** based on the outcome.

All events get sent directly to JavaScript in the web view _and_ to your PHP application via a special route. This
allows you to listen for these events in the context that best suits your application.

### On the frontend

Events are 'broadcast' to the frontend of your application via the web view through a custom `Native` helper. You can
easily listen for these events through JavaScript in a few ways:

- The globally available `Native.on()` helper
- Directly importing the `on` function
- The `#[OnNative()]` PHP attribute Livewire extension

<aside>

Typically, you shouldn't need to use more than one of these approaches. Which one you adopt will depend on which
frontend stack you're using to build your app.

</aside>

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

This approach is useful if you're not using any particular frontend JavaScript framework.

#### The `on` import

<aside>

Make sure you've [set up the `Native` plugin](native-functions#install-the-plugin) in your `package.json` first.

</aside>

If you're using a SPA framework like Vue or React, it's more convenient to import the `on` function directly to
register your event listeners. Here's an example using the amazing Vue:

```js
import { on, Events } from '#nativephp';
import { onMounted } from 'vue';

const handleButtonPressed = (payload: any) => {};

onMounted(() => {
    on(Events.Alert.ButtonPressed, handleButtonPressed);
});
```

Note how we're also using the `Events` object above to simplify our use of built-in event names. For custom event
classes, you will need to reference these by their full name:

```js
on('App\\Events\\MyButtonPressedEvent', handleButtonPressed);
```

In SPA land, don't forget to de-register your event handlers using the `off` function too:

```js
import { off, Events } from '#nativephp';
import { onUnmounted } from 'vue';

onUnmounted(() => {
    off(Events.Alert.ButtonPressed, handleButtonPressed);
});
```

#### The `#[OnNative()]` attribute

Livewire makes listening to 'broadcast' events simple. Just add the `#[OnNative()]` attribute attached to the Livewire
component method you want to use as its handler:

```php
use Native\Mobile\Attributes\OnNative;
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
