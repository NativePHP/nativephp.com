---
title: Notifications
order: 500
---

## Native Notifications

NativePHP allows you to send system notifications using an elegant PHP API. These notifications are, unlike Laravel's built-in notifications, actual UI notifications displayed by your operating system.

When used sparingly, notifications can be a great way to inform the user about events that are occurring in your application and to bring their attention back to it, especially if further input from them is required.

Notifications are sent using the `Notification` facade.
```php
use Native\Laravel\Facades\Notification;
```

### Sending Notifications

You may send a notification using the `Notification` facade.

```php
Notification::title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->show();
```

This will show a system-wide notification to the user with the given title and message.

### Handling clicks on notifications

You may register a custom event along with your NativePHP notification. 
This event will be fired when a user clicks on the notification, so that you may add some custom logic within your application in this scenario.

To attach an event to your notification, you may use the `event` method. The argument passed to this method is the class name of the event that should get dispatched upon clicking on the notification.

```php
Notification::title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->event(\App\Events\MyNotificationEvent::class)
    ->show();
```

### Notification Reference

To keep track of different notifications, each notification gets a reference once created. You can manually set a reference using the `reference()` method. 
By default, a unique ID is generated as the reference. Once the notification is shown, the reference is stored in the notification class.

```
$notification = Notification::title('Hello from NativePHP')->show();
$notification->reference; // <-- This property contains the reference
```

## Configuring Notifications

### Notification Title

You may set the notification's title using the `title()` method.

```php
Notification::title('Hello from NativePHP')
    ->show();
```

### Notification Reference

You can use the `reference()` method to set an event identifier and track which notification triggered a certain event. 
This reference will be sent along with any event triggered by the notification. By default, a unique ID is generated as the reference.

```php
Notification::title('Hello from NativePHP')
    ->reference(Str::uuid())
    ->show();
```

### Notification Message

You may set the notification's message using the `message()` method.

```php
Notification::title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->show();
```

### Notification Reply

On macOS, you can allow the user to reply to a notification using the `hasReply()` method. 

```php
Notification::title('Hello from NativePHP')
    ->hasReply()
    ->show();
```

The `hasReply()` method accepts a placeholder reply message as an argument.

```php
Notification::title('Hello from NativePHP')
    ->hasReply('This is a placeholder')
    ->show();
```

### Notification Actions

On macOS, you can add action buttons to a notification using the `addAction()` method. 

```php
Notification::title('Hello from NativePHP')
    ->addAction('Click here')
    ->show();
```

You can call the `addAction()` method multiple times if you need to add multiple buttons.

```php
Notification::title('Hello from NativePHP')
    ->addAction('Button One')
    ->addAction('Button Two')
    ->show();
```

## Events

### `NotificationClicked`
The `Native\Laravel\Events\Notifications\NotificationClicked` event is dispatched when a user clicks on a notification.

### `NotificationClosed`
The `Native\Laravel\Events\Notifications\NotificationClosed` event is dispatched when a user closes a notification.

### `NotificationReply`
The `Native\Laravel\Events\Notifications\NotificationReply` event is dispatched when a user replies to a notification.

### `NotificationActionClicked`
The `Native\Laravel\Events\Notifications\NotificationActionClicked` event is dispatched when a user clicks an action button on a notification.

The `$index` references to the order of the buttons. The first button added has an index of `0`. 
