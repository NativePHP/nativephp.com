---
title: Notifications
order: 500
---

## Native Notifications

NativePHP supports showing native notifications for each platform. When used sparingly, notifications can be a great
way to inform the user about events that are occurring in your application and to bring their attention back to it,
especially if further input from them is required.

### Showing a Notification

To show a notification, you can use the `Native\Laravel\Notification` class. The notification will show only when the 
`show()` method is called.

```php
use Native\Laravel\Notification;

Notification::new()
    ->title('Hello, from NativePHP!')
    ->show();
```

## Configuring Notifications

### Notification Title

You may set the title of the notification using the `title()` method.

```php
use Native\Laravel\Notification;

Notification::new()
    ->title('NativePHP rocks!')
    ->show();
```

### Notification Body

You may set the body of the notification using the `message()` method.

```php
use Native\Laravel\Notification;

Notification::new()
    ->title('NativePHP rocks!')
    ->message('🔥')
    ->show();
```

## Notification Events

NativePHP provides a simple way to listen for notification events. All events get dispatched as regular Laravel events,
so you may use your `EventServiceProvider` to register listeners.

### Notification Clicked

The `Native\Laravel\Events\Notifications\NotificationClicked` event will be dispatched when the user clicks on a
notification shown by your application. 
